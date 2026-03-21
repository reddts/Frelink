<?php

namespace app\model;

class Insight extends BaseModel
{
    protected static array $allowedDays = [1, 3, 7, 30];
    protected static array $allowedEventTypes = ['impression', 'click', 'detail_view'];
    protected static array $allowedItemTypes = ['question', 'article', 'topic', 'column'];
    protected static array $allowedPublishItemTypes = ['question', 'article'];

    public static function normalizeDays(int $days): int
    {
        return in_array($days, self::$allowedDays, true) ? $days : 7;
    }

    public static function trackEvent(array $data): array
    {
        if (!checkTableExist('analytics_event')) {
            return ['status' => false, 'message' => 'analytics_event table not found'];
        }

        $eventType = trim((string) ($data['event_type'] ?? ''));
        $itemType = trim((string) ($data['item_type'] ?? ''));
        $itemId = intval($data['item_id'] ?? 0);
        if (!in_array($eventType, self::$allowedEventTypes, true)) {
            return ['status' => false, 'message' => 'invalid event_type'];
        }
        if (!in_array($itemType, self::$allowedItemTypes, true) || $itemId <= 0) {
            return ['status' => false, 'message' => 'invalid item'];
        }

        $visitorToken = substr(trim((string) ($data['visitor_token'] ?? '')), 0, 64);
        if ($visitorToken === '') {
            return ['status' => false, 'message' => 'missing visitor_token'];
        }

        $position = intval($data['position'] ?? 0);
        $listKey = substr(trim((string) ($data['list_key'] ?? '')), 0, 100);
        $source = substr(trim((string) ($data['source'] ?? '')), 0, 64);
        $uid = intval($data['uid'] ?? 0);
        $timeBucket = in_array($eventType, ['impression', 'detail_view'], true) ? 600 : 120;
        $dedupeKey = 'analytics_event:' . md5(implode('|', [
            $visitorToken,
            $eventType,
            $itemType,
            $itemId,
            $listKey,
            $source,
            floor(time() / $timeBucket),
        ]));

        if (cache($dedupeKey)) {
            return ['status' => true, 'message' => 'duplicate ignored'];
        }

        cache($dedupeKey, 1, $timeBucket);

        db('analytics_event')->insert([
            'uid' => $uid,
            'visitor_token' => $visitorToken,
            'item_id' => $itemId,
            'item_type' => $itemType,
            'event_type' => $eventType,
            'source' => $source,
            'list_key' => $listKey,
            'position' => $position,
            'referrer' => substr(trim((string) ($data['referrer'] ?? '')), 0, 255),
            'ip' => substr(trim((string) ($data['ip'] ?? '')), 0, 64),
            'user_agent' => substr(trim((string) ($data['user_agent'] ?? '')), 0, 500),
            'create_time' => time(),
            'extra' => json_encode($data['extra'] ?? [], JSON_UNESCAPED_UNICODE),
        ]);

        return ['status' => true, 'message' => 'tracked'];
    }

    public static function getWindowSummary(int $days = 7): array
    {
        $days = self::normalizeDays($days);
        [$start, $end] = self::timeRange($days);
        $searchCount = db('search_log')->whereBetween('create_time', [$start, $end])->count();
        $uniqueKeywords = db('search_log')->whereBetween('create_time', [$start, $end])->distinct(true)->count('keyword');
        $impressions = self::countEvents('impression', $start, $end);
        $clicks = self::countEvents('click', $start, $end);
        $detailViews = self::countEvents('detail_view', $start, $end);

        return [
            'window_days' => $days,
            'from' => date('Y-m-d H:i:s', $start),
            'to' => date('Y-m-d H:i:s', $end),
            'search_count' => $searchCount,
            'unique_keywords' => $uniqueKeywords,
            'impression_count' => $impressions,
            'click_count' => $clicks,
            'detail_view_count' => $detailViews,
            'ctr' => $impressions > 0 ? round($clicks / $impressions, 4) : 0,
            'top_keywords' => self::getTopKeywords($days, 5),
            'top_content' => self::getContentTrends($days, 5),
            'top_topics' => self::getTopicTrends($days, 5),
        ];
    }

