<?php
namespace app\model;

class Help extends BaseModel
{
    public static function getActiveChapterList($limit = 0): array
    {
        $query = db('help_chapter')
            ->where(['status' => 1])
            ->order(['sort' => 'ASC', 'id' => 'DESC']);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->select()->toArray();
    }

    public static function getFeaturedArchiveChapters($limit = 4, $itemLimit = 3): array
    {
        $chapters = self::getActiveChapterList($limit);
        if (!$chapters) {
            return [];
        }

        foreach ($chapters as $k => $chapter) {
            $chapters[$k]['chapters'] = self::getRelationHelpChapterListByChapterId($chapter['id'], $itemLimit);
            $chapters[$k]['relation_count'] = db('help_chapter_relation')
                ->where(['chapter_id' => $chapter['id'], 'status' => 1])
                ->count();
        }

        return $chapters;
    }

    public static function getKnowledgeMapSummary(): array
    {
        $chapterCount = db('help_chapter')->where(['status' => 1])->count();
        $relationCount = db('help_chapter_relation')->where(['status' => 1])->count();
        $questionCount = db('help_chapter_relation')->where(['status' => 1, 'item_type' => 'question'])->count();
        $articleCount = db('help_chapter_relation')->where(['status' => 1, 'item_type' => 'article'])->count();

        return [
            'chapter_count' => $chapterCount,
            'relation_count' => $relationCount,
            'question_count' => $questionCount,
            'article_count' => $articleCount,
        ];
    }

    public static function getUnarchivedContentSummary(int $limit = 6): array
    {
        $questionCount = db('question')
            ->alias('q')
            ->join('help_chapter_relation r', "r.item_id = q.id AND r.item_type = 'question' AND r.status = 1", 'LEFT')
            ->where(['q.status' => 1])
            ->whereRaw('r.id IS NULL')
            ->count();

        $articleCount = db('article')
            ->alias('a')
            ->join('help_chapter_relation r', "r.item_id = a.id AND r.item_type = 'article' AND r.status = 1", 'LEFT')
            ->where(['a.status' => 1])
            ->whereRaw('r.id IS NULL')
            ->count();

        $questionList = db('question')
            ->alias('q')
            ->join('help_chapter_relation r', "r.item_id = q.id AND r.item_type = 'question' AND r.status = 1", 'LEFT')
            ->where(['q.status' => 1])
            ->whereRaw('r.id IS NULL')
            ->field('q.id,q.title,q.update_time,q.view_count,q.answer_count')
            ->order(['q.update_time' => 'DESC', 'q.id' => 'DESC'])
            ->limit($limit)
            ->select()
            ->toArray();

        $articleList = db('article')
            ->alias('a')
            ->join('help_chapter_relation r', "r.item_id = a.id AND r.item_type = 'article' AND r.status = 1", 'LEFT')
            ->where(['a.status' => 1])
            ->whereRaw('r.id IS NULL')
            ->field('a.id,a.title,a.update_time,a.view_count,a.comment_count,a.article_type')
            ->order(['a.update_time' => 'DESC', 'a.id' => 'DESC'])
            ->limit($limit)
            ->select()
            ->toArray();

        foreach ($articleList as $k => $item) {
            $articleList[$k]['article_type_label'] = frelink_article_type_label($item['article_type'] ?? 'normal');
        }

        return [
            'question_count' => $questionCount,
            'article_count' => $articleCount,
            'questions' => $questionList,
            'articles' => $articleList,
        ];
    }

