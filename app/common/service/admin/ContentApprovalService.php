<?php

namespace app\common\service\admin;

use app\model\Approval as ApprovalModel;
use app\model\Users as UserModel;

class ContentApprovalService
{
    public function getOverview(int $status = 0, string $type = '', string $agentScope = '', string $keyword = ''): array
    {
        $allowedStatus = [0, 1, 2];
        if (!in_array($status, $allowedStatus, true)) {
            $status = 0;
        }

        return [
            'status' => $status,
            'type' => $type,
            'is_agent' => $agentScope,
            'keyword' => $keyword,
            'status_tabs' => [
                ['label' => '待审核', 'value' => 0],
                ['label' => '已审核', 'value' => 1],
                ['label' => '已拒绝', 'value' => 2],
            ],
            'type_tabs' => $this->getTypeTabs(),
            'list' => $this->getList($status, $type, $agentScope, $keyword),
        ];
    }

    public function getDetail(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        $info = db('approval')
            ->alias('a')
            ->leftJoin('users u', 'a.uid = u.uid')
            ->where('a.id', $id)
            ->field('a.*,u.user_name,u.url_token,u.is_agent')
            ->find();

        if (!$info) {
            return [];
        }

        $data = json_decode((string) ($info['data'] ?? '{}'), true);
        if (!is_array($data)) {
            $data = [];
        }

        $reviewMeta = $this->buildContentReview((string) ($info['type'] ?? ''), $data);

        return [
            'id' => intval($info['id'] ?? 0),
            'uid' => intval($info['uid'] ?? 0),
            'user_name' => (string) ($info['user_name'] ?? ''),
            'url_token' => (string) ($info['url_token'] ?? ''),
            'type' => (string) ($info['type'] ?? ''),
            'type_label' => $this->getTypeLabel((string) ($info['type'] ?? '')),
            'status' => intval($info['status'] ?? 0),
            'reason' => (string) ($info['reason'] ?? ''),
            'item_id' => intval($info['item_id'] ?? 0),
            'is_agent' => intval($info['is_agent'] ?? 0),
            'create_time_text' => !empty($info['create_time']) ? date('Y-m-d H:i:s', intval($info['create_time'])) : '-',
            'summary' => $this->buildSummary((string) ($info['type'] ?? ''), $data),
            'target_url' => $this->buildTargetUrl((string) ($info['type'] ?? ''), $data, intval($info['item_id'] ?? 0)),
            'subject_title' => $this->buildSubjectTitle((string) ($info['type'] ?? ''), $data, intval($info['item_id'] ?? 0)),
            'preview_fields' => $this->buildPreviewFields((string) ($info['type'] ?? ''), $data, intval($info['uid'] ?? 0)),
            'content_review' => $reviewMeta,
            'payload' => $data,
            'payload_json' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        ];
    }

    public function approve($ids): array
    {
        if (!ApprovalModel::approval($ids)) {
            return ['code' => 0, 'msg' => '审核失败' . (ApprovalModel::getError() ?: '')];
        }

        return ['code' => 1, 'msg' => '审核成功'];
    }

    public function decline($ids, string $reason = ''): array
    {
        if (!ApprovalModel::decline($ids, $reason)) {
            return ['code' => 0, 'msg' => '操作失败' . (ApprovalModel::getError() ?: '')];
        }

        return ['code' => 1, 'msg' => '操作成功'];
    }

    public function delete($ids): array
    {
        $ids = $this->normalizeIds($ids);
        if (!$ids) {
            return ['code' => 0, 'msg' => '请选择要删除的数据'];
        }

        $deleted = db('approval')->whereIn('id', $ids)->delete();
        if (!$deleted) {
            return ['code' => 0, 'msg' => '删除失败'];
        }

        return ['code' => 1, 'msg' => '删除成功'];
    }

