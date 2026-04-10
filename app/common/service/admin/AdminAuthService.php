<?php

namespace app\common\service\admin;

use app\model\admin\AdminAuth;

class AdminAuthService
{
    protected ?bool $hasMenuColumn = null;

    public function getTreeList(): array
    {
        $list = db('admin_auth')
            ->order(['sort' => 'asc', 'id' => 'asc'])
            ->select()
            ->toArray();

        $indexed = [];
        foreach ($list as $item) {
            $item['id'] = intval($item['id'] ?? 0);
            $item['pid'] = intval($item['pid'] ?? 0);
            $item['sort'] = intval($item['sort'] ?? 0);
            $item['status'] = intval($item['status'] ?? 0);
            $item['auth_open'] = intval($item['auth_open'] ?? 0);
            $item['menu'] = intval($item['menu'] ?? 1);
            $item['type'] = intval($item['type'] ?? 1);
            $item['children'] = [];
            $indexed[$item['id']] = $item;
        }

        $tree = [];
        foreach ($indexed as $id => $item) {
            if ($item['pid'] > 0 && isset($indexed[$item['pid']])) {
                $indexed[$item['pid']]['children'][] = &$indexed[$id];
            } else {
                $tree[] = &$indexed[$id];
            }
        }

        return array_values($tree);
    }

    public function getDetail(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        $info = db('admin_auth')->find($id);
        if (!$info) {
            return [];
        }

        return $this->formatDetail($info);
    }

    public function getEditorMeta(): array
    {
        return [
            'parent_options' => $this->getParentOptions(),
            'detail_template' => [
                'id' => 0,
                'pid' => 0,
                'icon' => '',
                'name' => '',
                'title' => '',
                'param' => '',
                'auth_open' => 1,
                'status' => 1,
                'sort' => 50,
                'menu' => 1,
                'type' => 1,
            ],
        ];
    }

    public function getParentOptions(): array
    {
        $options = AdminAuth::getPidOptions();
        $result = [
            ['label' => '顶级节点', 'value' => 0],
        ];
        foreach ($options as $value => $label) {
            $result[] = [
                'label' => (string) $label,
                'value' => intval($value),
            ];
        }
        return $result;
    }

    public function save(array $data): array
    {
        $payload = $this->normalizePayload($data);
        if ($payload['title'] === '') {
            return ['code' => 0, 'msg' => '菜单名称不能为空'];
        }
        if ($payload['name'] === '') {
            return ['code' => 0, 'msg' => '控制器/方法不能为空'];
        }

        if ($payload['id'] > 0) {
            $updated = db('admin_auth')->where('id', $payload['id'])->update($payload);
            if ($updated === false) {
                return ['code' => 0, 'msg' => '保存失败'];
            }

            return ['code' => 1, 'msg' => '保存成功', 'data' => ['id' => $payload['id']]];
        }

        $id = intval(db('admin_auth')->insertGetId($payload));
        if ($id <= 0) {
            return ['code' => 0, 'msg' => '添加失败'];
        }

        return ['code' => 1, 'msg' => '添加成功', 'data' => ['id' => $id]];
    }

    public function delete(int $id): array
    {
        if ($id <= 0) {
            return ['code' => 0, 'msg' => '参数错误'];
        }

        $info = db('admin_auth')->find($id);
        if (!$info) {
            return ['code' => 0, 'msg' => '权限节点不存在'];
        }

        if (intval(db('admin_auth')->where('pid', $id)->count()) > 0) {
            return ['code' => 0, 'msg' => '请先删除子节点'];
        }

        $deleted = db('admin_auth')->where('id', $id)->delete();
        if (!$deleted) {
            return ['code' => 0, 'msg' => '删除失败'];
        }

        return ['code' => 1, 'msg' => '删除成功'];
    }

    public function toggleState(int $id): array
    {
        if ($id <= 0) {
            return ['code' => 0, 'msg' => '参数错误'];
        }

        $info = db('admin_auth')->find($id);
        if (!$info) {
            return ['code' => 0, 'msg' => '权限节点不存在'];
        }

        $status = intval($info['status'] ?? 0) === 1 ? 0 : 1;
        db('admin_auth')->where('id', $id)->update(['status' => $status]);
        return ['code' => 1, 'msg' => '修改成功'];
    }

    protected function normalizePayload(array $data): array
    {
        $payload = [
            'id' => intval($data['id'] ?? 0),
            'pid' => intval($data['pid'] ?? 0),
            'icon' => trim((string) ($data['icon'] ?? '')),
            'name' => trim((string) ($data['name'] ?? '')),
            'title' => trim((string) ($data['title'] ?? '')),
            'param' => trim((string) ($data['param'] ?? '')),
            'auth_open' => intval($data['auth_open'] ?? 1),
            'status' => intval($data['status'] ?? 1),
            'sort' => intval($data['sort'] ?? 50),
            'type' => intval($data['type'] ?? 1),
        ];

        if ($this->hasMenuColumn()) {
            $payload['menu'] = intval($data['menu'] ?? 1);
        }

        return $payload;
    }

    protected function formatDetail(array $info): array
    {
        return [
            'id' => intval($info['id'] ?? 0),
            'pid' => intval($info['pid'] ?? 0),
            'icon' => (string) ($info['icon'] ?? ''),
            'name' => (string) ($info['name'] ?? ''),
            'title' => (string) ($info['title'] ?? ''),
            'param' => (string) ($info['param'] ?? ''),
            'auth_open' => intval($info['auth_open'] ?? 0),
            'status' => intval($info['status'] ?? 0),
            'sort' => intval($info['sort'] ?? 0),
            'menu' => intval($info['menu'] ?? 1),
            'type' => intval($info['type'] ?? 1),
        ];
    }

    protected function hasMenuColumn(): bool
    {
        if ($this->hasMenuColumn !== null) {
            return $this->hasMenuColumn;
        }

        $prefix = app()->db->getConfig('connections.mysql.prefix');
        $table = $prefix . 'admin_auth';
        $database = app()->db->getConfig('connections.mysql.database');

        $result = \think\facade\Db::query(
            "SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :database AND TABLE_NAME = :table AND COLUMN_NAME = 'menu'",
            ['database' => $database, 'table' => $table]
        );

        $this->hasMenuColumn = intval($result[0]['count'] ?? 0) > 0;
        return $this->hasMenuColumn;
    }
}