    public static function getArchiveChapterBacklogSummary(int $limit = 8): array
    {
        $chapters = db('help_chapter')
            ->alias('c')
            ->join('help_chapter_relation r', 'r.chapter_id = c.id AND r.status = 1', 'LEFT')
            ->where(['c.status' => 1])
            ->field('c.id,c.title,c.url_token,c.sort,c.description,count(r.id) as relation_count')
            ->group('c.id,c.title,c.url_token,c.sort,c.description')
            ->order(['relation_count' => 'ASC', 'c.sort' => 'ASC', 'c.id' => 'DESC'])
            ->limit($limit)
            ->select()
            ->toArray();

        $emptyCount = 0;
        $lowCount = 0;

        foreach ($chapters as $k => $chapter) {
            $relationCount = intval($chapter['relation_count'] ?? 0);
            if ($relationCount === 0) {
                $emptyCount++;
                $chapters[$k]['backlog_label'] = '空章节';
            } elseif ($relationCount <= 2) {
                $lowCount++;
                $chapters[$k]['backlog_label'] = '待补充';
            } else {
                $chapters[$k]['backlog_label'] = '已起步';
            }
        }

        $totalChapters = db('help_chapter')->where(['status' => 1])->count();

        return [
            'empty_count' => $emptyCount,
            'low_count' => $lowCount,
            'chapter_count' => $totalChapters,
            'chapters' => $chapters,
        ];
    }

    /**
     * 获取帮助章节列表
     * @param $page
     * @param $per_page
     * @param $chapters
     * @param $pjax
     * @return array
     */
    public static function getHelpChapterList($page=1,$per_page=10,$chapters=true,$pjax='tabMain'): array
    {
        $list = db('help_chapter')
            ->order('sort', 'ASC')
            ->paginate(
            [
                'list_rows' => $per_page,
                'page' => $page,
                'query' => request()->param(),
                'pjax' => $pjax
            ]
        );
        $pageVar = $list->render();
        $data = $list->toArray();
        if($chapters)
        {
            foreach ($data['data'] as $k=>$v)
            {
                $data['data'][$k]['chapters'] = self::getRelationHelpChapterListByChapterId($v['id'],3,);
            }
        }
        return ['list' => $data['data'], 'page' => $pageVar, 'total' => $data['last_page']];
    }

    private static function getRelationHelpChapterListByChapterId($chapter_id,$limit)
    {
        $list = db('help_chapter_relation')
            ->where(['chapter_id'=>$chapter_id,'status'=>1])
            ->order('sort', 'ASC')
            ->limit($limit)
            ->select()
            ->toArray();

        foreach ($list as $k=>$v)
        {
            if($v['item_type']=='question')
            {
                $list[$k]['info'] = db('question')->where(['id'=>$v['item_id'],'status'=>1])->find();
            }

            if($v['item_type']=='article')
            {
                $list[$k]['info'] = db('article')->where(['id'=>$v['item_id'],'status'=>1])->find();
            }
        }
        return $list;
    }

    public static function getRelationHelpChapterList($chapter_id,$page=1,$per_page=10,$pjax='tabMain',$itemType='')
    {
        $query = db('help_chapter_relation')
            ->where(['chapter_id'=>$chapter_id,'status'=>1])
            ->order('sort', 'ASC')
        ;
        if (in_array($itemType, ['question', 'article'], true)) {
            $query->where(['item_type' => $itemType]);
        }

        $list = $query->paginate(
            [
                'list_rows' => $per_page,
                'page' => $page,
                'query' => request()->param(),
                'pjax' => $pjax
            ]
        );
        $pageVar = $list->render();
        $data = $list->toArray();
        $dataList=$data['data'];
        foreach ($dataList as $k=>$v)
        {
            if($v['item_type']=='question')
            {
                $dataList[$k]['info'] = db('question')->where(['id'=>$v['item_id'],'status'=>1])->find();
            }

            if($v['item_type']=='article')
            {
                $dataList[$k]['info'] = db('article')->where(['id'=>$v['item_id'],'status'=>1])->find();
            }
        }

        return ['list' => $dataList, 'page' => $pageVar, 'total' => $data['last_page']];
    }

    public static function getChapterRelationStats($chapter_id): array
    {
        $stats = db('help_chapter_relation')
            ->where(['chapter_id' => $chapter_id, 'status' => 1])
            ->field('item_type,count(*) as total')
            ->group('item_type')
            ->select()
            ->toArray();

        $summary = [
            'question_count' => 0,
            'article_count' => 0,
            'total_count' => 0,
        ];

        foreach ($stats as $row) {
            $count = intval($row['total'] ?? 0);
            $summary['total_count'] += $count;
            if (($row['item_type'] ?? '') === 'question') {
                $summary['question_count'] = $count;
            }
            if (($row['item_type'] ?? '') === 'article') {
                $summary['article_count'] = $count;
            }
        }

        return $summary;
    }

