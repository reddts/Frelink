<?php

namespace app\common\service\admin;

use app\model\admin\MenuRule;

class AdminMenuService
{
    public function getTreeList(string $group = 'nav'): array
    {
        $list = db('menu_rule')
            ->where('group', $group)
            ->order(['sort' => 'ASC', 'id' => 'DESC'])
            ->select()
            ->toArray();

        $indexed = [];
        foreach ($list as $item) {
            $item['id'] = intval($item['id']);
            $item['pid'] = intval($item['pid']);
            $item['sort'] = intval($item['sort'] ?? 0);
            $item['status'] = intval($item['status'] ?? 0);
            $item['is_home'] = intval($item['is_home'] ?? 0);
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

        $info = db('menu_rule')->find($id);
        if (!$info) {
            return [];
        }

        $info['id'] = intval($info['id']);
        $info['pid'] = intval($info['pid']);
        $info['sort'] = intval($info['sort'] ?? 0);
        $info['status'] = intval($info['status'] ?? 0);
        $info['is_home'] = intval($info['is_home'] ?? 0);
        $info['type'] = intval($info['type'] ?? 1);
        return $info;
    }

    public function getGroupTabs(): array
    {
        return [
            ['label' => '主导航', 'value' => 'nav'],
            ['label' => '底部导航', 'value' => 'footer'],
        ];
    }

    public function getParentOptions(string $group = 'nav'): array
    {
        $options = MenuRule::getPidOptions($group);
        $result = [
            ['label' => '顶级导航', 'value' => 0],
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
        if (!$payload['title']) {
            return ['code' => 0, 'msg' => '导航名称不能为空'];
        }
        if (!$payload['name']) {
            return ['code' => 0, 'msg' => '导航链接不能为空'];
        }

        if ($payload['is_home'] && $payload['type'] != 1) {
            $payload['is_home'] = 0;
        }

        \think\facade\Db::startTrans();
        try {
            if ($payload['is_home'] && $payload['status'] && $payload['type'] == 1) {
                db('menu_rule')->where(['is_home' => 1])->update(['is_home' => 0]);
            }

            if (!empty($payload['id'])) {
                db('menu_rule')->where('id', intval($payload['id']))->update($payload);
                $id = intval($payload['id']);
            } else {
                $menu = MenuRule::create($payload);
                $id = intval($menu->id ?? 0);
            }

            \think\facade\Db::commit();
            return ['code' => 1, 'msg' => '保存成功', 'data' => ['id' => $id]];
        } catch (\Throwable $e) {
            \think\facade\Db::rollback();
            return ['code' => 0, 'msg' => $e->getMessage() ?: '保存失败'];
        }
    }

    public function delete(int $id): array
    {
        if ($id <= 0) {
            return ['code' => 0, 'msg' => '参数错误'];
        }

        $children = db('menu_rule')->where('pid', $id)->count();
        if ($children > 0) {
            return ['code' => 0, 'msg' => '请先删除子菜单'];
        }

        if (!db('menu_rule')->where('id', $id)->delete()) {
            return ['code' => 0, 'msg' => '删除失败'];
        }

        return ['code' => 1, 'msg' => '删除成功'];
    }

    public function toggleState(int $id, string $field): array
    {
        if ($id <= 0 || !in_array($field, ['status', 'is_home'], true)) {
            return ['code' => 0, 'msg' => '参数错误'];
        }

        $info = db('menu_rule')->find($id);
        if (!$info) {
            return ['code' => 0, 'msg' => '菜单不存在'];
        }

        $status = intval($info[$field] ?? 0) === 1 ? 0 : 1;
        if ($field === 'is_home' && $status && intval($info['type'] ?? 1) != 1) {
            return ['code' => 0, 'msg' => '仅可把站内链接设为默认首页'];
        }

        if ($field === 'is_home' && $status) {
            db('menu_rule')->where(['is_home' => 1])->update(['is_home' => 0]);
        }

        db('menu_rule')->where('id', $id)->update([$field => $status]);
        return ['code' => 1, 'msg' => '修改成功'];
    }

    protected function normalizePayload(array $data): array
    {
        return [
            'id' => intval($data['id'] ?? 0),
            'pid' => intval($data['pid'] ?? 0),
            'group' => trim((string) ($data['group'] ?? 'nav')),
            'icon' => trim((string) ($data['icon'] ?? '')),
            'name' => trim((string) ($data['name'] ?? '')),
            'type' => intval($data['type'] ?? 1),
            'is_home' => intval($data['is_home'] ?? 0),
            'title' => trim((string) ($data['title'] ?? '')),
            'param' => trim((string) ($data['param'] ?? '')),
            'auth_open' => intval($data['auth_open'] ?? 0),
            'status' => intval($data['status'] ?? 1),
            'sort' => intval($data['sort'] ?? 50),
        ];
    }
}
