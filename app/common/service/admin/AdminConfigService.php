<?php

namespace app\common\service\admin;

class AdminConfigService
{
    public function getOverview(int $groupId = 0, string $keyword = ''): array
    {
        $groups = $this->getGroups();
        $selectedGroupId = $this->resolveSelectedGroupId($groupId, $groups);

        return [
            'group_id' => $selectedGroupId,
            'group_tabs' => $this->buildGroupTabs($groups),
            'list' => $this->getConfigList($selectedGroupId, $keyword),
            'groups' => $groups,
        ];
    }

    public function getGroups(): array
    {
        $groups = db('config_group')
            ->order(['sort' => 'asc', 'id' => 'asc'])
            ->select()
            ->toArray();

        foreach ($groups as &$group) {
            $group = $this->formatGroupRow($group);
        }
        unset($group);

        return $groups;
    }

    protected function getConfigList(int $groupId, string $keyword): array
    {
        $query = db('config')->order(['sort' => 'asc', 'id' => 'asc']);
        if ($groupId > 0) {
            $query->where('group', $groupId);
        }
        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword) {
                $builder
                    ->whereLike('name', '%' . $keyword . '%')
                    ->whereOrLike('title', '%' . $keyword . '%');
            });
        }

        $groups = db('config_group')->column('name', 'id');
        $typeMap = config('app.fieldType');
        $list = $query->select()->toArray();

        foreach ($list as &$item) {
            $item = $this->formatConfigRow($item, $groups, $typeMap);
        }
        unset($item);

        return $list;
    }

    protected function buildGroupTabs(array $groups): array
    {
        $tabs = [
            ['label' => '全部配置', 'value' => 0],
        ];

        foreach ($groups as $group) {
            $tabs[] = [
                'label' => (string) ($group['name'] ?? ''),
                'value' => intval($group['id'] ?? 0),
            ];
        }

        return $tabs;
    }

    protected function resolveSelectedGroupId(int $groupId, array $groups): int
    {
        if ($groupId <= 0) {
            return 0;
        }

        foreach ($groups as $group) {
            if (intval($group['id'] ?? 0) === $groupId) {
                return $groupId;
            }
        }

        return 0;
    }

    protected function formatConfigRow(array $item, array $groups, array $typeMap): array
    {
        $item['id'] = intval($item['id'] ?? 0);
        $item['group'] = intval($item['group'] ?? 0);
        $item['sort'] = intval($item['sort'] ?? 0);
        $item['group_name'] = (string) ($groups[$item['group']] ?? '未分组');
        $item['type_label'] = (string) ($typeMap[$item['type'] ?? ''] ?? ($item['type'] ?? ''));
        $item['name'] = (string) ($item['name'] ?? '');
        $item['title'] = (string) ($item['title'] ?? '');
        $item['tips'] = trim((string) ($item['tips'] ?? ''));
        $item['value_preview'] = $this->buildValuePreview($item);
        return $item;
    }

    protected function formatGroupRow(array $group): array
    {
        $group['id'] = intval($group['id'] ?? 0);
        $group['sort'] = intval($group['sort'] ?? 0);
        $group['status'] = intval($group['status'] ?? 0);
        $group['name'] = (string) ($group['name'] ?? '');
        $group['description'] = trim((string) ($group['description'] ?? ''));
        $group['config_count'] = intval(db('config')->where('group', $group['id'])->count());
        return $group;
    }

    protected function buildValuePreview(array $item): string
    {
        $type = (string) ($item['type'] ?? '');
        $value = $item['value'] ?? '';

        if ($type === 'password') {
            return '******';
        }

        if (in_array($type, ['images', 'files', 'checkbox', 'select2'], true)) {
            return $value === '' ? '' : implode(' / ', array_filter(explode(',', (string) $value)));
        }

        if ($type === 'array') {
            $decoded = json_decode((string) ($item['option'] ?? ''), true);
            return is_array($decoded) ? json_encode($decoded, JSON_UNESCAPED_UNICODE) : '';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        return mb_strimwidth(strip_tags(htmlspecialchars_decode($value)), 0, 120, '...');
    }
}