    public static function getChapterRelatedTopics(array $chapterInfo, int $limit = 6): array
    {
        if (empty($chapterInfo['title'])) {
            return [];
        }

        $keywords = self::extractSuggestionKeywords([
            'title' => $chapterInfo['title'] ?? '',
            'topics' => []
        ]);

        if (!empty($chapterInfo['description'])) {
            $descParts = preg_split('/[\s,，。；;、\/\-\|\(\)（）]+/u', strip_tags((string)$chapterInfo['description']));
            foreach ($descParts as $part) {
                $part = trim((string)$part);
                if ($part !== '' && mb_strlen($part) >= 2) {
                    $keywords[] = $part;
                }
            }
        }

        $keywords = array_values(array_unique(array_filter($keywords)));
        if (!$keywords) {
            return [];
        }

        $chapterId = intval($chapterInfo['id'] ?? 0);
        $topicMatchMap = [];
        if ($chapterId) {
            $topicMatchRows = db('help_chapter_relation')
                ->alias('hcr')
                ->join('topic_relation tr', 'tr.item_id = hcr.item_id AND tr.item_type = hcr.item_type AND tr.status = 1')
                ->join('topic t', 't.id = tr.topic_id AND t.status = 1')
                ->where(['hcr.chapter_id' => $chapterId, 'hcr.status' => 1])
                ->field('t.id,count(DISTINCT concat(hcr.item_type,":",hcr.item_id)) as matched_count')
                ->group('t.id')
                ->select()
                ->toArray();
            $topicMatchMap = $topicMatchRows ? array_column($topicMatchRows, 'matched_count', 'id') : [];
        }

        $topics = db('topic')
            ->where(['status' => 1])
            ->field('id,title,description,url_token,focus,discuss')
            ->order(['discuss' => 'DESC', 'focus' => 'DESC', 'id' => 'DESC'])
            ->limit(120)
            ->select()
            ->toArray();

        foreach ($topics as $k => $topic) {
            $score = 0;
            $title = mb_strtolower(strip_tags((string)($topic['title'] ?? '')));
            $desc = mb_strtolower(strip_tags((string)($topic['description'] ?? '')));

            foreach ($keywords as $keyword) {
                $keyword = mb_strtolower((string)$keyword);
                if ($keyword === '') {
                    continue;
                }
                if (mb_stripos($title, $keyword) !== false) {
                    $score += 12;
                }
                if ($desc !== '' && mb_stripos($desc, $keyword) !== false) {
                    $score += 5;
                }
            }

            $topics[$k]['matched_count'] = intval($topicMatchMap[$topic['id']] ?? 0);
            $topics[$k]['score'] = $score
                + ($topics[$k]['matched_count'] * 20)
                + min(intval($topic['discuss'] ?? 0), 10)
                + min(intval($topic['focus'] ?? 0), 6);
        }

        $topics = array_values(array_filter($topics, function ($topic) {
            return intval($topic['score'] ?? 0) > 0;
        }));

        usort($topics, function ($a, $b) {
            if (intval($a['score']) === intval($b['score'])) {
                if (intval($a['matched_count'] ?? 0) === intval($b['matched_count'] ?? 0)) {
                return intval($b['discuss'] ?? 0) <=> intval($a['discuss'] ?? 0);
                }
                return intval($b['matched_count'] ?? 0) <=> intval($a['matched_count'] ?? 0);
            }
            return intval($b['score']) <=> intval($a['score']);
        });

        return array_slice($topics, 0, $limit);
    }