    public static function getTopKeywords(int $days = 7, int $limit = 10): array
    {
        $days = self::normalizeDays($days);
        [$start, $end] = self::timeRange($days);
        $rows = db('search_log')
            ->whereBetween('create_time', [$start, $end])
            ->where('keyword', '<>', '')
            ->fieldRaw('keyword, COUNT(*) AS search_count')
            ->group('keyword')
            ->order('search_count', 'desc')
            ->limit(max(1, min(100, $limit)))
            ->select()
            ->toArray();

        foreach ($rows as &$row) {
            $row['keyword'] = trim((string) $row['keyword']);
            $row['search_count'] = intval($row['search_count']);
        }

        return array_values(array_filter($rows, function ($row) {
            return mb_strlen((string) $row['keyword'], 'UTF-8') >= 2;
        }));
    }

    public static function getSearchOpportunities(int $days = 7, int $limit = 10): array
    {
        $keywords = self::getTopKeywords($days, max(10, $limit * 2));
        $result = [];

        foreach ($keywords as $row) {
            $keyword = $row['keyword'];
            $searchCount = intval($row['search_count']);
            if ($searchCount < 2) {
                continue;
            }

            $match = self::countKeywordMatches($keyword);
            $matchedContent = $match['question_count'] + $match['article_count'] + $match['topic_count'] + $match['help_count'];
            $suggestion = '保持观察';

            if ($matchedContent === 0) {
                $suggestion = '高频无内容，优先补问题或帮助文档';
            } elseif ($matchedContent <= 2 && $searchCount >= 3) {
                $suggestion = '内容偏少，建议补教程、专题或术语解释';
            } elseif ($searchCount > $matchedContent * 2) {
                $suggestion = '搜索热度高于供给，建议扩展该主题';
            }

            $result[] = [
                'keyword' => $keyword,
                'search_count' => $searchCount,
                'matched_content_count' => $matchedContent,
                'question_count' => $match['question_count'],
                'article_count' => $match['article_count'],
                'topic_count' => $match['topic_count'],
                'help_count' => $match['help_count'],
                'gap_score' => $matchedContent > 0 ? round($searchCount / $matchedContent, 2) : round($searchCount, 2),
                'suggestion' => $suggestion,
            ];
        }

        usort($result, function ($a, $b) {
            return $b['gap_score'] <=> $a['gap_score'];
        });

        return array_slice($result, 0, $limit);
    }

    public static function getContentTrends(int $days = 7, int $limit = 10, string $itemType = ''): array
    {
        $days = self::normalizeDays($days);
        [$start, $end, $prevStart, $prevEnd] = self::compareRange($days);
        $recentStats = self::getEventStats($start, $end, $itemType);
        $prevStats = self::getEventStats($prevStart, $prevEnd, $itemType);
        $browseRecent = self::getBrowseStats($start, $end, $itemType);
        $browsePrev = self::getBrowseStats($prevStart, $prevEnd, $itemType);

        $keys = array_unique(array_merge(array_keys($recentStats), array_keys($browseRecent)));
        $rows = [];
        foreach ($keys as $key) {
            [$type, $id] = explode(':', $key);
            $recent = $recentStats[$key] ?? ['impressions' => 0, 'clicks' => 0, 'detail_views' => 0];
            $prev = $prevStats[$key] ?? ['impressions' => 0, 'clicks' => 0, 'detail_views' => 0];
            $recent['detail_views'] += intval($browseRecent[$key] ?? 0);
            $prev['detail_views'] += intval($browsePrev[$key] ?? 0);
            $rows[] = [
                'item_type' => $type,
                'item_id' => intval($id),
                'impressions' => intval($recent['impressions']),
                'clicks' => intval($recent['clicks']),
                'detail_views' => intval($recent['detail_views']),
                'ctr' => intval($recent['impressions']) > 0 ? round($recent['clicks'] / $recent['impressions'], 4) : 0,
                'previous_detail_views' => intval($prev['detail_views']),
                'trend_ratio' => intval($prev['detail_views']) > 0
                    ? round($recent['detail_views'] / max(1, $prev['detail_views']), 2)
                    : ($recent['detail_views'] > 0 ? round($recent['detail_views'], 2) : 0),
            ];
        }

        usort($rows, function ($a, $b) {
            if ($a['detail_views'] === $b['detail_views']) {
                return $b['ctr'] <=> $a['ctr'];
            }
            return $b['detail_views'] <=> $a['detail_views'];
        });

        $rows = array_slice($rows, 0, max(1, min(100, $limit)));
        return self::hydrateContentRows($rows);
    }

