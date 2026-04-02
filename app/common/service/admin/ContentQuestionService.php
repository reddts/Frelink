<?php

namespace app\common\service\admin;

use app\model\Question as QuestionModel;

class ContentQuestionService
{
    public function getOverview(int $status = 1, string $keyword = ''): array
    {
        return [
            'status' => $status === 0 ? 0 : 1,
            'tabs' => [
                ['label' => '列表', 'value' => 1],
                ['label' => '已删除', 'value' => 0],
            ],
            'list' => $this->getList($status, $keyword),
        ];
    }

    public function getDetail(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        $info = db('question')
            ->alias('q')
            ->leftJoin('users u', 'q.uid = u.uid')
            ->where('q.id', $id)
            ->field('q.*,u.user_name,u.url_token')
            ->find();

        if (!$info) {
            return [];
        }

        return [
            'id' => intval($info['id'] ?? 0),
            'uid' => intval($info['uid'] ?? 0),
            'title' => (string) ($info['title'] ?? ''),
            'detail' => htmlspecialchars_decode((string) ($info['detail'] ?? '')),
            'seo_title' => (string) ($info['seo_title'] ?? ''),
            'seo_keywords' => (string) ($info['seo_keywords'] ?? ''),
            'seo_description' => (string) ($info['seo_description'] ?? ''),
            'user_name' => (string) ($info['user_name'] ?? ''),
            'url_token' => (string) ($info['url_token'] ?? ''),
            'status' => intval($info['status'] ?? 0),
            'answer_count' => intval($info['answer_count'] ?? 0),
            'comment_count' => intval($info['comment_count'] ?? 0),
            'view_count' => intval($info['view_count'] ?? 0),
            'create_time_text' => !empty($info['create_time']) ? date('Y-m-d H:i:s', intval($info['create_time'])) : '-',
            'update_time_text' => !empty($info['update_time']) ? date('Y-m-d H:i:s', intval($info['update_time'])) : '-',
        ];
    }

    public function saveSeo(array $data): array
    {
        $id = intval($data['id'] ?? 0);
        if ($id <= 0) {
            return ['code' => 0, 'msg' => '问题不存在'];
        }

        $updated = QuestionModel::update([
            'id' => $id,
            'seo_title' => trim((string) ($data['seo_title'] ?? '')),
            'seo_keywords' => trim((string) ($data['seo_keywords'] ?? '')),
            'seo_description' => trim((string) ($data['seo_description'] ?? '')),
        ]);

        if (!$updated) {
            return ['code' => 0, 'msg' => '保存失败'];
        }

        return ['code' => 1, 'msg' => '保存成功', 'data' => ['id' => $id]];
    }

    public function delete($ids): array
    {
        if (!QuestionModel::removeQuestion($this->normalizeIds($ids))) {
            return ['code' => 0, 'msg' => QuestionModel::getError() ?: '删除失败'];
        }

        return ['code' => 1, 'msg' => '删除成功'];
    }

    public function manager($ids, string $type): array
    {
        $normalized = $this->normalizeIds($ids);
        if (!$normalized) {
            return ['code' => 0, 'msg' => '请选择要操作的数据'];
        }

        if ($type === 'recover') {
            if (!QuestionModel::recordQuestion($normalized)) {
                return ['code' => 0, 'msg' => QuestionModel::getError() ?: '恢复失败'];
            }
            return ['code' => 1, 'msg' => '恢复成功'];
        }

        if ($type === 'remove') {
            if (!QuestionModel::removeQuestion($normalized, true)) {
                return ['code' => 0, 'msg' => QuestionModel::getError() ?: '删除失败'];
            }
            return ['code' => 1, 'msg' => '删除成功'];
        }

        return ['code' => 0, 'msg' => '操作类型错误'];
    }

    protected function getList(int $status, string $keyword): array
    {
        $query = db('question')
            ->alias('q')
            ->leftJoin('users u', 'q.uid = u.uid')
            ->where('q.status', $status === 0 ? 0 : 1)
            ->field('q.id,q.uid,q.title,q.answer_count,q.comment_count,q.view_count,q.create_time,q.update_time,u.user_name,u.url_token');

        if ($keyword !== '') {
            $query->whereLike('q.title', '%' . $keyword . '%');
        }

        $list = $query->order(['q.id' => 'desc'])->select()->toArray();
        foreach ($list as &$item) {
            $item['id'] = intval($item['id'] ?? 0);
            $item['uid'] = intval($item['uid'] ?? 0);
            $item['answer_count'] = intval($item['answer_count'] ?? 0);
            $item['comment_count'] = intval($item['comment_count'] ?? 0);
            $item['view_count'] = intval($item['view_count'] ?? 0);
            $item['create_time_text'] = !empty($item['create_time']) ? date('Y-m-d H:i:s', intval($item['create_time'])) : '-';
            $item['update_time_text'] = !empty($item['update_time']) ? date('Y-m-d H:i:s', intval($item['update_time'])) : '-';
        }
        unset($item);

        return $list;
    }

    protected function normalizeIds($ids): array
    {
        $ids = is_array($ids) ? $ids : explode(',', (string) $ids);
        return array_values(array_filter(array_map('intval', $ids)));
    }
}