    public static function getTopicRelatedChapters(array $topicInfo, int $limit = 4): array
    {
        if (empty($topicInfo['title'])) {
            return [];
        }

        $chapters = self::getActiveChapterList();
        if (!$chapters) {
            return [];
        }

        $topicPayload = [
            'title' => $topicInfo['title'] ?? '',
            'topics' => [trim((string)($topicInfo['title'] ?? ''))],
        ];

        if (!empty($topicInfo['description'])) {
            $topicPayload['description'] = strip_tags((string)$topicInfo['description']);
        }

        if (!empty($topicInfo['relation_topics']) && is_array($topicInfo['relation_topics'])) {
            foreach ($topicInfo['relation_topics'] as $relatedTopic) {
                if (!empty($relatedTopic['title'])) {
                    $topicPayload['topics'][] = trim((string)$relatedTopic['title']);
                }
            }
        }

        $keywords = self::extractSuggestionKeywords($topicPayload);
        if (!$keywords) {
            return [];
        }

        $topicIds = [intval($topicInfo['id'] ?? 0)];
        if (!empty($topicInfo['relation_topics']) && is_array($topicInfo['relation_topics'])) {
            foreach ($topicInfo['relation_topics'] as $relatedTopic) {
                $relatedId = intval($relatedTopic['id'] ?? 0);
                if ($relatedId) {
                    $topicIds[] = $relatedId;
                }
            }
        }
        $topicIds = array_values(array_unique(array_filter($topicIds)));

        $relationCountMap = db('help_chapter_relation')
            ->where(['status' => 1])
            ->field('chapter_id,count(*) as relation_count')
            ->group('chapter_id')
            ->select()
            ->toArray();
        $relationCountMap = $relationCountMap ? array_column($relationCountMap, 'relation_count', 'chapter_id') : [];

        $topicMatchMap = [];
        if ($topicIds) {
            $topicMatchRows = db('help_chapter_relation')
                ->alias('hcr')
                ->join('topic_relation tr', 'tr.item_id = hcr.item_id AND tr.item_type = hcr.item_type AND tr.status = 1')
                ->where(['hcr.status' => 1])
                ->whereIn('tr.topic_id', $topicIds)
                ->field('hcr.chapter_id,count(DISTINCT concat(hcr.item_type,":",hcr.item_id)) as matched_count')
                ->group('hcr.chapter_id')
                ->select()
                ->toArray();
            $topicMatchMap = $topicMatchRows ? array_column($topicMatchRows, 'matched_count', 'chapter_id') : [];
        }

        foreach ($chapters as $k => $chapter) {
            $score = 0;
            $haystackTitle = mb_strtolower((string)($chapter['title'] ?? ''));
            $haystackDesc = mb_strtolower(strip_tags((string)($chapter['description'] ?? '')));

            foreach ($keywords as $keyword) {
                $keyword = mb_strtolower((string)$keyword);
                if ($keyword === '') {
                    continue;
                }
                if (mb_stripos($haystackTitle, $keyword) !== false) {
                    $score += 12;
                }
                if ($haystackDesc !== '' && mb_stripos($haystackDesc, $keyword) !== false) {
                    $score += 6;
                }
            }

            $chapters[$k]['relation_count'] = intval($relationCountMap[$chapter['id']] ?? 0);
            $chapters[$k]['topic_match_count'] = intval($topicMatchMap[$chapter['id']] ?? 0);
            $chapters[$k]['score'] = $score + min($chapters[$k]['relation_count'], 8) + ($chapters[$k]['topic_match_count'] * 20);
        }

        $chapters = array_values(array_filter($chapters, function ($chapter) {
            return intval($chapter['score'] ?? 0) > 0;
        }));

        usort($chapters, function ($a, $b) {
            if (intval($a['score']) === intval($b['score'])) {
                if (intval($a['topic_match_count'] ?? 0) === intval($b['topic_match_count'] ?? 0)) {
                    if (intval($a['relation_count'] ?? 0) === intval($b['relation_count'] ?? 0)) {
                        if (intval($a['sort'] ?? 0) === intval($b['sort'] ?? 0)) {
                            return intval($b['id'] ?? 0) <=> intval($a['id'] ?? 0);
                        }
                        return intval($a['sort'] ?? 0) <=> intval($b['sort'] ?? 0);
                    }
                    return intval($b['relation_count'] ?? 0) <=> intval($a['relation_count'] ?? 0);
                }
                return intval($b['topic_match_count'] ?? 0) <=> intval($a['topic_match_count'] ?? 0);
            }
            return intval($b['score']) <=> intval($a['score']);
        });

        return array_slice($chapters, 0, $limit);
    }