    public function forbid($uids, string $forbiddenTime, string $reason): array
    {
        $uids = $this->normalizeIds($uids);
        if (!$uids) {
            return ['code' => 0, 'msg' => '请选择要操作的数据'];
        }
        if ($forbiddenTime === '') {
            return ['code' => 0, 'msg' => '请选择封禁时长'];
        }
        if ($reason === '') {
            return ['code' => 0, 'msg' => '请填写封禁原因'];
        }

        foreach ($uids as $uid) {
            $forbidden = db('users_forbidden')->where(['uid' => $uid])->find();
            $payload = [
                'uid' => $uid,
                'forbidden_time' => strtotime($forbiddenTime),
                'forbidden_reason' => trim($reason),
                'status' => 1,
            ];
            if ($forbidden) {
                db('users_forbidden')->where(['uid' => $uid])->update($payload);
            } else {
                $payload['create_time'] = time();
                db('users_forbidden')->insert($payload);
            }
        }

        db('users')->whereIn('uid', $uids)->update(['status' => 3]);
        return ['code' => 1, 'msg' => '操作成功'];
    }

    public function forbiddenIp($uids): array
    {
        $uids = $this->normalizeIds($uids);
        if (!$uids) {
            return ['code' => 0, 'msg' => '请选择要操作的数据'];
        }

        if (count($uids) === 1) {
            $user = UserModel::find($uids[0]);
            $result = $user ? UserModel::forbiddenIp(['uid' => $uids[0], 'ip' => $user->last_login_ip, 'time' => time()]) : false;
            return ['code' => $result ? 1 : 0, 'msg' => $result ? '操作成功' : '操作失败'];
        }

        $result = UserModel::batchForbiddenIp($uids);
        return ['code' => $result ? 1 : 0, 'msg' => $result ? '操作成功' : '操作失败'];
    }

    protected function getList(int $status, string $type, string $agentScope, string $keyword = ''): array
    {
        $query = db('approval')
            ->alias('a')
            ->leftJoin('users u', 'a.uid = u.uid')
            ->where('a.status', $status)
            ->field('a.id,a.uid,a.type,a.status,a.item_id,a.reason,a.data,a.create_time,u.user_name,u.url_token,u.is_agent');

        if ($type !== '') {
            $query->where('a.type', $type);
        }
        if ($agentScope === '0' || $agentScope === '1') {
            $query->where('u.is_agent', intval($agentScope));
        }

        $list = $query->order(['a.id' => 'desc'])->select()->toArray();
        $keyword = trim($keyword);
        foreach ($list as &$item) {
            $payload = json_decode((string) ($item['data'] ?? '{}'), true);
            if (!is_array($payload)) {
                $payload = [];
            }
            $item['id'] = intval($item['id'] ?? 0);
            $item['uid'] = intval($item['uid'] ?? 0);
            $item['status'] = intval($item['status'] ?? 0);
            $item['item_id'] = intval($item['item_id'] ?? 0);
            $item['is_agent'] = intval($item['is_agent'] ?? 0);
            $item['type_label'] = $this->getTypeLabel((string) ($item['type'] ?? ''));
            $item['summary'] = $this->buildSummary((string) ($item['type'] ?? ''), $payload);
            $item['target_url'] = $this->buildTargetUrl((string) ($item['type'] ?? ''), $payload, $item['item_id']);
            $item['create_time_text'] = !empty($item['create_time']) ? date('Y-m-d H:i:s', intval($item['create_time'])) : '-';
            $item['content_review'] = $this->buildContentReview((string) ($item['type'] ?? ''), $payload);
            unset($item['data']);
        }
        unset($item);

        if ($keyword !== '') {
            $normalized = mb_strtolower($keyword, 'UTF-8');
            $list = array_values(array_filter($list, function ($item) use ($normalized) {
                $id = strval(intval($item['id'] ?? 0));
                if ($id === $normalized) {
                    return true;
                }
                $summary = mb_strtolower((string) ($item['summary'] ?? ''), 'UTF-8');
                $userName = mb_strtolower((string) ($item['user_name'] ?? ''), 'UTF-8');
                return str_contains($summary, $normalized) || str_contains($userName, $normalized);
            }));
        }

        return $list;
    }

    protected function getTypeTabs(): array
    {
        return [
            ['label' => '全部', 'value' => ''],
            ['label' => '问题', 'value' => 'question'],
            ['label' => '文章', 'value' => 'article'],
            ['label' => '回答', 'value' => 'answer'],
            ['label' => '修改问题', 'value' => 'modify_question'],
            ['label' => '修改文章', 'value' => 'modify_article'],
            ['label' => '修改回答', 'value' => 'modify_answer'],
            ['label' => '问题评论', 'value' => 'question_comment'],
            ['label' => '文章评论', 'value' => 'article_comment'],
            ['label' => '回答评论', 'value' => 'answer_comment'],
            ['label' => '话题', 'value' => 'topic'],
        ];
    }

