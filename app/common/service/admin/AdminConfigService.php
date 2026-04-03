<?php

namespace app\common\service\admin;

use app\common\library\helper\ArrayHelper;

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

    public function getConfigPage(int $groupId = 0): array
    {
        $groups = $this->getGroups();
        $selectedGroupId = $this->resolveSelectedGroupId($groupId, $groups);
        if ($selectedGroupId <= 0 && !empty($groups)) {
            $selectedGroupId = intval($groups[0]['id'] ?? 0);
        }

        return [
            'group_id' => $selectedGroupId,
            'group_tabs' => $this->buildGroupTabs($groups),
            'fields' => $selectedGroupId > 0 ? $this->buildConfigPageFields($selectedGroupId) : [],
        ];
    }

    public function saveConfigPage(int $groupId, array $payload): array
    {
        if ($groupId <= 0) {
            return ['code' => 0, 'msg' => '配置分组错误'];
        }

        $configs = db('config')
            ->where('group', $groupId)
            ->order(['sort' => 'asc', 'id' => 'asc'])
            ->select()
            ->toArray();

        if (!$configs) {
            return ['code' => 0, 'msg' => '当前分组暂无配置项'];
        }

        $configList = [];
        foreach ($configs as $config) {
            $name = (string) ($config['name'] ?? '');
            if ($name === '' || !array_key_exists($name, $payload)) {
                continue;
            }

            $normalized = $this->normalizeConfigPageValue($config, $payload[$name]);
            $config['value'] = $normalized['value'];
            $config['option'] = $normalized['option'];
            $configList[] = $config;
        }

        if (!$configList) {
            return ['code' => 0, 'msg' => '没有可保存的配置项'];
        }

        $result = db('config')->saveAll($configList);
        if (!$result) {
            return ['code' => 0, 'msg' => '保存失败或数据无变化'];
        }

        return ['code' => 1, 'msg' => '修改成功'];
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

    public function getDetail(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        $info = db('config')->find($id);
        if (!$info) {
            return [];
        }

        return $this->formatConfigDetail($info);
    }

    public function getEditorMeta(): array
    {
        return [
            'group_options' => $this->buildGroupTabs($this->getGroups()),
            'type_options' => $this->buildTypeOptions(),
            'dictionary_options' => $this->buildDictionaryOptions(),
            'detail_template' => [
                'id' => 0,
                'group' => 0,
                'type' => 'text',
                'name' => '',
                'title' => '',
                'value' => '',
                'tips' => '',
                'sort' => 0,
                'dict_code' => 0,
                'source' => 0,
                'option_text' => '',
                'settings' => [],
            ],
        ];
    }

    public function saveConfig(array $data): array
    {
        $payload = $this->normalizeConfigPayload($data);
        if ($payload['name'] === '') {
            return ['code' => 0, 'msg' => '变量名不能为空'];
        }
        if ($payload['title'] === '') {
            return ['code' => 0, 'msg' => '配置标题不能为空'];
        }
        if ($payload['type'] === '') {
            return ['code' => 0, 'msg' => '配置类型不能为空'];
        }

        $dbPayload = [
            'group' => $payload['group'],
            'type' => $payload['type'],
            'name' => $payload['name'],
            'title' => $payload['title'],
            'value' => $payload['value'],
            'dict_code' => $payload['dict_code'],
            'option' => $payload['dict_code'] > 0 ? '' : json_encode(ArrayHelper::strToArr($payload['option_text']), JSON_UNESCAPED_UNICODE),
            'tips' => $payload['tips'],
            'sort' => $payload['sort'],
            'settings' => json_encode($payload['settings'], JSON_UNESCAPED_UNICODE),
        ];

        if ($payload['id'] > 0) {
            $updated = db('config')->where('id', $payload['id'])->update($dbPayload);
            if ($updated === false) {
                return ['code' => 0, 'msg' => '保存失败'];
            }

            return ['code' => 1, 'msg' => '保存成功', 'data' => ['id' => $payload['id']]];
        }

        $id = intval(db('config')->insertGetId($dbPayload));
        if ($id <= 0) {
            return ['code' => 0, 'msg' => '添加失败'];
        }

        return ['code' => 1, 'msg' => '添加成功', 'data' => ['id' => $id]];
    }

    public function deleteConfig(int $id): array
    {
        if ($id <= 0) {
            return ['code' => 0, 'msg' => '参数错误'];
        }

        $info = db('config')->find($id);
        if (!$info) {
            return ['code' => 0, 'msg' => '配置不存在'];
        }

        $deleted = db('config')->where('id', $id)->delete();
        if (!$deleted) {
            return ['code' => 0, 'msg' => '删除失败'];
        }

        return ['code' => 1, 'msg' => '删除成功'];
    }

    public function getGroupDetail(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        $group = db('config_group')->find($id);
        if (!$group) {
            return [];
        }

        return $this->formatGroupRow($group);
    }

    public function saveGroup(array $data): array
    {
        $payload = [
            'id' => intval($data['id'] ?? 0),
            'name' => trim((string) ($data['name'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'sort' => intval($data['sort'] ?? 0),
            'status' => intval($data['status'] ?? 1),
        ];

        if ($payload['name'] === '') {
            return ['code' => 0, 'msg' => '分组名称不能为空'];
        }

        $dbPayload = [
            'name' => $payload['name'],
            'description' => $payload['description'],
            'sort' => $payload['sort'],
            'status' => $payload['status'],
        ];

        if ($payload['id'] > 0) {
            $dbPayload['update_time'] = time();
            $updated = db('config_group')->where('id', $payload['id'])->update($dbPayload);
            if ($updated === false) {
                return ['code' => 0, 'msg' => '保存失败'];
            }

            return ['code' => 1, 'msg' => '保存成功', 'data' => ['id' => $payload['id']]];
        }

        $dbPayload['create_time'] = time();
        $id = intval(db('config_group')->insertGetId($dbPayload));
        if ($id <= 0) {
            return ['code' => 0, 'msg' => '添加失败'];
        }

        return ['code' => 1, 'msg' => '添加成功', 'data' => ['id' => $id]];
    }

    public function deleteGroup(int $id): array
    {
        if ($id <= 0) {
            return ['code' => 0, 'msg' => '参数错误'];
        }

        $group = db('config_group')->find($id);
        if (!$group) {
            return ['code' => 0, 'msg' => '配置分组不存在'];
        }

        $count = intval(db('config')->where('group', $id)->count());
        if ($count > 0) {
            return ['code' => 0, 'msg' => '请先迁移或删除分组下的配置项'];
        }

        $deleted = db('config_group')->where('id', $id)->delete();
        if (!$deleted) {
            return ['code' => 0, 'msg' => '删除失败'];
        }

        return ['code' => 1, 'msg' => '删除成功'];
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

    protected function formatConfigDetail(array $item): array
    {
        $typeMap = config('app.fieldType');
        $groups = db('config_group')->column('name', 'id');
        $item = $this->formatConfigRow($item, $groups, $typeMap);
        $item['dict_code'] = intval($item['dict_code'] ?? 0);
        $item['source'] = $item['dict_code'] > 0 ? 1 : 0;
        $item['option_text'] = $this->formatOptionText((string) ($item['option'] ?? ''));
        $item['settings'] = $this->decodeSettings($item['settings'] ?? '');
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

    protected function formatOptionText(string $option): string
    {
        $decoded = json_decode($option, true);
        if (!is_array($decoded)) {
            return '';
        }

        return ArrayHelper::arrToStr($decoded);
    }

    protected function decodeSettings($settings): array
    {
        if (is_array($settings)) {
            return $settings;
        }

        $decoded = json_decode((string) $settings, true);
        return is_array($decoded) ? $decoded : [];
    }

    protected function buildTypeOptions(): array
    {
        $options = [];
        foreach (config('app.fieldType') as $value => $label) {
            $options[] = [
                'label' => (string) $label,
                'value' => (string) $value,
            ];
        }
        return $options;
    }

    protected function buildConfigPageFields(int $groupId): array
    {
        $list = db('config')
            ->where('group', $groupId)
            ->order(['sort' => 'asc', 'id' => 'asc'])
            ->select()
            ->toArray();

        $fields = [];
        foreach ($list as $item) {
            $fields[] = $this->formatConfigPageField($item);
        }

        return $fields;
    }

    protected function formatConfigPageField(array $item): array
    {
        $type = (string) ($item['type'] ?? 'text');
        $name = (string) ($item['name'] ?? '');
        $title = (string) ($item['title'] ?? $name);
        $tips = trim((string) ($item['tips'] ?? ''));

        if ($type !== 'html') {
            $tips = ($tips ? $tips . '; ' : '') . "调用方式：get_setting('{$name}')";
        }

        $options = [];
        if (!empty($item['dict_code'])) {
            $options = $this->mapOptions(db('dict')->where(['dict_id' => intval($item['dict_code'])])->column('name', 'value'));
        } else {
            $decoded = json_decode((string) ($item['option'] ?? ''), true);
            if (is_array($decoded) && in_array($type, ['radio', 'checkbox', 'select', 'select2'], true)) {
                $options = $this->mapOptions($decoded);
            }
        }

        return [
            'id' => intval($item['id'] ?? 0),
            'name' => $name,
            'title' => $title,
            'type' => $type,
            'tips' => $tips,
            'widget' => $this->resolveConfigWidget($type),
            'multiple' => in_array($type, ['checkbox', 'select2', 'images', 'files'], true),
            'options' => $options,
            'value' => $this->normalizeConfigPageDisplayValue($item),
        ];
    }

    protected function resolveConfigWidget(string $type): string
    {
        if (in_array($type, ['textarea', 'editor', 'code', 'html'], true)) {
            return 'textarea';
        }
        if ($type === 'number') {
            return 'number';
        }
        if (in_array($type, ['radio', 'select'], true)) {
            return 'select';
        }
        if (in_array($type, ['checkbox', 'select2'], true)) {
            return 'multi-select';
        }
        if ($type === 'array') {
            return 'key-value';
        }
        if (in_array($type, ['images', 'files'], true)) {
            return 'list-text';
        }
        if ($type === 'bool') {
            return 'boolean';
        }
        return 'text';
    }

    protected function normalizeConfigPageDisplayValue(array $item)
    {
        $type = (string) ($item['type'] ?? '');
        $value = $item['value'] ?? '';

        if (in_array($type, ['editor', 'textarea', 'code', 'html', 'text'], true)) {
            return htmlspecialchars_decode((string) $value);
        }

        if (in_array($type, ['checkbox', 'select2'], true)) {
            if ($value === '' || $value === null) {
                return [];
            }
            return array_values(array_filter(explode(',', (string) $value), static function ($part) {
                return $part !== '';
            }));
        }

        if (in_array($type, ['images', 'files'], true)) {
            $decoded = json_decode((string) ($item['option'] ?? ''), true);
            return is_array($decoded) ? array_values($decoded) : [];
        }

        if ($type === 'array') {
            $decoded = json_decode((string) ($item['option'] ?? ''), true);
            $pairs = [];
            if (is_array($decoded)) {
                foreach ($decoded as $key => $optionValue) {
                    $pairs[] = [
                        'key' => (string) $key,
                        'value' => (string) $optionValue,
                    ];
                }
            }
            return $pairs;
        }

        if ($type === 'bool') {
            return intval($value) ? '1' : '0';
        }

        return $value;
    }

    protected function normalizeConfigPageValue(array $config, $value): array
    {
        $type = (string) ($config['type'] ?? '');
        $option = json_decode((string) ($config['option'] ?? ''), true);
        if (!is_array($option)) {
            $option = [];
        }

        if ($type === 'array') {
            $pairs = is_array($value) ? $value : [];
            $arrayValue = [];
            foreach ($pairs as $pair) {
                if (!is_array($pair)) {
                    continue;
                }
                $key = trim((string) ($pair['key'] ?? ''));
                if ($key === '') {
                    continue;
                }
                $arrayValue[$key] = (string) ($pair['value'] ?? '');
            }

            return [
                'value' => 0,
                'option' => json_encode($arrayValue, JSON_UNESCAPED_UNICODE),
            ];
        }

        if (in_array($type, ['images', 'files'], true)) {
            $listValue = is_array($value) ? array_values(array_filter(array_map('strval', $value), static function ($item) {
                return trim($item) !== '';
            })) : [];
            return [
                'value' => 0,
                'option' => json_encode($listValue, JSON_UNESCAPED_UNICODE),
            ];
        }

        if (is_array($value)) {
            $value = implode(',', array_map('strval', $value));
        }

        return [
            'value' => (string) $value,
            'option' => json_encode($option, JSON_UNESCAPED_UNICODE),
        ];
    }

    protected function mapOptions(array $options): array
    {
        $result = [];
        foreach ($options as $value => $label) {
            $result[] = [
                'label' => (string) $label,
                'value' => (string) $value,
            ];
        }
        return $result;
    }

    protected function buildDictionaryOptions(): array
    {
        $list = db('dict_type')->order(['id' => 'asc'])->column('title', 'id');
        $options = [
            ['label' => '不使用字典', 'value' => 0],
        ];
        foreach ($list as $id => $title) {
            $options[] = [
                'label' => (string) $title,
                'value' => intval($id),
            ];
        }
        return $options;
    }

    protected function normalizeConfigPayload(array $data): array
    {
        $settings = $data['settings'] ?? [];
        if (!is_array($settings)) {
            $settings = [];
        }

        return [
            'id' => intval($data['id'] ?? 0),
            'group' => intval($data['group'] ?? 0),
            'type' => trim((string) ($data['type'] ?? 'text')),
            'name' => trim((string) ($data['name'] ?? '')),
            'title' => trim((string) ($data['title'] ?? '')),
            'value' => (string) ($data['value'] ?? ''),
            'tips' => trim((string) ($data['tips'] ?? '')),
            'sort' => intval($data['sort'] ?? 0),
            'dict_code' => intval($data['dict_code'] ?? 0),
            'option_text' => trim((string) ($data['option_text'] ?? '')),
            'settings' => $settings,
        ];
    }
}