    /**
     * 检查是否已加入帮助
     */
    public static function checkRelationHelpItemExist($chapter_id,$item_id,$item_type)
    {
        if(!$chapter_id || !$item_type || !$item_id) return false;
        return  db('help_chapter_relation')->where(['chapter_id'=>$chapter_id,'status'=>1,'item_id'=>$item_id,'item_type'=>$item_type])->value('id');
    }

    public static function getItemArchiveChapters($item_type, $item_id, $limit = 0): array
    {
        if (!$item_type || !$item_id) {
            return [];
        }

        $query = db('help_chapter_relation')
            ->alias('r')
            ->join('help_chapter c', 'c.id = r.chapter_id')
            ->where([
                'r.item_type' => $item_type,
                'r.item_id' => $item_id,
                'r.status' => 1,
                'c.status' => 1
            ])
            ->field('r.id as relation_id,r.chapter_id,c.title,c.url_token,c.description,c.image,c.sort')
            ->order(['c.sort' => 'ASC', 'c.id' => 'DESC']);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->select()->toArray();
    }

    public static function getItemArchiveChapterIds($item_type, $item_id): array
    {
        if (!$item_type || !$item_id) {
            return [];
        }

        return db('help_chapter_relation')
            ->where([
                'item_type' => $item_type,
                'item_id' => $item_id,
                'status' => 1
            ])
            ->column('chapter_id');
    }

    public static function attachItemArchiveChapter($item_type, $item_id, $chapter_id): bool
    {
        $item_type = trim((string)$item_type);
        $item_id = intval($item_id);
        $chapter_id = intval($chapter_id);
        if (!$item_type || !$item_id || !$chapter_id) {
            return false;
        }

        if (self::checkRelationHelpItemExist($chapter_id, $item_id, $item_type)) {
            return true;
        }

        return (bool)db('help_chapter_relation')->insert([
            'chapter_id' => $chapter_id,
            'item_id' => $item_id,
            'item_type' => $item_type,
            'sort' => 999,
            'status' => 1
        ]);
    }

    public static function syncItemArchiveChapters($item_type, $item_id, array $chapter_ids = []): bool
    {
        if (!$item_type || !$item_id) {
            return false;
        }

        $chapter_ids = array_values(array_unique(array_filter(array_map('intval', $chapter_ids))));

        db('help_chapter_relation')
            ->where([
                'item_type' => $item_type,
                'item_id' => $item_id
            ])
            ->delete();

        if (!$chapter_ids) {
            return true;
        }

        $rows = [];
        foreach ($chapter_ids as $sort => $chapter_id) {
            $rows[] = [
                'chapter_id' => $chapter_id,
                'item_id' => $item_id,
                'item_type' => $item_type,
                'sort' => $sort + 1,
                'status' => 1
            ];
        }

        return (bool)db('help_chapter_relation')->insertAll($rows);
    }