    protected function getTypeLabel(string $type): string
    {
        $map = [];
        foreach ($this->getTypeTabs() as $item) {
            if ($item['value'] !== '') {
                $map[$item['value']] = $item['label'];
            }
        }
        return $map[$type] ?? $type;
    }

    protected function buildSummary(string $type, array $payload): string
    {
        if (isset($payload['title']) && $payload['title'] !== '') {
            return (string) $payload['title'];
        }
        if ($type === 'answer' || $type === 'modify_answer') {
            return str_cut(strip_tags(htmlspecialchars_decode((string) ($payload['content'] ?? ''))), 0, 80);
        }
        if (str_contains($type, 'comment')) {
            return str_cut(strip_tags(htmlspecialchars_decode((string) ($payload['message'] ?? ''))), 0, 80);
        }
        return '待审内容 #' . (string) intval($payload['id'] ?? 0);
    }

    protected function buildSubjectTitle(string $type, array $payload, int $itemId): string
    {
        if (!empty($payload['title'])) {
            return (string) $payload['title'];
        }

        if (in_array($type, ['question_comment', 'answer_comment'], true)) {
            $questionId = intval($payload['question_info']['id'] ?? 0);
            if (!$questionId && !empty($payload['item_id']) && $type === 'answer_comment') {
                $questionId = intval(db('answer')->where('id', intval($payload['item_id']))->value('question_id'));
            }
            if ($questionId > 0) {
                return (string) (db('question')->where('id', $questionId)->value('title') ?: '');
            }
        }

        if ($type === 'article_comment') {
            $articleId = intval($payload['item_id'] ?? $itemId);
            if ($articleId > 0) {
                return (string) (db('article')->where('id', $articleId)->value('title') ?: '');
            }
        }

        if (in_array($type, ['answer', 'modify_answer'], true)) {
            $questionId = intval($payload['question_id'] ?? 0);
            if ($questionId > 0) {
                return (string) (db('question')->where('id', $questionId)->value('title') ?: '');
            }
        }

        return '';
    }