    public static function getTopicTrends(int $days = 7, int $limit = 10): array
    {
        $contentRows = self::getContentTrends($days, 100);
        if (!$contentRows) {
            return [];
        }

        $topicMap = [];
        foreach ($contentRows as $row) {
            $relations = db('topic_relation')
                ->where([
                    'item_type' => $row['item_type'],
                    'item_id' => $row['item_id'],
                    'status' => 1,
                ])
                ->column('topic_id');

            foreach ($relations as $topicId) {
                $topicId = intval($topicId);
                if (!isset($topicMap[$topicId])) {
                    $topicMap[$topicId] = [
                        'topic_id' => $topicId,
                        'impressions' => 0,
                        'clicks' => 0,
                        'detail_views' => 0,
                        'content_count' => 0,
                    ];
                }
                $topicMap[$topicId]['impressions'] += intval($row['impressions']);
                $topicMap[$topicId]['clicks'] += intval($row['clicks']);
                $topicMap[$topicId]['detail_views'] += intval($row['detail_views']);
                $topicMap[$topicId]['content_count']++;
            }
        }

        foreach ($topicMap as &$row) {
            $row['ctr'] = $row['impressions'] > 0 ? round($row['clicks'] / $row['impressions'], 4) : 0;
            $info = db('topic')->where(['id' => $row['topic_id'], 'status' => 1])->field('title,description')->find();
            $row['title'] = $info['title'] ?? '';
            $row['description'] = isset($info['description']) ? str_cut(strip_tags(htmlspecialchars_decode((string) $info['description'])), 0, 120) : '';
        }

        $rows = array_values(array_filter($topicMap, function ($row) {
            return $row['title'] !== '';
        }));

        usort($rows, function ($a, $b) {
            return $b['detail_views'] <=> $a['detail_views'];
        });

        return array_slice($rows, 0, $limit);
    }

    public static function getRecommendations(int $days = 7, int $limit = 10): array
    {
        $keywords = self::getSearchOpportunities($days, $limit);
        $content = self::getContentTrends($days, $limit);
        $topics = self::getTopicTrends($days, $limit);
        $actions = [];

        foreach ($keywords as $item) {
            if ($item['matched_content_count'] === 0) {
                $actions[] = [
                    'type' => 'content_gap',
                    'priority' => 'high',
                    'title' => '补充新内容：' . $item['keyword'],
                    'reason' => '最近窗口内该词搜索次数高，但站内没有匹配内容',
                    'suggestion' => '优先新增问题或帮助文档，再补文章与专题',
                    'window_days' => self::normalizeDays($days),
                ];
            } elseif ($item['gap_score'] >= 3) {
                $actions[] = [
                    'type' => 'expand_topic',
                    'priority' => 'medium',
                    'title' => '扩展主题：' . $item['keyword'],
                    'reason' => '最近搜索热度明显高于内容供给',
                    'suggestion' => '补术语解释、FAQ、教程和关联主题',
                    'window_days' => self::normalizeDays($days),
                ];
            }
        }

        foreach ($content as $item) {
            if ($item['ctr'] >= 0.2 && $item['impressions'] > 0 && $item['impressions'] <= 20) {
                $actions[] = [
                    'type' => 'boost_exposure',
                    'priority' => 'medium',
                    'title' => '提高曝光：' . $item['title'],
                    'reason' => '点击率较高，但最近窗口曝光不足',
                    'suggestion' => '增加内链、挂到主题页、加入帮助或专题推荐',
                    'window_days' => self::normalizeDays($days),
                ];
            }
        }

        foreach ($topics as $item) {
            if ($item['detail_views'] >= 5 && $item['content_count'] <= 2) {
                $actions[] = [
                    'type' => 'topic_expand',
                    'priority' => 'medium',
                    'title' => '扩充主题内容：' . $item['title'],
                    'reason' => '主题访问增长明显，但挂载内容偏少',
                    'suggestion' => '围绕该主题继续补问题、文章和专题',
                    'window_days' => self::normalizeDays($days),
                ];
            }
        }

        return array_slice($actions, 0, $limit);
    }