    public static function getChapterCandidateContent($chapter_id, int $limitPerType = 6): array
    {
        $chapter = db('help_chapter')->where(['id' => $chapter_id, 'status' => 1])->find();
        if (!$chapter) {
            return ['chapter' => null, 'questions' => [], 'articles' => [], 'related_topics' => []];
        }

        $keywords = self::extractSuggestionKeywords([
            'title' => $chapter['title'] ?? '',
            'topics' => []
        ]);
        if (!empty($chapter['description'])) {
            $descKeywords = preg_split('/[\s,，。；;、\/\-\|\(\)（）]+/u', strip_tags((string)$chapter['description']));
            foreach ($descKeywords as $part) {
                $part = trim((string)$part);
                if ($part !== '' && mb_strlen($part) >= 2) {
                    $keywords[] = $part;
                }
            }
            $keywords = array_values(array_unique($keywords));
        }

        $questionCandidates = self::scoreCandidateItems(
            db('question')
                ->alias('q')
                ->join('help_chapter_relation r', "r.item_id = q.id AND r.item_type = 'question' AND r.status = 1", 'LEFT')
                ->where(['q.status' => 1])
                ->whereRaw('r.id IS NULL')
                ->field('q.id,q.title,q.detail,q.update_time,q.view_count,q.answer_count')
                ->order(['q.update_time' => 'DESC', 'q.id' => 'DESC'])
                ->limit(80)
                ->select()
                ->toArray(),
            $keywords,
            'question',
            $limitPerType
        );

        $articleCandidates = self::scoreCandidateItems(
            db('article')
                ->alias('a')
                ->join('help_chapter_relation r', "r.item_id = a.id AND r.item_type = 'article' AND r.status = 1", 'LEFT')
                ->where(['a.status' => 1])
                ->whereRaw('r.id IS NULL')
                ->field('a.id,a.title,a.message,a.update_time,a.view_count,a.comment_count,a.article_type')
                ->order(['a.update_time' => 'DESC', 'a.id' => 'DESC'])
                ->limit(80)
                ->select()
                ->toArray(),
            $keywords,
            'article',
            $limitPerType
        );

        foreach ($articleCandidates as $k => $item) {
            $articleCandidates[$k]['article_type_label'] = frelink_article_type_label($item['article_type'] ?? 'normal');
        }

        return [
            'chapter' => $chapter,
            'questions' => $questionCandidates,
            'articles' => $articleCandidates,
            'related_topics' => self::getChapterTopicHints($chapter_id, 8),
        ];
    }

    public static function getChapterTopicHints(int $chapter_id, int $limit = 8): array
    {
        if (!$chapter_id) {
            return [];
        }

        $rows = db('help_chapter_relation')
            ->alias('hcr')
            ->join('topic_relation tr', 'tr.item_id = hcr.item_id AND tr.item_type = hcr.item_type AND tr.status = 1')
            ->join('topic t', 't.id = tr.topic_id AND t.status = 1')
            ->where(['hcr.chapter_id' => $chapter_id, 'hcr.status' => 1])
            ->field('t.id,t.title,t.url_token,t.focus,t.discuss,count(DISTINCT concat(hcr.item_type,":",hcr.item_id)) as matched_count')
            ->group('t.id,t.title,t.url_token,t.focus,t.discuss')
            ->order(['matched_count' => 'DESC', 't.discuss' => 'DESC', 't.focus' => 'DESC', 't.id' => 'DESC'])
            ->limit($limit)
            ->select()
            ->toArray();

        return $rows ?: [];
    }

    public static function getSuggestedArchiveChapters($item_type, array $item_info = [], $limit = 6): array
    {
        $chapters = self::getActiveChapterList();
        if (!$chapters) {
            return [];
        }

        $keywords = self::extractSuggestionKeywords($item_info);
        $relationCountMap = db('help_chapter_relation')
            ->where(['status' => 1])
            ->field('chapter_id,count(*) as relation_count')
            ->group('chapter_id')
            ->select()
            ->toArray();
        $relationCountMap = $relationCountMap ? array_column($relationCountMap, 'relation_count', 'chapter_id') : [];

        foreach ($chapters as $k => $chapter) {
            $score = 0;
            $haystackTitle = mb_strtolower((string)$chapter['title']);
            $haystackDesc = mb_strtolower(strip_tags((string)$chapter['description']));

            foreach ($keywords as $keyword) {
                $keyword = mb_strtolower((string)$keyword);
                if ($keyword === '') {
                    continue;
                }
                if (mb_stripos($haystackTitle, $keyword) !== false) {
                    $score += 12;
                }
                if ($haystackDesc && mb_stripos($haystackDesc, $keyword) !== false) {
                    $score += 6;
                }
            }

            if ($item_type === 'article' && in_array(($item_info['article_type'] ?? ''), ['faq', 'tutorial'])) {
                if (mb_stripos($haystackTitle, '帮助') !== false || mb_stripos($haystackTitle, 'FAQ') !== false) {
                    $score += 8;
                }
            }

            if ($item_type === 'question') {
                if (mb_stripos($haystackTitle, 'FAQ') !== false || mb_stripos($haystackTitle, '问题') !== false) {
                    $score += 5;
                }
            }

            $chapters[$k]['relation_count'] = intval($relationCountMap[$chapter['id']] ?? 0);
            $chapters[$k]['score'] = $score + min($chapters[$k]['relation_count'], 10);
        }

        usort($chapters, function ($a, $b) {
            if ($a['score'] === $b['score']) {
                if ($a['relation_count'] === $b['relation_count']) {
                    if (intval($a['sort']) === intval($b['sort'])) {
                        return intval($b['id']) <=> intval($a['id']);
                    }
                    return intval($a['sort']) <=> intval($b['sort']);
                }
                return intval($b['relation_count']) <=> intval($a['relation_count']);
            }
            return intval($b['score']) <=> intval($a['score']);
        });

        $chapters = array_values(array_filter($chapters, function ($chapter) {
            return $chapter['score'] > 0;
        }));

        if (!$chapters) {
            $chapters = self::getActiveChapterList($limit);
        }

        return array_slice($chapters, 0, $limit);
    }