    protected function buildPreviewFields(string $type, array $payload, int $uid): array
    {
        $fields = [];

        if (in_array($type, ['question', 'modify_question'], true)) {
            $fields[] = ['label' => '问题标题', 'value' => (string) ($payload['title'] ?? '-')];
            $fields[] = ['label' => '问题详情', 'value' => strip_tags(htmlspecialchars_decode((string) ($payload['detail'] ?? '')))];
            $fields[] = ['label' => '问题类型', 'value' => (string) (($payload['question_type'] ?? 'normal') === 'reward' ? '悬赏问题' : '普通问题')];
            $fields[] = ['label' => '匿名状态', 'value' => intval($payload['is_anonymous'] ?? 0) === 1 ? '匿名' : '公开'];
            $fields[] = ['label' => '推荐状态', 'value' => intval($payload['is_recommend'] ?? 0) === 1 ? '推荐' : '普通'];
            $fields[] = ['label' => '置顶状态', 'value' => intval($payload['set_top'] ?? 0) === 1 ? '已置顶' : '未置顶'];
            if (!empty($payload['category_id'])) {
                $fields[] = ['label' => '问题分类', 'value' => (string) (db('category')->where('id', intval($payload['category_id']))->value('title') ?: $payload['category_id'])];
            }
            $fields = array_merge($fields, $this->appendTopicFields($payload, 'question', intval($payload['id'] ?? 0)));
        } elseif (in_array($type, ['article', 'modify_article'], true)) {
            $fields[] = ['label' => '文章标题', 'value' => (string) ($payload['title'] ?? '-')];
            $fields[] = ['label' => '文章详情', 'value' => strip_tags(htmlspecialchars_decode((string) ($payload['message'] ?? '')))];
            $fields[] = ['label' => '推荐状态', 'value' => intval($payload['is_recommend'] ?? 0) === 1 ? '推荐' : '普通'];
            $fields[] = ['label' => '置顶状态', 'value' => intval($payload['set_top'] ?? 0) === 1 ? '已置顶' : '未置顶'];
            if (!empty($payload['category_id'])) {
                $fields[] = ['label' => '文章分类', 'value' => (string) (db('category')->where('id', intval($payload['category_id']))->value('title') ?: $payload['category_id'])];
            }
            if (!empty($payload['column_id'])) {
                $fields[] = ['label' => '文章专栏', 'value' => (string) (db('column')->where('id', intval($payload['column_id']))->value('name') ?: $payload['column_id'])];
            }
            if (!empty($payload['cover'])) {
                $fields[] = ['label' => '文章封面', 'value' => (string) $payload['cover']];
            }
            $fields = array_merge($fields, $this->appendTopicFields($payload, 'article', intval($payload['id'] ?? 0)));
        } elseif ($type === 'topic') {
            $fields[] = ['label' => '话题标题', 'value' => (string) ($payload['title'] ?? '-')];
        } elseif (in_array($type, ['answer', 'modify_answer'], true)) {
            $questionTitle = '';
            if (!empty($payload['question_id'])) {
                $questionTitle = (string) (db('question')->where('id', intval($payload['question_id']))->value('title') ?: '');
            }
            $fields[] = ['label' => '所属问题', 'value' => $questionTitle ?: '-'];
            $fields[] = ['label' => '回答详情', 'value' => strip_tags(htmlspecialchars_decode((string) ($payload['content'] ?? '')))];
            $fields[] = ['label' => '匿名状态', 'value' => intval($payload['is_anonymous'] ?? 0) === 1 ? '匿名' : '公开'];
            $fields[] = ['label' => '回答类型', 'value' => intval($payload['is_best'] ?? 0) === 1 ? '最佳回答' : '普通回答'];
        } elseif (str_contains($type, 'comment')) {
            $fields[] = ['label' => '关联标题', 'value' => $this->buildSubjectTitle($type, $payload, intval($payload['item_id'] ?? 0)) ?: '-'];
            $fields[] = ['label' => '评论内容', 'value' => strip_tags(htmlspecialchars_decode((string) ($payload['message'] ?? '')))];
        }

        if (!empty($payload['is_agent_content'])) {
            $fields[] = ['label' => '提交来源', 'value' => 'Agent'];
            $fields[] = ['label' => 'Agent 展示名', 'value' => (string) ($payload['agent_display_name'] ?? '-')];
            $fields[] = ['label' => 'Agent 用户名', 'value' => (string) ($payload['agent_user_name'] ?? '-')];
            $fields[] = ['label' => 'Agent 等级', 'value' => 'L' . max(0, intval($payload['agent_level_snapshot'] ?? 0))];
            $fields[] = ['label' => 'Agent 徽标', 'value' => (string) ($payload['agent_badge_snapshot'] ?? '-')];
            $fields[] = ['label' => '协议版本', 'value' => (string) ($payload['protocol_version'] ?? 'v1')];
            $fields[] = ['label' => '提交时间', 'value' => !empty($payload['submitted_at']) ? date('Y-m-d H:i:s', intval($payload['submitted_at'])) : '-'];
        }

        $result = [];
        foreach ($fields as $field) {
            $value = trim((string) ($field['value'] ?? ''));
            if ($value === '') {
                continue;
            }
            $result[] = [
                'label' => (string) ($field['label'] ?? ''),
                'value' => $value,
            ];
        }

        return $result;
    }

    protected function appendTopicFields(array $payload, string $itemType, int $fallbackItemId = 0): array
    {
        $itemId = intval($payload['id'] ?? 0);
        if ($itemId <= 0) {
            $itemId = $fallbackItemId;
        }

        if ($itemId <= 0) {
            return [];
        }

        $topicIds = db('topic_relation')
            ->where([
                'item_type' => $itemType,
                'item_id' => $itemId,
                'status' => 1,
            ])
            ->column('topic_id');

        if (!$topicIds) {
            return [];
        }

        $topicTitles = array_values(array_filter(db('topic')->whereIn('id', $topicIds)->column('title')));
        if (!$topicTitles) {
            return [];
        }

        return [
            ['label' => '关联话题', 'value' => implode(' / ', $topicTitles)],
        ];
    }

