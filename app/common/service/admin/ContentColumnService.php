<?php

namespace app\common\service\admin;

class ContentColumnService
{
    public function getOverview(int $verify = 1, string $keyword = ''): array
    {
        $activeVerify = in_array($verify, [0, 1, 2], true) ? $verify : 1;

        return [
            'verify' => $activeVerify,
            'tabs' => [
                ['label' => '已审核', 'value' => 1],
                ['label' => '待审核', 'value' => 0],
                ['label' => '已拒绝', 'value' => 2],
            ],
            'list' => $this->getList($activeVerify, $keyword),
        ];
    }

    public function getDetail(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        $info = db('column')
            ->alias('c')
            ->leftJoin('users u', 'c.uid = u.uid')
            ->where('c.id', $id)
            ->field('c.*,u.user_name,u.nick_name,u.url_token')
            ->find();

        if (!$info) {
            return [];
        }

        return [
            'id' => intval($info['id'] ?? 0),
            'uid' => intval($info['uid'] ?? 0),
            'name' => (string) ($info['name'] ?? ''),
            'description' => htmlspecialchars_decode((string) ($info['description'] ?? '')),
            'cover' => (string) ($info['cover'] ?? ''),
            'verify' => intval($info['verify'] ?? 0),
            'recommend' => intval($info['recommend'] ?? 0),
            'sort' => intval($info['sort'] ?? 0),
            'focus_count' => intval($info['focus_count'] ?? 0),
            'view_count' => intval($info['view_count'] ?? 0),
            'post_count' => intval($info['post_count'] ?? 0),
            'user_name' => (string) (($info['user_name'] ?? '') ?: ($info['nick_name'] ?? '')),
            'url_token' => (string) ($info['url_token'] ?? ''),
            'preview_url' => get_url('column/detail', ['id' => intval($info['id'] ?? 0)], true, false),
            'create_time_text' => !empty($info['create_time']) ? date('Y-m-d H:i:s', intval($info['create_time'])) : '-',
            'flags' => $this->buildFlags($info),
            'detail_fields' => $this->buildDetailFields($info),
        ];
    }

    public function save(array $data): array
    {
        $id = intval($data['id'] ?? 0);
        if ($id <= 0) {
            return ['code' => 0, 'msg' => '专栏不存在'];
        }

        $current = db('column')->where('id', $id)->find();
        if (!$current) {
            return ['code' => 0, 'msg' => '专栏不存在'];
        }

        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            return ['code' => 0, 'msg' => '专栏标题不能为空'];
        }

        $updated = db('column')->where('id', $id)->update([
            'name' => $name,
            'cover' => trim((string) ($data['cover'] ?? ($current['cover'] ?? ''))),
            'description' => htmlspecialchars((string) ($data['description'] ?? htmlspecialchars_decode((string) ($current['description'] ?? ''))), ENT_QUOTES),
            'sort' => intval($data['sort'] ?? ($current['sort'] ?? 0)),
            'recommend' => array_key_exists('recommend', $data) ? intval($data['recommend']) : intval($current['recommend'] ?? 0),
            'verify' => array_key_exists('verify', $data) ? intval($data['verify']) : intval($current['verify'] ?? 0),
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
            return ['code' => 0, 'msg' => '请选择要删除的专栏'];
        }

        $deleted = db('column')->whereIn('id', $normalized)->delete();
        if ($deleted === false) {
            return ['code' => 0, 'msg' => '删除失败'];
        }

        return ['code' => 1, 'msg' => '删除成功'];
    }

    public function approve($ids): array
    {
        $normalized = $this->normalizeIds($ids);
        if (!$normalized) {
            return ['code' => 0, 'msg' => '请选择要审核的专栏'];
        }

        foreach ($normalized as $id) {
            $uid = intval(db('column')->where(['id' => $id, 'verify' => 0])->value('uid'));
            if ($uid <= 0) {
                continue;
            }

            db('column')->where('id', $id)->update(['verify' => 1]);
            send_notify(0, $uid, 'TYPE_COLUMN_APPROVAL', 'column', $id);
        }

        return ['code' => 1, 'msg' => '审核通过成功'];
    }