    private static function extractSuggestionKeywords(array $item_info): array
    {
        $keywords = [];

        if (!empty($item_info['title'])) {
            $title = trim(strip_tags((string)$item_info['title']));
            if ($title !== '') {
                $keywords[] = $title;
                $parts = preg_split('/[\s,，。；;、\/\-\|\(\)（）]+/u', $title);
                foreach ($parts as $part) {
                    $part = trim($part);
                    if ($part !== '' && mb_strlen($part) >= 2) {
                        $keywords[] = $part;
                    }
                }
            }
        }

        if (!empty($item_info['topics'])) {
            $topicTitles = [];
            if (is_string($item_info['topics'])) {
                $topicIds = array_filter(array_map('intval', explode(',', $item_info['topics'])));
                if ($topicIds) {
                    $topicTitles = db('topic')->whereIn('id', $topicIds)->column('title');
                }
            } elseif (is_array($item_info['topics'])) {
                foreach ($item_info['topics'] as $topic) {
                    if (is_array($topic) && !empty($topic['title'])) {
                        $topicTitles[] = $topic['title'];
                    } elseif (is_numeric($topic)) {
                        $title = db('topic')->where('id', intval($topic))->value('title');
                        if ($title) {
                            $topicTitles[] = $title;
                        }
                    }
                }
            }

            foreach ($topicTitles as $title) {
                $title = trim((string)$title);
                if ($title !== '') {
                    $keywords[] = $title;
                }
            }
        }

        return array_values(array_unique($keywords));
    }

    private static function scoreCandidateItems(array $items, array $keywords, string $itemType, int $limit): array
    {
        if (!$items || !$keywords) {
            return [];
        }

        foreach ($items as $k => $item) {
            $score = 0;
            $title = mb_strtolower(strip_tags((string)($item['title'] ?? '')));
            $bodyField = $itemType === 'question' ? 'detail' : 'message';
            $body = mb_strtolower(strip_tags((string)($item[$bodyField] ?? '')));

            foreach ($keywords as $keyword) {
                $keyword = mb_strtolower((string)$keyword);
                if ($keyword === '') {
                    continue;
                }
                if (mb_stripos($title, $keyword) !== false) {
                    $score += 12;
                }
                if ($body !== '' && mb_stripos($body, $keyword) !== false) {
                    $score += 5;
                }
            }

            $items[$k]['score'] = $score;
        }

        $items = array_values(array_filter($items, function ($item) {
            return intval($item['score'] ?? 0) > 0;
        }));

        usort($items, function ($a, $b) {
            if (intval($a['score']) === intval($b['score'])) {
                return intval($b['update_time'] ?? 0) <=> intval($a['update_time'] ?? 0);
            }
            return intval($b['score']) <=> intval($a['score']);
        });

        return array_slice($items, 0, $limit);
    }
}
