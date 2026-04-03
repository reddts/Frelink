<?php

namespace app\common\service\admin;

use app\common\library\helper\AuthHelper;

class AdminGroupService
{
    protected AuthHelper $auth;

    public function __construct(?AuthHelper $auth = null)
    {
        $this->auth = $auth ?: AuthHelper::instance();
    }

    public function getList(string $keyword = ''): array
    {
        $query = db('admin_group')->order(['id' => 'asc']);
        if ($keyword !== '') {
            $query->whereLike('title', '%' . $keyword . '%');
        }

        $list = $query->select()->toArray();
        foreach ($list as &$item) {
            $item = $this->formatGroupRow($item);
        }
        unset($item);

        return $list;
    }

    public function getDetail(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        $info = db('admin_group')->find($id);
        if (!$info) {
            return [];
        }

        $info = $this->formatGroupRow($info);
        $info['rule_ids'] = $this->normalizeRuleIds((string) ($info['rules'] ?? ''));
        $info['rule_tree'] = $this->auth->getGroupAuthRule($id);
        return $info;
    }

    public function getCreateMeta(): array
    {
        return [
            'rule_tree' => $this->auth->getGroupAuthRule(),
        ];
    }

    public function save(array $data): array
    {
        $payload = $this->normalizePayload($data);
        if ($payload['title'] === '') {
            return ['code' => 0, 'msg' => '系统组名称不能为空'];
        }

        if ($payload['id'] === 1) {
            $payload['rules'] = '*';
        }

        if ($payload['id'] > 0) {
            $updated = db('admin_group')->where('id', $payload['id'])->update($payload);
            return ['code' => 1, 'msg' => $updated === false ? '保存失败' : '保存成功', 'data' => ['id' => $payload['id']]];
        }

        $payload['permission'] = $this->buildDefaultPermissionJson();
        $id = intval(db('admin_group')->insertGetId($payload));
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

        $group = db('admin_group')->find($id);
        if (!$group) {
            return ['code' => 0, 'msg' => '系统组不存在'];
        }
        if (intval($group['system'] ?? 0) === 1) {
            return ['code' => 0, 'msg' => '删除失败,删除组可能为系统内置组'];
        }

        $deleted = db('admin_group')->where('id', $id)->delete();
        if (!$deleted) {
            return ['code' => 0, 'msg' => '删除失败'];
        }

        return ['code' => 1, 'msg' => '删除成功'];
    }

    protected function formatGroupRow(array $item): array
    {
        $item['id'] = intval($item['id'] ?? 0);
        $item['status'] = intval($item['status'] ?? 0);
        $item['system'] = intval($item['system'] ?? 0);
        $item['rules'] = (string) ($item['rules'] ?? '');
        $item['permission'] = $this->decodePermission((string) ($item['permission'] ?? '{}'));
        $item['rule_count'] = $item['rules'] === '*' ? -1 : count($this->normalizeRuleIds($item['rules']));
        return $item;
    }

    protected function normalizePayload(array $data): array
    {
        $ruleIds = $data['rule_ids'] ?? $data['rules'] ?? [];
        if (!is_array($ruleIds)) {
            $ruleIds = $this->normalizeRuleIds((string) $ruleIds);
        }

        $normalizedRules = [];
        foreach ($ruleIds as $ruleId) {
            $ruleId = intval($ruleId);
            if ($ruleId > 0) {
                $normalizedRules[] = $ruleId;
            }
        }
        $normalizedRules = array_values(array_unique($normalizedRules));

        return [
            'id' => intval($data['id'] ?? 0),
            'title' => trim((string) ($data['title'] ?? '')),
            'status' => intval($data['status'] ?? 0),
            'rules' => !empty($data['rules']) && $data['rules'] === '*' ? '*' : implode(',', $normalizedRules),
        ];
    }

    protected function buildDefaultPermissionJson(): string
    {
        $permission = db('users_permission')->field('name,value')->select()->toArray();
        $permissionMap = [];
        foreach ($permission as $item) {
            $permissionMap[(string) $item['name']] = $item['value'];
        }
        return json_encode($permissionMap, JSON_UNESCAPED_UNICODE);
    }

    protected function normalizeRuleIds(string $rules): array
    {
        $rules = trim($rules);
        if ($rules === '' || $rules === '0') {
            return [];
        }
        if ($rules === '*') {
            return array_map('intval', db('admin_auth')->column('id'));
        }

        return array_values(array_filter(array_map('intval', explode(',', $rules))));
    }

    protected function decodePermission(string $permission): array
    {
        $decoded = json_decode($permission, true);
        return is_array($decoded) ? $decoded : [];
    }
}