    protected function buildTargetUrl(string $type, array $payload, int $itemId): string
    {
        if (in_array($type, ['question', 'modify_question'], true)) {
            $questionId = $itemId > 0 ? $itemId : intval($payload['id'] ?? 0);
            return $questionId > 0 ? get_url('question/detail', ['id' => $questionId], true, false) : '';
        }
        if (in_array($type, ['article', 'modify_article'], true)) {
            $articleId = $itemId > 0 ? $itemId : intval($payload['id'] ?? 0);
            return $articleId > 0 ? get_url('article/detail', ['id' => $articleId], true, false) : '';
        }
        if (in_array($type, ['answer', 'modify_answer'], true)) {
            $questionId = intval($payload['question_id'] ?? 0);
            $answerId = $itemId > 0 ? $itemId : intval($payload['id'] ?? 0);
            return ($questionId > 0 && $answerId > 0)
                ? get_url('question/detail', ['id' => $questionId, 'answer' => $answerId], true, false)
                : '';
        }
        return '';
    }

    protected function buildContentReview(string $type, array $payload): array
    {
        if (!in_array($type, ['article', 'modify_article'], true)) {
            return [];
        }

        $title = trim((string) ($payload['title'] ?? ''));
        $message = htmlspecialchars_decode((string) ($payload['message'] ?? ''));
        $plainText = trim(preg_replace('/\s+/u', ' ', strip_tags($message)));
        $plainTextChars = function_exists('mb_strlen') ? mb_strlen($plainText, 'UTF-8') : strlen($plainText);
        $paragraphs = preg_match_all('/<p\b/i', $message);
        $headings = preg_match_all('/<h[23]\b/i', $message);
        $listItems = preg_match_all('/<li\b/i', $message);
        $categoryId = intval($payload['category_id'] ?? 0);
        $cover = trim((string) ($payload['cover'] ?? ''));
        $articleType = trim((string) ($payload['article_type'] ?? ''));

        $score = 0;
        $issues = [];

        if ($title !== '') {
            $score += 10;
        } else {
            $issues[] = '缺少标题';
        }

        if ($categoryId > 0) {
            $score += 10;
            $categoryStatus = '已设置，需人工确认是否准确';
        } else {
            $categoryStatus = '缺失';
            $issues[] = '缺少分类';
        }

        if ($cover !== '') {
            $score += 12;
            $coverStatus = '完整';
        } else {
            $coverStatus = '缺失';
            $issues[] = '缺少封面';
        }

        if ($articleType !== '') {
            $score += 5;
        } else {
            $issues[] = '缺少 article_type';
        }

        if ($plainTextChars >= 1800) {
            $score += 35;
        } elseif ($plainTextChars >= 1200) {
            $score += 30;
        } elseif ($plainTextChars >= 900) {
            $score += 24;
        } elseif ($plainTextChars >= 600) {
            $score += 15;
            $issues[] = '正文长度偏短';
        } else {
            $score += 5;
            $issues[] = '正文长度明显不足';
        }

        $score += min(10, intval($paragraphs) * 2);
        if (intval($paragraphs) < 5) {
            $issues[] = '段落数量不足';
        }

        $score += min(10, intval($headings) * 5);
        if (intval($headings) < 2) {
            $issues[] = '小标题不足';
        }

        $score += min(8, intval($listItems) * 2);
        if (intval($listItems) < 3) {
            $issues[] = '列表项不足';
        }

        $score = min(100, $score);
        if ($score >= 85) {
            $recommendation = '可优先审核';
        } elseif ($score >= 70) {
            $recommendation = '可审核，但建议人工复核分类与封面';
        } elseif ($score >= 50) {
            $recommendation = '建议先复核后再决定是否通过';
        } else {
            $recommendation = '建议拒审或删除后重提';
        }

        return [
            'score' => intval($score),
            'recommendation' => $recommendation,
            'completeness' => [
                'category' => [
                    'status' => $categoryStatus,
                    'value' => $categoryId > 0
                        ? (string) (db('category')->where('id', $categoryId)->value('title') ?: $categoryId)
                        : '',
                ],
                'cover' => [
                    'status' => $coverStatus,
                    'value' => $cover,
                ],
                'article_type' => $articleType,
            ],
            'metrics' => [
                'plain_text_chars' => intval($plainTextChars),
                'paragraphs' => intval($paragraphs),
                'headings' => intval($headings),
                'list_items' => intval($listItems),
            ],
            'issues' => array_values(array_unique($issues)),
        ];
    }

    protected function normalizeIds($ids): array
    {
        $ids = is_array($ids) ? $ids : explode(',', (string) $ids);
        return array_values(array_filter(array_map('intval', $ids)));
    }
}