    public static function getPublishAssist(string $itemType = 'question', int $days = 7, int $limit = 6): array
    {
        $days = self::normalizeDays($days);
        $limit = max(1, min(20, $limit));
        $itemType = in_array($itemType, self::$allowedPublishItemTypes, true) ? $itemType : 'question';
        $keywordLimit = max(5, $limit);

        $keywords = self::getTopKeywords($days, $keywordLimit);
        $opportunities = self::getSearchOpportunities($days, $keywordLimit);
        $topics = self::getTopicTrends($days, $keywordLimit);
        $content = self::getContentTrends($days, $keywordLimit, $itemType);

        $titleIdeas = [];
        foreach ($opportunities as $item) {
            $keyword = trim((string)($item['keyword'] ?? ''));
            if (!$keyword) {
                continue;
            }

            $recommendedType = $itemType === 'article'
                ? self::recommendArticleType($keyword, $item)
                : null;

            $titleIdeas[] = [
                'keyword' => $keyword,
                'title' => $itemType === 'question'
                    ? $keyword . ' 应该怎么处理？'
                    : $keyword . '：完整说明与实操指南',
                'recommended_type' => $recommendedType['type'] ?? '',
                'recommended_type_label' => $recommendedType['label'] ?? '',
                'reason' => $item['suggestion'] ?? '',
                'search_count' => intval($item['search_count'] ?? 0),
                'matched_content_count' => intval($item['matched_content_count'] ?? 0),
            ];

            if (count($titleIdeas) >= $limit) {
                break;
            }
        }

        $guidance = [
            $itemType === 'question'
                ? '优先把高频搜索词写成可直接命中的具体问题标题。'
                : '优先把高频搜索词整理成教程、帮助文档或专题文章。',
            '判断依据只看最近窗口，不参考长期累计点击率。',
            '先挂到相关主题，再补内链和下一步阅读，避免内容成为孤岛。',
        ];

        $typeIdeas = $itemType === 'article'
            ? self::buildArticleTypeIdeas($opportunities, $limit)
            : [];

        return [
            'item_type' => $itemType,
            'window_days' => $days,
            'top_keywords' => array_slice($keywords, 0, $limit),
            'opportunities' => array_slice($opportunities, 0, $limit),
            'suggested_topics' => array_slice($topics, 0, $limit),
            'trending_content' => array_slice($content, 0, $limit),
            'title_ideas' => $titleIdeas,
            'type_ideas' => $typeIdeas,
            'default_article_type' => $typeIdeas[0]['type'] ?? 'research',
            'guidance' => $guidance,
        ];
    }

