<?php

namespace app\common\service\admin;

use app\model\Answer as AnswerModel;

class ContentAnswerService
{
    public function getOverview(int $status = 1): array
    {
        return [
            'status' => $status === 0 ? 0 : 1,
            'tabs' => [
                ['label' => '列表', 'value' => 1],
                ['label' => '已删除', 'value' => 0],
            ],
            'list' => $this->getList($status),
        ];
    }

    public function getDetail(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        $info = db('answer')
            ->alias('a')
            ->leftJoin('users u', 'a.uid = u.uid')
            ->leftJoin('question q', 'a.question_id = q.id')
            ->where('a.id', $id)
            ->field('a.*,u.nick_name,u.url_token,q.title as question_title')
            ->find();

        if (!$info) {
            return [];
        }

        return [
            'id' => intval($info['id'] ?? 0),
            'uid' => intval($info['uid'] ?? 0),
            'question_id' => intval($info['question_id'] ?? 0),
            'question_title' => (string) ($info['question_title'] ?? ''),
            'nick_name' => (string) ($info['nick_name'] ?? ''),
            'url_token' => (string) ($info['url_token'] ?? ''),
            'content' => htmlspecialchars_decode((string) ($info['content'] ?? '')),
            'against_count' => intval($info['against_count'] ?? 0),
            'agree_count' => intval($info['agree_count'] ?? 0),
            'comment_count' => intval($info['comment_count'] ?? 0),
            'is_best' => intval($info['is_best'] ?? 0),
            'status' => intval($info['status'] ?? 0),
            'preview_url' => get_url('question/detail', ['id' => intval($info['question_id'] ?? 0), 'answer' => intval($info['id'] ?? 0)], true, false),
            'create_time_text' => !empty($info['create_time']) ? date('Y-m-d H:i:s', intval($info['create_time'])) : '-',
            'update_time_text' => !empty($info['update_time']) ? date('Y-m-d H:i:s', intval($info['update_time'])) : '-',
            'status_label' => intval($info['status'] ?? 0) === 1 ? '正常' : '已删除',
            'best_label' => intval($info['is_best'] ?? 0) === 1 ? '最佳回答' : '普通回答',
            'detail_fields' => $this->buildDetailFields($info),
        ];
    }

    public function save(array $data): array
    {
        $id = intval($data['id'] ?? 0);
        if ($id <= 0) {
            return ['code' => 0, 'msg' => '回答不存在'];
        }

        $updated = db('answer')->where('id', $id)->update([
            'content' => (string) ($data['content'] ?? ''),
            'update_time' => time(),
        ]);

        if ($updated === false) {
            return ['code' => 0, 'msg' => '保存失败'];
        }

        return ['code' => 1, 'msg' => '保存成功', 'data' => ['id' => $id]];
    }

    public function delete($ids, bool $real = false): array
    {
        if (!AnswerModel::deleteAnswer($this->normalizeIds($ids), $real)) {
            return ['code' => 0, 'msg' => '删除失败'];
        }

        return ['code' => 1, 'msg' => '删除成功'];
    }

    protected function getList(int $status): array
    {
        $list = db('answer')
            ->alias('a')
            ->leftJoin('users u', 'a.uid = u.uid')
            ->leftJoin('question q', 'a.question_id = q.id')
            ->where('a.status', $status === 0 ? 0 : 1)
            ->field('a.id,a.uid,a.question_id,a.content,a.against_count,a.agree_count,a.comment_count,a.is_best,a.create_time,a.update_time,u.nick_name,u.url_token,q.title')
            ->order(['a.id' => 'desc'])
            ->select()
            ->toArray();

        foreach ($list as &$item) {
            $item['id'] = intval($item['id'] ?? 0);
            $item['uid'] = intval($item['uid'] ?? 0);
            $item['question_id'] = intval($item['question_id'] ?? 0);
            $item['against_count'] = intval($item['against_count'] ?? 0);
            $item['agree_count'] = intval($item['agree_count'] ?? 0);
            $item['comment_count'] = intval($item['comment_count'] ?? 0);
            $item['is_best'] = intval($item['is_best'] ?? 0);
            $item['content_preview'] = str_cut(strip_tags(htmlspecialchars_decode((string) ($item['content'] ?? ''))), 0, 100);
            $item['preview_url'] = get_url('question/detail', ['id' => $item['question_id'], 'answer' => $item['id']], true, false);
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

    protected function buildDetailFields(array $info): array
    {
        return [
            ['label' => '问题标题', 'value' => (string) ($info['question_title'] ?? '-')],
            ['label' => '作者', 'value' => (string) ($info['nick_name'] ?? '未知用户')],
            ['label' => '状态', 'value' => intval($info['status'] ?? 0) === 1 ? '正常' : '已删除'],
            ['label' => '回答类型', 'value' => intval($info['is_best'] ?? 0) === 1 ? '最佳回答' : '普通回答'],
            ['label' => '赞同数', 'value' => (string) intval($info['agree_count'] ?? 0)],
            ['label' => '反对数', 'value' => (string) intval($info['against_count'] ?? 0)],
            ['label' => '评论数', 'value' => (string) intval($info['comment_count'] ?? 0)],
            ['label' => '创建时间', 'value' => !empty($info['create_time']) ? date('Y-m-d H:i:s', intval($info['create_time'])) : '-'],
            ['label' => '更新时间', 'value' => !empty($info['update_time']) ? date('Y-m-d H:i:s', intval($info['update_time'])) : '-'],
        ];
    }
}
