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
            $keyword = trim((string) ($row['keyword'] ?? ''));
            if (mb_strlen($keyword, 'UTF-8') < 2) {
                return false;
            }

            return self::isMeaningfulSearchKeyword($keyword);
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
        $fragmentIdeas = $itemType === 'article' ? self::getFragmentPromotionIdeas($days, $limit) : [];

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
            $itemType === 'article'
                ? '把最近被持续阅读的观察，优先整理成综述、帮助或主题追踪。'
                : '判断依据只看最近窗口，不参考长期累计点击率。',
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
            'fragment_ideas' => array_slice($fragmentIdeas, 0, $limit),
            'default_article_type' => $typeIdeas[0]['type'] ?? 'research',
            'guidance' => $guidance,
        ];
    }

    public static function getColdStartSummary(): array
    {
        $targets = [
            'research' => ['label' => '综述', 'target' => 12],
            'fragment' => ['label' => '观察', 'target' => 20],
            'track' => ['label' => '主题追踪', 'target' => 8],
            'faq' => ['label' => 'FAQ', 'target' => 30],
            'help' => ['label' => '帮助', 'target' => 12],
            'chapter' => ['label' => '知识章节', 'target' => 8],
        ];

        $counts = [
            'research' => db('article')->where(['status' => 1, 'article_type' => 'research'])->count(),
            'fragment' => db('article')->where(['status' => 1, 'article_type' => 'fragment'])->count(),
            'track' => db('article')->where(['status' => 1, 'article_type' => 'track'])->count(),
            'faq' => db('question')->where(['status' => 1])->count(),
            'help' => db('article')->where(['status' => 1])->whereIn('article_type', ['tutorial', 'faq'])->count(),
            'chapter' => db('help_chapter')->where(['status' => 1])->count(),
        ];

        $items = [];
        $completedTargets = 0;
        foreach ($targets as $key => $meta) {
            $current = intval($counts[$key] ?? 0);
            $target = intval($meta['target']);
            $progress = $target > 0 ? min(100, round(($current / $target) * 100)) : 0;
            $gap = max(0, $target - $current);
            if ($current >= $target) {
                $completedTargets++;
            }
            $items[$key] = [
                'key' => $key,
                'label' => $meta['label'],
                'current' => $current,
                'target' => $target,
                'gap' => $gap,
                'progress' => $progress,
                'status' => $current >= $target ? '已达标' : ($progress >= 60 ? '接近达标' : '待补足'),
            ];
        }

        $recommendations = [];
        foreach ($items as $key => $item) {
            if ($item['gap'] <= 0) {
                continue;
            }
            $action = '';
            switch ($key) {
                case 'research':
                    $action = '优先补 1-2 篇研究综述，建立可被搜索和引用的核心判断内容。';
                    break;
                case 'fragment':
                    $action = '优先补一批观察记录，保证主题页和首页有持续更新的轻内容。';
                    break;
                case 'track':
                    $action = '优先补主题追踪，把阶段变化、修正和下一步观察点沉淀成连续记录。';
                    break;
                case 'faq':
                    $action = '优先从高频问题里补 FAQ，先覆盖检索入口，再慢慢升级为综述或帮助。';
                    break;
                case 'help':
                    $action = '优先补帮助型内容，把规则、术语和方法沉淀成稳定知识资产。';
                    break;
                case 'chapter':
                    $action = '优先补知识章节，把已有内容归档成可长期维护的知识地图结构。';
                    break;
            }
            $recommendations[] = [
                'key' => $key,
                'label' => $item['label'],
                'gap' => $item['gap'],
                'progress' => $item['progress'],
                'status' => $item['status'],
                'action' => $action,
            ];
        }

        usort($recommendations, function ($a, $b) {
            if (intval($a['gap']) === intval($b['gap'])) {
                return intval($a['progress']) <=> intval($b['progress']);
            }
            return intval($b['gap']) <=> intval($a['gap']);
        });

        return [
            'overall_progress' => round(($completedTargets / count($targets)) * 100),
            'completed_targets' => $completedTargets,
            'target_total' => count($targets),
            'items' => $items,
            'recommendations' => array_slice($recommendations, 0, 3),
        ];
    }

    public static function getWeeklyExecutionPlan(int $days = 7, int $limit = 3): array
    {
        $days = self::normalizeDays($days);
        $limit = max(1, min(10, $limit));
        $windowEndTs = time();
        $windowStartTs = strtotime('-' . max(0, $days - 1) . ' days 00:00:00');
        $expiresAtTs = strtotime('+1 day 00:00:00');
        $coldStart = self::getColdStartSummary();
        $articleAssist = self::getPublishAssist('article', $days, 8);
        $questionAssist = self::getPublishAssist('question', $days, 8);

        $typeIdeas = [];
        foreach (($articleAssist['type_ideas'] ?? []) as $item) {
            $typeIdeas[$item['type']] = $item;
        }
        $questionIdeas = $questionAssist['title_ideas'] ?? [];
        $searchIdeas = array_slice($articleAssist['title_ideas'] ?? [], 0, min(2, $limit));
        $fragmentIdeas = self::getFragmentPromotionIdeas($days, $limit);

        $tasksByKeyword = [];
        foreach ($searchIdeas as $idea) {
            $keyword = trim((string) ($idea['keyword'] ?? ''));
            $title = trim((string) ($idea['title'] ?? ''));
            if ($keyword === '' || $title === '') {
                continue;
            }

            $contentType = trim((string) ($idea['recommended_type'] ?? '')) ?: 'research';
            $task = [
                'task_type' => 'search_topic',
                'status' => 'pending',
                'suggested_owner' => self::resolveSuggestedOwner($contentType, 'search'),
                'window_start' => date('Y-m-d H:i:s', $windowStartTs),
                'window_end' => date('Y-m-d H:i:s', $windowEndTs),
                'expires_at' => date('Y-m-d H:i:s', $expiresAtTs),
                'content_type' => $contentType,
                'source_key' => 'search',
                'priority' => self::resolveSearchExecutionPriority($idea),
                'label' => '搜索选题',
                'title' => $title,
                'keyword' => $keyword,
                'reason' => (string) ($idea['reason'] ?? '围绕高频搜索词补充系统性内容'),
                'primary_label' => '去写内容',
                'primary_url' => get_url('article/publish', ['article_type' => $contentType]),
                'secondary_label' => '查看现有内容',
                'secondary_url' => get_url('article/index', ['type' => $contentType]),
            ];
            self::mergeWeeklyTask($tasksByKeyword, $task, $days);
        }

        foreach ($fragmentIdeas as $idea) {
            $keyword = trim((string) ($idea['keyword'] ?? ''));
            $title = trim((string) ($idea['title'] ?? ''));
            if ($keyword === '' || $title === '') {
                continue;
            }

            $contentType = trim((string) ($idea['recommended_type'] ?? '')) ?: 'research';
            $task = [
                'task_type' => 'promote_fragment',
                'status' => 'pending',
                'suggested_owner' => self::resolveSuggestedOwner($contentType, 'fragment'),
                'window_start' => date('Y-m-d H:i:s', $windowStartTs),
                'window_end' => date('Y-m-d H:i:s', $windowEndTs),
                'expires_at' => date('Y-m-d H:i:s', $expiresAtTs),
                'content_type' => $contentType,
                'source_key' => 'fragment',
                'priority' => self::resolveFragmentExecutionPriority($idea),
                'label' => '整理沉淀',
                'title' => $title,
                'keyword' => $keyword,
                'reason' => (string) ($idea['reason'] ?? '这条观察已有持续阅读，适合整理为更稳定的知识内容。'),
                'primary_label' => '去写内容',
                'primary_url' => get_url('article/publish', ['article_type' => $contentType]),
                'secondary_label' => '查看原观察',
                'secondary_url' => (string) ($idea['url'] ?? get_url('article/detail', ['id' => intval($idea['article_id'] ?? 0)])),
            ];
            self::mergeWeeklyTask($tasksByKeyword, $task, $days);
        }

        foreach (($coldStart['recommendations'] ?? []) as $recommendation) {
            $key = $recommendation['key'] ?? '';
            if ($key === 'chapter') {
                continue;
            }

            if ($key === 'faq') {
                $idea = $questionIdeas[0] ?? null;
                if (!$idea) {
                    continue;
                }
                $task = [
                    'task_type' => 'fill_gap',
                    'status' => 'pending',
                    'suggested_owner' => self::resolveSuggestedOwner('faq', $key),
                    'window_start' => date('Y-m-d H:i:s', $windowStartTs),
                    'window_end' => date('Y-m-d H:i:s', $windowEndTs),
                    'expires_at' => date('Y-m-d H:i:s', $expiresAtTs),
                    'content_type' => 'faq',
                    'source_key' => $key,
                    'priority' => self::resolveExecutionPriority($recommendation),
                    'label' => 'FAQ',
                    'title' => $idea['title'],
                    'keyword' => $idea['keyword'] ?? '',
                    'reason' => ($idea['reason'] ?? '') ?: ($recommendation['action'] ?? ''),
                    'primary_label' => '去补 FAQ',
                    'primary_url' => get_url('question/publish'),
                    'secondary_label' => '查看 FAQ 列表',
                    'secondary_url' => get_url('question/index'),
                ];
                $task['dedupe_key'] = self::buildExecutionDedupeKey($task);
                $task['task_id'] = self::buildExecutionTaskId($days, $task);
                $tasks[] = $task;
                continue;
            }

            $preferredTypes = match ($key) {
                'research' => ['research', 'normal'],
                'fragment' => ['fragment'],
                'help' => ['faq', 'tutorial'],
                default => [],
            };

            $idea = null;
            foreach ($preferredTypes as $type) {
                if (!empty($typeIdeas[$type])) {
                    $idea = $typeIdeas[$type];
                    break;
                }
            }
            if (!$idea) {
                continue;
            }

            $task = [
                'task_type' => 'fill_gap',
                'status' => 'pending',
                'suggested_owner' => self::resolveSuggestedOwner($idea['type'] ?? 'research', $key),
                'window_start' => date('Y-m-d H:i:s', $windowStartTs),
                'window_end' => date('Y-m-d H:i:s', $windowEndTs),
                'expires_at' => date('Y-m-d H:i:s', $expiresAtTs),
                'content_type' => $idea['type'] ?? 'research',
                'source_key' => $key,
                'priority' => self::resolveExecutionPriority($recommendation),
                'label' => $idea['label'] ?? ($recommendation['label'] ?? ''),
                'title' => $idea['title'] ?? '',
                'keyword' => $idea['keyword'] ?? '',
                'reason' => ($idea['reason'] ?? '') ?: ($recommendation['action'] ?? ''),
                'primary_label' => '去写内容',
                'primary_url' => get_url('article/publish', ['article_type' => $idea['type'] ?? 'research']),
                'secondary_label' => '查看现有内容',
                'secondary_url' => get_url('article/index', ['type' => $idea['type'] ?? 'research']),
            ];
            self::mergeWeeklyTask($tasksByKeyword, $task, $days);
        }

        $unique = array_values($tasksByKeyword);
        usort($unique, function ($a, $b) {
            $priorityOrder = ['high' => 3, 'medium' => 2, 'low' => 1];
            $aPriority = $priorityOrder[strtolower((string)($a['priority'] ?? 'low'))] ?? 1;
            $bPriority = $priorityOrder[strtolower((string)($b['priority'] ?? 'low'))] ?? 1;
            if ($aPriority !== $bPriority) {
                return $bPriority <=> $aPriority;
            }

            $aSearchFirst = ($a['task_type'] ?? '') === 'search_topic' ? 1 : 0;
            $bSearchFirst = ($b['task_type'] ?? '') === 'search_topic' ? 1 : 0;
            if ($aSearchFirst !== $bSearchFirst) {
                return $bSearchFirst <=> $aSearchFirst;
            }

            return strcmp((string)($a['title'] ?? ''), (string)($b['title'] ?? ''));
        });

        $unique = array_slice($unique, 0, $limit);

        return [
            'window_days' => $days,
            'generated_at' => date('Y-m-d H:i:s'),
            'window_start' => date('Y-m-d H:i:s', $windowStartTs),
            'window_end' => date('Y-m-d H:i:s', $windowEndTs),
            'expires_at' => date('Y-m-d H:i:s', $expiresAtTs),
            'schema_version' => '2026-03-21.3',
            'cold_start' => $coldStart,
            'tasks' => $unique,
        ];
    }

    public static function renderWeeklyExecutionBrief(array $plan): string
    {
        $days = intval($plan['window_days'] ?? 7);
        $coldStart = $plan['cold_start'] ?? [];
        $tasks = $plan['tasks'] ?? [];

        $lines = [];
        $lines[] = 'Frelink 本周执行清单';
        $lines[] = '统计窗口：最近 ' . $days . ' 天';
        $lines[] = '生成时间：' . ($plan['generated_at'] ?? date('Y-m-d H:i:s'));
        $lines[] = '窗口范围：' . (($plan['window_start'] ?? '-') . ' ~ ' . ($plan['window_end'] ?? '-'));
        $lines[] = '失效时间：' . ($plan['expires_at'] ?? '-');
        $lines[] = '冷启动进度：' . intval($coldStart['overall_progress'] ?? 0) . '%'
            . '，已达标 ' . intval($coldStart['completed_targets'] ?? 0)
            . '/' . intval($coldStart['target_total'] ?? 0);

        if (!empty($tasks)) {
            $lines[] = '';
            $lines[] = '本周建议优先补这三项：';
            foreach ($tasks as $index => $item) {
                $lines[] = ($index + 1) . '. [' . ($item['label'] ?? '-') . '/' . strtoupper((string)($item['priority'] ?? 'normal')) . '] ' . ($item['title'] ?? '-');
                $lines[] = '   关键词：' . (($item['keyword'] ?? '') ?: '-');
                $lines[] = '   原因：' . (($item['reason'] ?? '') ?: '-');
                $lines[] = '   类型：' . (($item['task_type'] ?? '-') . ' / ' . ($item['content_type'] ?? '-'));
                $lines[] = '   协作：' . (($item['status'] ?? '-') . ' / ' . ($item['suggested_owner'] ?? '-'));
                $lines[] = '   时间：' . (($item['window_start'] ?? '-') . ' ~ ' . ($item['window_end'] ?? '-') . ' / 失效 ' . (($item['expires_at'] ?? '-') ?: '-'));
                $lines[] = '   标识：' . (($item['task_id'] ?? '-') . ' / ' . ($item['dedupe_key'] ?? '-'));
                $lines[] = '   动作：' . (($item['primary_label'] ?? '-') . ' / ' . ($item['secondary_label'] ?? '-'));
            }
        } else {
            $lines[] = '';
            $lines[] = '当前还没有形成明确的本周执行清单。';
        }

        return implode(PHP_EOL, $lines);
    }

    public static function getWritingWorkflow(string $mode = 'all', int $days = 7, int $limit = 3, string $topic = '', string $itemType = 'article'): array
    {
        $days = self::normalizeDays($days);
        $limit = max(1, min(10, $limit));
        $mode = strtolower(trim($mode));
        if (!in_array($mode, ['all', 'auto', 'manual'], true)) {
            $mode = 'all';
        }

        $itemType = in_array($itemType, self::$allowedPublishItemTypes, true) ? $itemType : 'article';
        $topic = trim($topic);
        $workflowWindow = self::getWeeklyExecutionPlan($days, max(3, $limit));
        $publishAssist = self::getPublishAssist($itemType, $days, max(5, $limit));
        $manualSeed = $topic !== ''
            ? $topic
            : trim((string) ($publishAssist['title_ideas'][0]['keyword'] ?? ''));
        $manualRecommended = self::recommendArticleType($manualSeed ?: 'Frelink 内容', ['suggestion' => '手动指令发文']);

        $autoCandidates = [];
        foreach (array_slice($workflowWindow['tasks'] ?? [], 0, $limit) as $task) {
            $autoCandidates[] = [
                'task_id' => $task['task_id'] ?? '',
                'title' => $task['title'] ?? '',
                'keyword' => $task['keyword'] ?? '',
                'content_type' => $task['content_type'] ?? 'research',
                'content_type_label' => frelink_article_type_label($task['content_type'] ?? 'research'),
                'priority' => $task['priority'] ?? 'low',
                'reason' => $task['reason'] ?? '',
                'source_key' => $task['source_key'] ?? '',
                'task_type' => $task['task_type'] ?? '',
                'review_required' => true,
                'publish_allowed' => false,
                'draft_stage' => 'draft -> review -> publish',
                'primary_label' => $task['primary_label'] ?? '去写内容',
                'primary_url' => $task['primary_url'] ?? '',
                'secondary_label' => $task['secondary_label'] ?? '查看现有内容',
                'secondary_url' => $task['secondary_url'] ?? '',
            ];
        }

        $manualTitleIdeas = [];
        foreach (array_slice($publishAssist['title_ideas'] ?? [], 0, $limit) as $idea) {
            $manualTitleIdeas[] = [
                'keyword' => $idea['keyword'] ?? '',
                'title' => $idea['title'] ?? '',
                'recommended_type' => $idea['recommended_type'] ?? '',
                'recommended_type_label' => $idea['recommended_type_label'] ?? '',
                'reason' => $idea['reason'] ?? '',
            ];
        }

        $manualOutline = self::buildWorkflowOutline(
            $manualRecommended['type'] ?? 'research',
            $topic,
            $manualSeed
        );

        $reviewPolicy = [
            'required' => true,
            'reviewer' => 'human',
            'auto_publish_allowed' => false,
            'manual_publish_allowed' => false,
            'focus' => [
                'title matches body',
                'no automation traces',
                'logic is self-consistent',
                'boundaries and citations are explicit',
            ],
        ];

        return [
            'workflow_mode' => $mode,
            'window_days' => $days,
            'generated_at' => date('Y-m-d H:i:s'),
            'review_policy' => $reviewPolicy,
            'auto_flow' => [
                'daily_target' => 2,
                'active' => in_array($mode, ['all', 'auto'], true),
                'source' => 'hot_search_and_content_gap',
                'candidates' => in_array($mode, ['all', 'auto'], true) ? $autoCandidates : [],
            ],
            'manual_flow' => [
                'active' => in_array($mode, ['all', 'manual'], true),
                'topic' => $topic,
                'item_type' => $itemType,
                'recommended_type' => $manualRecommended['type'] ?? 'research',
                'recommended_type_label' => $manualRecommended['label'] ?? frelink_article_type_label('research'),
                'prompt_template' => self::buildManualPromptTemplate($topic, $itemType, $manualRecommended),
                'outline_template' => $manualOutline,
                'title_ideas' => $manualTitleIdeas,
                'review_notes' => [
                    'confirm the viewpoint and target audience before drafting',
                    'keep the article free of generic AI phrasing',
                    'revise until the logic chain reads like a human-written note',
                ],
            ],
            'routes' => [
                'publish_page' => get_url('article/publish', ['article_type' => $manualRecommended['type'] ?? 'research']),
                'weekly_execution_api' => '/api/Insight/weekly_execution?days=' . $days . '&limit=' . $limit,
                'publish_assist_api' => '/api/Insight/publish_assist?days=' . $days . '&item_type=' . $itemType . '&limit=' . $limit,
            ],
        ];
    }

    public static function renderWritingWorkflowBrief(array $workflow): string
    {
        $lines = [];
        $lines[] = 'Frelink 发文工作流';
        $lines[] = '工作模式：' . ($workflow['workflow_mode'] ?? 'all');
        $lines[] = '统计窗口：最近 ' . intval($workflow['window_days'] ?? 7) . ' 天';
        $lines[] = '生成时间：' . ($workflow['generated_at'] ?? date('Y-m-d H:i:s'));

        $reviewPolicy = $workflow['review_policy'] ?? [];
        $lines[] = '审核要求：' . (!empty($reviewPolicy['required']) ? '必须人工审核' : '未配置');
        $lines[] = '审核闸门：' . implode('、', $reviewPolicy['focus'] ?? ['title matches body', 'no automation traces', 'logic is self-consistent']);

        $autoFlow = $workflow['auto_flow'] ?? [];
        $lines[] = '';
        $lines[] = '自动筛选发文：';
        $lines[] = ' - 每日目标：' . intval($autoFlow['daily_target'] ?? 2) . ' 篇';
        $lines[] = ' - 发布前状态：仅草稿，必须人工确认';
        foreach (array_slice($autoFlow['candidates'] ?? [], 0, 3) as $item) {
            $lines[] = ' - ' . ($item['title'] ?? '-') . ' [' . ($item['content_type_label'] ?? ($item['content_type'] ?? '-')) . ']';
            $lines[] = '   关键词：' . (($item['keyword'] ?? '') ?: '-');
            $lines[] = '   原因：' . (($item['reason'] ?? '') ?: '-');
        }

        $manualFlow = $workflow['manual_flow'] ?? [];
        $lines[] = '';
        $lines[] = '手动指令发文：';
        $lines[] = ' - 主题：' . (($manualFlow['topic'] ?? '') ?: '-');
        $lines[] = ' - 推荐形态：' . (($manualFlow['recommended_type_label'] ?? '-') ?: '-');
        if (!empty($manualFlow['prompt_template']['instruction'])) {
            $lines[] = ' - 指令：' . $manualFlow['prompt_template']['instruction'];
        }
        if (!empty($manualFlow['outline_template'])) {
            foreach ($manualFlow['outline_template'] as $item) {
                $lines[] = ' - ' . ($item['title'] ?? '-') . '：' . ($item['description'] ?? '-');
            }
        }

        $lines[] = '';
        $lines[] = '路线：';
        $lines[] = ' - ' . (($workflow['routes']['publish_page'] ?? '-') ?: '-');
        $lines[] = ' - ' . (($workflow['routes']['weekly_execution_api'] ?? '-') ?: '-');
        $lines[] = ' - ' . (($workflow['routes']['publish_assist_api'] ?? '-') ?: '-');

        return implode(PHP_EOL, $lines);
    }

    protected static function resolveExecutionPriority(array $recommendation): string
    {
        $gap = intval($recommendation['gap'] ?? 0);
        $progress = intval($recommendation['progress'] ?? 0);

        if ($gap >= 10 || $progress < 40) {
            return 'high';
        }
        if ($gap >= 4 || $progress < 70) {
            return 'medium';
        }
        return 'low';
    }

    protected static function buildWorkflowOutline(string $articleType, string $topic, string $seed): array
    {
        $articleType = self::normalizeWeeklyTaskKeyword($articleType) ?: 'research';
        if ($topic === '') {
            $topic = $seed !== '' ? $seed : 'Frelink 内容';
        }

        $outlineMap = [
            'research' => [
                ['title' => '背景', 'description' => '这篇综述为什么值得现在重新看一遍，它处在什么上下文里'],
                ['title' => '核心问题', 'description' => '要回答的 2-3 个关键问题是什么'],
                ['title' => '资料来源', 'description' => '主要参考了哪些一手资料和二手资料'],
                ['title' => '分歧点', 'description' => '当前最重要的分歧在哪里，不同观点各自基于什么前提'],
                ['title' => '当前判断', 'description' => '基于现有资料，当前更倾向什么判断以及原因'],
                ['title' => '待验证', 'description' => '还有哪些关键问题没有证据，后续还要继续跟踪什么'],
            ],
            'fragment' => [
                ['title' => '观察', 'description' => '这次重点看到的变化、现象或信号是什么'],
                ['title' => '触发原因', 'description' => '是什么事件、资料或体验触发了这条记录'],
                ['title' => '暂时判断', 'description' => '当前判断是什么，它成立的边界在哪里'],
                ['title' => '后续待补资料', 'description' => '下一步还需要补哪些数据、案例或对照材料'],
            ],
            'track' => [
                ['title' => '阶段更新', 'description' => '这一期追踪最重要的变化是什么'],
                ['title' => '本期变化', 'description' => '相比上次判断，哪些地方发生了变化'],
                ['title' => '旧判断是否要修正', 'description' => '哪些旧结论已经过时，为什么'],
                ['title' => '下一步观察点', 'description' => '接下来最值得继续看的 2-3 个信号是什么'],
            ],
            'faq' => [
                ['title' => '问题定义', 'description' => '这个问题到底在问什么，边界在哪里'],
                ['title' => '直接答案', 'description' => '最直接、最清晰的答案是什么'],
                ['title' => '常见误区', 'description' => '读者最容易误解的地方有哪些'],
                ['title' => '补充说明', 'description' => '有哪些条件、限制或延伸说明需要补充'],
            ],
            'tutorial' => [
                ['title' => '目标', 'description' => '这篇方法文要解决什么问题'],
                ['title' => '步骤', 'description' => '操作步骤或执行路径是什么'],
                ['title' => '关键参数', 'description' => '哪些条件、参数或设置最关键'],
                ['title' => '避坑', 'description' => '最容易出错的环节有哪些'],
            ],
            'normal' => [
                ['title' => '事件背景', 'description' => '这件事为什么现在值得关注'],
                ['title' => '真正问题', 'description' => '热点表面之下真正重要的是什么'],
                ['title' => '影响对象', 'description' => '会影响谁，如何影响'],
                ['title' => '我的判断', 'description' => '基于现有信息，当前判断是什么'],
            ],
        ];

        $outline = $outlineMap[$articleType] ?? $outlineMap['research'];
        array_unshift($outline, [
            'title' => $topic,
            'description' => '围绕该主题起草正文，先写清判断，再补结构和证据',
        ]);

        return $outline;
    }

    protected static function buildManualPromptTemplate(string $topic, string $itemType, array $recommended): array
    {
        $topic = trim($topic);
        $itemType = in_array($itemType, self::$allowedPublishItemTypes, true) ? $itemType : 'article';
        $recommendedType = $recommended['type'] ?? 'research';
        $recommendedLabel = $recommended['label'] ?? frelink_article_type_label($recommendedType);

        return [
            'instruction' => $topic !== ''
                ? '围绕“' . $topic . '”写一篇' . $recommendedLabel . '，先给出清晰判断，再补资料和边界。'
                : '围绕一个你感兴趣的观点写一篇内容，先给出清晰判断，再补资料和边界。',
            'target_type' => $itemType,
            'recommended_article_type' => $recommendedType,
            'recommended_article_type_label' => $recommendedLabel,
            'review_gate' => 'draft only, human review required before publish',
            'style_constraints' => [
                'do not sound machine generated',
                'avoid generic filler paragraphs',
                'state the conclusion before broadening the context',
            ],
        ];
    }

    protected static function mergeWeeklyTask(array &$tasksByKeyword, array $task, int $days): void
    {
        $keyword = self::normalizeWeeklyTaskKeyword((string)($task['keyword'] ?? ''));
        if ($keyword === '') {
            return;
        }

        $task['dedupe_key'] = self::buildExecutionDedupeKey($task);
        $task['task_id'] = self::buildExecutionTaskId($days, $task);

        if (!isset($tasksByKeyword[$keyword])) {
            $tasksByKeyword[$keyword] = $task;
            return;
        }

        $existing = $tasksByKeyword[$keyword];
        $existing['priority'] = self::mergeExecutionPriority((string)($existing['priority'] ?? 'low'), (string)($task['priority'] ?? 'low'));

        $sourceKeys = array_filter(array_unique(array_filter([
            trim((string)($existing['source_key'] ?? '')),
            trim((string)($task['source_key'] ?? '')),
        ])));
        if ($sourceKeys) {
            $existing['source_key'] = implode('+', $sourceKeys);
        }

        $reasonParts = array_filter(array_unique(array_filter([
            trim((string)($existing['reason'] ?? '')),
            trim((string)($task['reason'] ?? '')),
        ])));
        if ($reasonParts) {
            $existing['reason'] = implode('；', $reasonParts);
        }

        if (empty($existing['task_type']) || (($existing['task_type'] ?? '') === 'fill_gap' && ($task['task_type'] ?? '') === 'search_topic')) {
            $existing['task_type'] = $task['task_type'];
            $existing['label'] = $task['label'] ?? ($existing['label'] ?? '');
            $existing['primary_label'] = $task['primary_label'] ?? ($existing['primary_label'] ?? '');
            $existing['primary_url'] = $task['primary_url'] ?? ($existing['primary_url'] ?? '');
            $existing['secondary_label'] = $task['secondary_label'] ?? ($existing['secondary_label'] ?? '');
            $existing['secondary_url'] = $task['secondary_url'] ?? ($existing['secondary_url'] ?? '');
        }

        $existing['dedupe_key'] = self::buildExecutionDedupeKey($existing);
        $existing['task_id'] = self::buildExecutionTaskId($days, $existing);
        $tasksByKeyword[$keyword] = $existing;
    }

    protected static function mergeExecutionPriority(string $left, string $right): string
    {
        $priorityOrder = ['high' => 3, 'medium' => 2, 'low' => 1];
        $leftValue = $priorityOrder[strtolower($left)] ?? 1;
        $rightValue = $priorityOrder[strtolower($right)] ?? 1;

        return $leftValue >= $rightValue ? strtolower($left) : strtolower($right);
    }

    protected static function normalizeWeeklyTaskKeyword(string $keyword): string
    {
        $keyword = mb_strtolower(trim($keyword), 'UTF-8');
        $keyword = preg_replace('/[\s\p{P}\p{S}]+/u', '', $keyword);
        return is_string($keyword) ? $keyword : '';
    }

    protected static function getFragmentPromotionIdeas(int $days, int $limit = 3): array
    {
        $days = self::normalizeDays($days);
        $limit = max(1, min(10, $limit));
        $contentRows = self::getContentTrends($days, 30, 'article');
        if (!$contentRows) {
            return [];
        }

        $ideas = [];
        foreach ($contentRows as $row) {
            $articleId = intval($row['item_id'] ?? 0);
            if ($articleId <= 0) {
                continue;
            }

            $info = db('article')
                ->where(['id' => $articleId, 'status' => 1])
                ->field('id,title,message,article_type')
                ->find();
            if (!$info || ($info['article_type'] ?? '') !== 'fragment') {
                continue;
            }

            $detailViews = intval($row['detail_views'] ?? 0);
            $trendRatio = floatval($row['trend_ratio'] ?? 0);
            if ($detailViews < 2 && $trendRatio < 1.1) {
                continue;
            }

            $title = trim((string) ($info['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $recommended = self::recommendArticleType($title, ['suggestion' => '从观察整理为更稳定的知识内容']);
            $targetType = trim((string) ($recommended['type'] ?? '')) ?: 'research';
            if (in_array($targetType, ['fragment', 'normal'], true)) {
                $targetType = 'research';
            }

            $ideas[] = [
                'article_id' => $articleId,
                'keyword' => $title,
                'title' => '整理观察：' . $title,
                'recommended_type' => $targetType,
                'recommended_type_label' => frelink_article_type_label($targetType),
                'reason' => '这条观察最近已有阅读和关注，适合整理成更稳定的知识内容。',
                'search_count' => $detailViews,
                'detail_views' => $detailViews,
                'trend_ratio' => $trendRatio,
                'url' => (string) get_url('article/detail', ['id' => $articleId]),
            ];
        }

        if (!$ideas) {
            $latestFragment = db('article')
                ->where(['status' => 1, 'article_type' => 'fragment'])
                ->field('id,title,message,article_type')
                ->order('id', 'desc')
                ->find();

            if ($latestFragment) {
                $title = trim((string) ($latestFragment['title'] ?? ''));
                if ($title !== '') {
                    $recommended = self::recommendArticleType($title, ['suggestion' => '从观察整理为更稳定的知识内容']);
                    $targetType = trim((string) ($recommended['type'] ?? '')) ?: 'research';
                    if (in_array($targetType, ['fragment', 'normal'], true)) {
                        $targetType = 'research';
                    }

                    $ideas[] = [
                        'article_id' => intval($latestFragment['id'] ?? 0),
                        'keyword' => $title,
                        'title' => '整理观察：' . $title,
                        'recommended_type' => $targetType,
                        'recommended_type_label' => frelink_article_type_label($targetType),
                        'reason' => '这是一条近期最新的观察内容，适合尽早整理成更稳定的知识内容。',
                        'search_count' => 0,
                        'detail_views' => 0,
                        'trend_ratio' => 0,
                        'url' => (string) get_url('article/detail', ['id' => intval($latestFragment['id'] ?? 0)]),
                    ];
                }
            }
        }

        usort($ideas, function ($a, $b) {
            $aScore = intval($a['detail_views'] ?? 0) * max(1, intval(($a['trend_ratio'] ?? 0) * 100));
            $bScore = intval($b['detail_views'] ?? 0) * max(1, intval(($b['trend_ratio'] ?? 0) * 100));
            return $bScore <=> $aScore;
        });

        return array_slice($ideas, 0, $limit);
    }

    protected static function isMeaningfulSearchKeyword(string $keyword): bool
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return false;
        }

        if (preg_match('/^\d+$/', $keyword)) {
            return false;
        }

        if (preg_match('/^(19|20)\d{2}$/', $keyword)) {
            return false;
        }

        if (preg_match('/^[\W_]+$/u', $keyword)) {
            return false;
        }

        $noiseWords = ['env', 'wp', 'cgi-bin', 'phpmyadmin', 'login', 'admin', 'robots', 'sitemap', 'favicon'];
        if (in_array(mb_strtolower($keyword, 'UTF-8'), $noiseWords, true)) {
            return false;
        }

        return true;
    }

    protected static function resolveSearchExecutionPriority(array $idea): string
    {
        $searchCount = intval($idea['search_count'] ?? 0);
        $matchedContentCount = intval($idea['matched_content_count'] ?? 0);

        if ($searchCount >= 8 && $matchedContentCount <= 1) {
            return 'high';
        }
        if ($searchCount >= 4 && $matchedContentCount <= 2) {
            return 'medium';
        }
        return 'low';
    }

    protected static function resolveFragmentExecutionPriority(array $idea): string
    {
        $detailViews = intval($idea['detail_views'] ?? 0);
        $trendRatio = floatval($idea['trend_ratio'] ?? 0);

        if ($detailViews >= 8 || $trendRatio >= 1.5) {
            return 'high';
        }
        if ($detailViews >= 5 || $trendRatio >= 1.25) {
            return 'medium';
        }
        return 'low';
    }

    protected static function buildExecutionDedupeKey(array $task): string
    {
        $parts = [
            trim((string)($task['task_type'] ?? '')),
            trim((string)($task['content_type'] ?? '')),
            trim((string)($task['source_key'] ?? '')),
            trim((string)($task['keyword'] ?? '')),
        ];

        return strtolower(implode(':', array_map(function ($value) {
            return str_replace([' ', "\t", "\r", "\n"], '', $value);
        }, $parts)));
    }

    protected static function buildExecutionTaskId(int $days, array $task): string
    {
        return 'weekly-' . $days . '-' . substr(md5(($task['dedupe_key'] ?? '') . '|' . ($task['title'] ?? '')), 0, 12);
    }

    protected static function resolveSuggestedOwner(string $contentType, string $sourceKey): string
    {
        if ($sourceKey === 'faq' || $contentType === 'faq') {
            return 'faq_editor';
        }

        if (in_array($contentType, ['tutorial', 'faq'], true) || $sourceKey === 'help') {
            return 'knowledge_editor';
        }

        if ($contentType === 'track') {
            return 'research_editor';
        }

        if ($contentType === 'fragment') {
            return 'observer';
        }

        return 'research_editor';
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
        } elseif (preg_match('/(追踪|阶段更新|阶段判断|复盘|修正|进展|跟踪|第[0-9一二三四五六七八九十]+期|变化|演进|路线图)/u', $text)) {
            $type = 'track';
            $reason = '搜索意图偏阶段变化和连续观察，适合整理成主题追踪。';
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
                'track' => $keyword . '追踪：最近变了什么',
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
            if (isset($info['article_type'])) {
                $row['article_type'] = $info['article_type'];
            }
            if (isset($info['article_type_label'])) {
                $row['article_type_label'] = $info['article_type_label'];
            }
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
                $info = db('article')->where(['id' => $itemId, 'status' => 1])->field('title,message,article_type')->find();
                if (!$info) {
                    return [];
                }
                return [
                    'title' => $info['title'],
                    'summary' => str_cut(strip_tags(htmlspecialchars_decode((string) $info['message'])), 0, 120),
                    'url' => (string) url('article/detail', ['id' => $itemId]),
                    'article_type' => $info['article_type'] ?? '',
                    'article_type_label' => frelink_article_type_label($info['article_type'] ?? 'normal'),
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