    protected static function buildArticleTypeIdeas(array $opportunities, int $limit): array
    {
        $ideas = [];

        foreach ($opportunities as $item) {
            $keyword = trim((string) ($item['keyword'] ?? ''));
            if ($keyword === '') {
                continue;
            }

            $recommended = self::recommendArticleType($keyword, $item);
            $type = $recommended['type'];
            if (isset($ideas[$type])) {
                if (intval($item['search_count'] ?? 0) > intval($ideas[$type]['search_count'] ?? 0)) {
                    $ideas[$type] = [
                        'type' => $type,
                        'label' => $recommended['label'],
                        'keyword' => $keyword,
                        'search_count' => intval($item['search_count'] ?? 0),
                        'reason' => $recommended['reason'],
                        'title' => $recommended['title'],
                    ];
                }
                continue;
            }

            $ideas[$type] = [
                'type' => $type,
                'label' => $recommended['label'],
                'keyword' => $keyword,
                'search_count' => intval($item['search_count'] ?? 0),
                'reason' => $recommended['reason'],
                'title' => $recommended['title'],
            ];
        }

        usort($ideas, function ($a, $b) {
            return intval($b['search_count']) <=> intval($a['search_count']);
        });

        return array_slice(array_values($ideas), 0, max(1, min(5, $limit)));
    }

    protected static function recommendArticleType(string $keyword, array $opportunity): array
    {
        $text = mb_strtolower($keyword, 'UTF-8');
        $suggestion = mb_strtolower((string) ($opportunity['suggestion'] ?? ''), 'UTF-8');
        $type = 'research';
        $reason = '适合先整理背景、分歧与阶段性结论。';

        if (preg_match('/(如何|怎么|教程|步骤|指南|配置|安装|部署|报错|错误|排查|解决|命令|区别|清单|实操)/u', $text)) {
            $type = 'tutorial';
            $reason = '搜索意图偏实操，优先沉淀成步骤清晰的方法内容。';
        } elseif (preg_match('/(是什么|定义|术语|规则|说明|限制|条件|费用|价格|时间|入口|地址)/u', $text) || mb_strpos($suggestion, '帮助文档') !== false || mb_strpos($suggestion, '术语解释') !== false) {
            $type = 'faq';
            $reason = '搜索意图偏明确答案，适合整理成帮助或 FAQ 型内容。';
        } elseif (preg_match('/(观察|记录|碎片|随想|笔记|实验|现象|现场|判断)/u', $text)) {
            $type = 'fragment';
            $reason = '更适合保留判断、线索和变化中的观察。';
        } elseif (preg_match('/(热点|更新|发布|新规|事件|案例|影响|变化|为什么重要)/u', $text)) {
            $type = 'normal';
            $reason = '更适合作为热点解释，回答这件事为什么值得关注。';
        }

        return [
            'type' => $type,
            'label' => frelink_article_type_label($type),
            'reason' => $reason,
            'title' => $thisTitle = match ($type) {
                'tutorial' => $keyword . '：步骤、配置与避坑',
                'faq' => $keyword . '：定义、规则与直接答案',
                'fragment' => $keyword . '：观察记录与判断笔记',
                'normal' => $keyword . '：发生了什么，为什么重要',
                default => $keyword . '：背景、分歧与阶段结论',
            },
        ];
    }

    protected static function countKeywordMatches(string $keyword): array
    {
        $like = '%' . addcslashes($keyword, '%_') . '%';
        return [
            'question_count' => db('question')->where(['status' => 1])->whereLike('title', $like)->count(),
            'article_count' => db('article')->where(['status' => 1])->whereLike('title', $like)->count(),
            'topic_count' => db('topic')->where(['status' => 1])->whereLike('title', $like)->count(),
            'help_count' => checkTableExist('help_chapter')
                ? db('help_chapter')->where(['status' => 1])->whereLike('title', $like)->count()
                : 0,
        ];
    }

    protected static function hydrateContentRows(array $rows): array
    {
        foreach ($rows as &$row) {
            $info = self::findContentInfo($row['item_type'], $row['item_id']);
            if (!$info) {
                $row['title'] = '';
                continue;
            }
            $row['title'] = $info['title'];
            $row['summary'] = $info['summary'];
            $row['url'] = $info['url'];
        }

        return array_values(array_filter($rows, function ($row) {
            return !empty($row['title']);
        }));
    }