    public function decline($ids): array
    {
        $normalized = $this->normalizeIds($ids);
        if (!$normalized) {
            return ['code' => 0, 'msg' => '请选择要拒绝的专栏'];
        }

        foreach ($normalized as $id) {
            $uid = intval(db('column')->where(['id' => $id, 'verify' => 0])->value('uid'));
            if ($uid <= 0) {
                continue;
            }

            db('column')->where('id', $id)->update(['verify' => 2]);
            send_notify(0, $uid, 'TYPE_COLUMN_DECLINE', 'column', $id);
        }

        return ['code' => 1, 'msg' => '拒绝审核成功'];
    }

    protected function getList(int $verify, string $keyword): array
    {
        $query = db('column')
            ->alias('c')
            ->leftJoin('users u', 'c.uid = u.uid')
            ->where('c.verify', $verify)
            ->field('c.id,c.uid,c.name,c.cover,c.verify,c.recommend,c.sort,c.focus_count,c.view_count,c.post_count,c.create_time,u.user_name,u.nick_name,u.url_token');

        if ($keyword !== '') {
            $query->whereLike('c.name', '%' . $keyword . '%');
        }

        $list = $query->order(['c.id' => 'desc'])->select()->toArray();
        foreach ($list as &$item) {
            $item['id'] = intval($item['id'] ?? 0);
            $item['uid'] = intval($item['uid'] ?? 0);
            $item['name'] = (string) ($item['name'] ?? '');
            $item['cover'] = (string) ($item['cover'] ?? '');
            $item['verify'] = intval($item['verify'] ?? 0);
            $item['recommend'] = intval($item['recommend'] ?? 0);
            $item['sort'] = intval($item['sort'] ?? 0);
            $item['focus_count'] = intval($item['focus_count'] ?? 0);
            $item['view_count'] = intval($item['view_count'] ?? 0);
            $item['post_count'] = intval($item['post_count'] ?? 0);
            $item['user_name'] = (string) (($item['user_name'] ?? '') ?: ($item['nick_name'] ?? ''));
            $item['url_token'] = (string) ($item['url_token'] ?? '');
            $item['preview_url'] = get_url('column/detail', ['id' => $item['id']], true, false);
            $item['create_time_text'] = !empty($item['create_time']) ? date('Y-m-d H:i:s', intval($item['create_time'])) : '-';
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
        if (intval($info['recommend'] ?? 0) === 1) {
            $flags[] = '推荐';
        }

        $verify = intval($info['verify'] ?? 0);
        if ($verify === 0) {
            $flags[] = '待审核';
        } elseif ($verify === 2) {
            $flags[] = '已拒绝';
        }

        return $flags;
    }

    protected function buildDetailFields(array $info): array
    {
        $verify = intval($info['verify'] ?? 0);
        $verifyLabel = '待审核';
        if ($verify === 1) {
            $verifyLabel = '已审核';
        } elseif ($verify === 2) {
            $verifyLabel = '已拒绝';
        }

        return [
            ['label' => '创建用户', 'value' => (string) ((($info['user_name'] ?? '') ?: ($info['nick_name'] ?? '')) ?: '-')],
            ['label' => '审核状态', 'value' => $verifyLabel],
            ['label' => '推荐状态', 'value' => intval($info['recommend'] ?? 0) === 1 ? '推荐' : '不推荐'],
            ['label' => '文章数', 'value' => (string) intval($info['post_count'] ?? 0)],
            ['label' => '关注数', 'value' => (string) intval($info['focus_count'] ?? 0)],
            ['label' => '浏览数', 'value' => (string) intval($info['view_count'] ?? 0)],
            ['label' => '排序值', 'value' => (string) intval($info['sort'] ?? 0)],
            ['label' => '创建时间', 'value' => !empty($info['create_time']) ? date('Y-m-d H:i:s', intval($info['create_time'])) : '-'],
        ];
    }
}
