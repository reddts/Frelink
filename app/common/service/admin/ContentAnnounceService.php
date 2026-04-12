<?php

namespace app\common\service\admin;

class ContentAnnounceService
{
    public function getOverview(int $status = -1, string $keyword = ''): array
    {
        $activeStatus = in_array($status, [-1, 0, 1], true) ? $status : -1;

        return [
            'status' => $activeStatus,
            'tabs' => [
                ['label' => '全部', 'value' => -1],
                ['label' => '启用', 'value' => 1],
                ['label' => '禁用', 'value' => 0],
            ],
            'list' => $this->getList($activeStatus, $keyword),
        ];
    }

    public function getDetail(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        $info = db('announce')
            ->alias('a')
            ->leftJoin('users u', 'a.uid = u.uid')
            ->where('a.id', $id)
            ->field('a.*,u.user_name,u.nick_name,u.url_token')
            ->find();

        if (!$info) {
            return [];
        }

        return [
            'id' => intval($info['id'] ?? 0),
            'uid' => intval($info['uid'] ?? 0),
            'title' => (string) ($info['title'] ?? ''),
            'message' => htmlspecialchars_decode((string) ($info['message'] ?? '')),
            'status' => intval($info['status'] ?? 1),
            'set_top' => intval($info['set_top'] ?? 0),
            'sort' => intval($info['sort'] ?? 0),
            'view_count' => intval($info['view_count'] ?? 0),
            'user_name' => (string) (($info['user_name'] ?? '') ?: ($info['nick_name'] ?? '')),
            'url_token' => (string) ($info['url_token'] ?? ''),
            'preview_url' => get_url('announce/detail', ['id' => intval($info['id'] ?? 0)], true, false),
            'create_time_text' => !empty($info['create_time']) ? date('Y-m-d H:i:s', intval($info['create_time'])) : '-',
            'update_time_text' => !empty($info['update_time']) ? date('Y-m-d H:i:s', intval($info['update_time'])) : '-',
            'flags' => $this->buildFlags($info),
            'detail_fields' => $this->buildDetailFields($info),
        ];
    }

    public function save(array $data): array
    {
        $id = intval($data['id'] ?? 0);
        if ($id <= 0) {
            return ['code' => 0, 'msg' => '公告不存在'];
        }

        $current = db('announce')->where('id', $id)->find();
        if (!$current) {
            return ['code' => 0, 'msg' => '公告不存在'];
        }

        $title = trim((string) ($data['title'] ?? ''));
        $message = trim((string) ($data['message'] ?? ''));
        if ($title === '') {
            return ['code' => 0, 'msg' => '公告标题不能为空'];
        }
        if ($message === '') {
            return ['code' => 0, 'msg' => '公告内容不能为空'];
        }

        $setTop = array_key_exists('set_top', $data) ? intval($data['set_top']) : intval($current['set_top'] ?? 0);
        $updated = db('announce')->where('id', $id)->update([
            'title' => $title,
            'message' => htmlspecialchars($message, ENT_QUOTES),
            'sort' => intval($data['sort'] ?? ($current['sort'] ?? 0)),
            'status' => array_key_exists('status', $data) ? intval($data['status']) : intval($current['status'] ?? 1),
            'set_top' => $setTop,
            'set_top_time' => $setTop === 1 ? time() : 0,
            'update_time' => time(),
        ]);

        if ($updated === false) {
            return ['code' => 0, 'msg' => '保存失败'];
        }

        return ['code' => 1, 'msg' => '保存成功', 'data' => ['id' => $id]];
    }

    public function delete($ids): array
    {
        $normalized = $this->normalizeIds($ids);
        if (!$normalized) {
            return ['code' => 0, 'msg' => '请选择要删除的公告'];
        }

        $deleted = db('announce')->whereIn('id', $normalized)->delete();
        if ($deleted === false) {
            return ['code' => 0, 'msg' => '删除失败'];
        }

        return ['code' => 1, 'msg' => '删除成功'];
    }

    protected function getList(int $status, string $keyword): array
    {
        $query = db('announce')
            ->alias('a')
            ->leftJoin('users u', 'a.uid = u.uid')
            ->field('a.id,a.uid,a.title,a.status,a.set_top,a.sort,a.view_count,a.create_time,a.update_time,u.user_name,u.nick_name,u.url_token');

        if (in_array($status, [0, 1], true)) {
            $query->where('a.status', $status);
        }

        if ($keyword !== '') {
            $query->whereLike('a.title', '%' . $keyword . '%');
        }

        $list = $query->order(['a.set_top_time' => 'desc', 'a.sort' => 'desc', 'a.id' => 'desc'])->select()->toArray();
        foreach ($list as &$item) {
            $item['id'] = intval($item['id'] ?? 0);
            $item['uid'] = intval($item['uid'] ?? 0);
            $item['title'] = (string) ($item['title'] ?? '');
            $item['status'] = intval($item['status'] ?? 1);
            $item['set_top'] = intval($item['set_top'] ?? 0);
            $item['sort'] = intval($item['sort'] ?? 0);
            $item['view_count'] = intval($item['view_count'] ?? 0);
            $item['user_name'] = (string) (($item['user_name'] ?? '') ?: ($item['nick_name'] ?? ''));
            $item['url_token'] = (string) ($item['url_token'] ?? '');
            $item['preview_url'] = get_url('announce/detail', ['id' => $item['id']], true, false);
            $item['create_time_text'] = !empty($item['create_time']) ? date('Y-m-d H:i:s', intval($item['create_time'])) : '-';
            $item['update_time_text'] = !empty($item['update_time']) ? date('Y-m-d H:i:s', intval($item['update_time'])) : '-';
            $item['flags'] = $this->buildFlags($item);
        }
        unset($item);

        return $list;
    }

    protected function normalizeIds($ids): array
    {
        $ids = is_array($ids) ? $ids : explode(',', (string) $ids);
        return array_values(array_filter(array_map('intval', $ids)));
    }

    protected function buildFlags(array $info): array
    {
        $flags = [];
        if (intval($info['set_top'] ?? 0) === 1) {
            $flags[] = '置顶';
        }
        if (intval($info['status'] ?? 1) !== 1) {
            $flags[] = '已禁用';
        }
        return $flags;
    }

    protected function buildDetailFields(array $info): array
    {
        return [
            ['label' => '发布用户', 'value' => (string) ((($info['user_name'] ?? '') ?: ($info['nick_name'] ?? '')) ?: '-')],
            ['label' => '浏览数', 'value' => (string) intval($info['view_count'] ?? 0)],
            ['label' => '排序值', 'value' => (string) intval($info['sort'] ?? 0)],
            ['label' => '状态', 'value' => intval($info['status'] ?? 1) === 1 ? '启用' : '禁用'],
            ['label' => '创建时间', 'value' => !empty($info['create_time']) ? date('Y-m-d H:i:s', intval($info['create_time'])) : '-'],
            ['label' => '更新时间', 'value' => !empty($info['update_time']) ? date('Y-m-d H:i:s', intval($info['update_time'])) : '-'],
        ];
    }
}