    protected static function findContentInfo(string $itemType, int $itemId): array
    {
        switch ($itemType) {
            case 'question':
                $info = db('question')->where(['id' => $itemId, 'status' => 1])->field('title,detail')->find();
                if (!$info) {
                    return [];
                }
                return [
                    'title' => $info['title'],
                    'summary' => str_cut(strip_tags(htmlspecialchars_decode((string) $info['detail'])), 0, 120),
                    'url' => (string) url('question/detail', ['id' => $itemId]),
                ];
            case 'article':
                $info = db('article')->where(['id' => $itemId, 'status' => 1])->field('title,message')->find();
                if (!$info) {
                    return [];
                }
                return [
                    'title' => $info['title'],
                    'summary' => str_cut(strip_tags(htmlspecialchars_decode((string) $info['message'])), 0, 120),
                    'url' => (string) url('article/detail', ['id' => $itemId]),
                ];
            case 'topic':
                $info = db('topic')->where(['id' => $itemId, 'status' => 1])->field('title,description')->find();
                if (!$info) {
                    return [];
                }
                return [
                    'title' => $info['title'],
                    'summary' => str_cut(strip_tags(htmlspecialchars_decode((string) $info['description'])), 0, 120),
                    'url' => (string) url('topic/detail', ['id' => $itemId]),
                ];
            case 'column':
                $info = db('column')->where(['id' => $itemId, 'verify' => 1])->field('name,description')->find();
                if (!$info) {
                    return [];
                }
                return [
                    'title' => $info['name'],
                    'summary' => str_cut(strip_tags(htmlspecialchars_decode((string) $info['description'])), 0, 120),
                    'url' => (string) url('column/detail', ['id' => $itemId]),
                ];
            default:
                return [];
        }
    }

    protected static function getEventStats(int $start, int $end, string $itemType = ''): array
    {
        if (!checkTableExist('analytics_event')) {
            return [];
        }

        $query = db('analytics_event')
            ->whereBetween('create_time', [$start, $end])
            ->fieldRaw("
                item_type,
                item_id,
                SUM(CASE WHEN event_type = 'impression' THEN 1 ELSE 0 END) AS impressions,
                SUM(CASE WHEN event_type = 'click' THEN 1 ELSE 0 END) AS clicks,
                SUM(CASE WHEN event_type = 'detail_view' THEN 1 ELSE 0 END) AS detail_views
            ")
            ->group('item_type,item_id');

        if ($itemType !== '') {
            $query->where(['item_type' => $itemType]);
        }

        $rows = $query->select()->toArray();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['item_type'] . ':' . $row['item_id']] = [
                'impressions' => intval($row['impressions']),
                'clicks' => intval($row['clicks']),
                'detail_views' => intval($row['detail_views']),
            ];
        }
        return $result;
    }

    protected static function getBrowseStats(int $start, int $end, string $itemType = ''): array
    {
        if (!checkTableExist('browse_records')) {
            return [];
        }

        $query = db('browse_records')
            ->whereBetween('create_time', [$start, $end])
            ->where(['status' => 1])
            ->fieldRaw('item_type,item_id, COUNT(*) AS detail_views')
            ->group('item_type,item_id');

        if ($itemType !== '') {
            $query->where(['item_type' => $itemType]);
        }

        $rows = $query->select()->toArray();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['item_type'] . ':' . $row['item_id']] = intval($row['detail_views']);
        }
        return $result;
    }

    protected static function countEvents(string $eventType, int $start, int $end): int
    {
        if (!checkTableExist('analytics_event')) {
            return 0;
        }

        return db('analytics_event')
            ->where(['event_type' => $eventType])
            ->whereBetween('create_time', [$start, $end])
            ->count();
    }

    protected static function timeRange(int $days): array
    {
        $end = time();
        $start = $end - ($days * 86400);
        return [$start, $end];
    }

    protected static function compareRange(int $days): array
    {
        $end = time();
        $start = $end - ($days * 86400);
        $prevEnd = $start;
        $prevStart = $prevEnd - ($days * 86400);
        return [$start, $end, $prevStart, $prevEnd];
    }
}
