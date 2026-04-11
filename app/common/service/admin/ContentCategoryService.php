<?php

namespace app\common\service\admin;

use app\common\library\helper\TreeHelper;
use Overtrue\Pinyin\Pinyin;

class ContentCategoryService
{
    public function getOverview(string $type = '', string $keyword = ''): array
    {
        $typeOptions = $this->getTypeOptions();
        $activeType = isset($typeOptions[$type]) ? $type : '';

        return [
            'type' => $activeType,
            'tabs' => $this->buildTypeTabs($typeOptions),
            'type_options' => $this->buildTypeSelectOptions($typeOptions),
            'list' => $this->getList($activeType, $keyword, $typeOptions),
        ];
    }

    public function getDetail(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        $info = db('category')
            ->alias('c')
            ->leftJoin('category p', 'c.pid = p.id')
            ->where('c.id', $id)
            ->field('c.*,p.title as parent_title')
            ->find();

        if (!$info) {
            return [];
        }

        $typeOptions = $this->getTypeOptions();

        return [
            'id' => intval($info['id'] ?? 0),
            'pid' => intval($info['pid'] ?? 0),
            'title' => (string) ($info['title'] ?? ''),
            'description' => htmlspecialchars_decode((string) ($info['description'] ?? '')),
            'icon' => (string) ($info['icon'] ?? ''),
            'type' => (string) ($info['type'] ?? 'common'),
            'type_label' => (string) ($typeOptions[$info['type'] ?? ''] ?? ($info['type'] ?? '')),
            'url_token' => (string) ($info['url_token'] ?? ''),
            'status' => intval($info['status'] ?? 1),
            'sort' => intval($info['sort'] ?? 0),
            'parent_title' => (string) ($info['parent_title'] ?? ''),
            'flags' => $this->buildFlags($info),
            'type_options' => $this->buildTypeSelectOptions($typeOptions),
            'detail_fields' => $this->buildDetailFields($info, $typeOptions),
        ];
    }

    public function save(array $data): array
    {
        $id = intval($data['id'] ?? 0);
        if ($id <= 0) {
            return ['code' => 0, 'msg' => '分类不存在'];
        }

        $current = db('category')->where('id', $id)->find();
        if (!$current) {
            return ['code' => 0, 'msg' => '分类不存在'];
        }

        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            return ['code' => 0, 'msg' => '分类名称不能为空'];
        }

        $pid = array_key_exists('pid', $data) ? intval($data['pid']) : intval($current['pid'] ?? 0);
        if ($pid === $id) {
            return ['code' => 0, 'msg' => '父级分类不能是自身'];
        }

        $typeOptions = $this->getTypeOptions();
        $type = trim((string) ($data['type'] ?? ($current['type'] ?? 'common')));

        if ($pid > 0) {
            $parent = db('category')->where('id', $pid)->find();
            if (!$parent) {
                return ['code' => 0, 'msg' => '父级分类不存在'];
            }
            $type = (string) ($parent['type'] ?? 'common');
        } elseif (!isset($typeOptions[$type])) {
            $type = 'common';
        }

        $pinyin = new Pinyin();
        $urlToken = $pinyin->permalink($title, '');
        if ($urlToken === '') {
            $urlToken = 'category-' . $id;
        }

        $conflictId = intval(db('category')
            ->where('id', '<>', $id)
            ->where('url_token', $urlToken)
            ->value('id'));
        if ($conflictId > 0) {
            $urlToken .= time();
        }

        $updated = db('category')->where('id', $id)->update([
            'pid' => $pid,
            'title' => $title,
            'icon' => trim((string) ($data['icon'] ?? ($current['icon'] ?? ''))),
            'type' => $type,
            'description' => htmlspecialchars((string) ($data['description'] ?? htmlspecialchars_decode((string) ($current['description'] ?? ''))), ENT_QUOTES),
            'sort' => intval($data['sort'] ?? ($current['sort'] ?? 0)),
            'status' => array_key_exists('status', $data) ? intval($data['status']) : intval($current['status'] ?? 1),
            'url_token' => $urlToken,
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
            return ['code' => 0, 'msg' => '请选择要删除的分类'];
        }

        $childCount = intval(db('category')->whereIn('pid', $normalized)->count());
        if ($childCount > 0) {
            return ['code' => 0, 'msg' => '请先删除子分类后再删除父分类'];
        }

        $deleted = db('category')->whereIn('id', $normalized)->delete();
        if ($deleted === false) {
            return ['code' => 0, 'msg' => '删除失败'];
        }

        return ['code' => 1, 'msg' => '删除成功'];
    }

    protected function getList(string $type, string $keyword, array $typeOptions): array
    {
        $query = db('category')
            ->alias('c')
            ->leftJoin('category p', 'c.pid = p.id')
            ->field('c.id,c.pid,c.title,c.icon,c.type,c.url_token,c.status,c.sort,c.description,p.title as parent_title');

        if ($type !== '') {
            $query->where('c.type', $type);
        }

        if ($keyword !== '') {
            $query->whereLike('c.title', '%' . $keyword . '%');
        }

        $list = $query->order(['c.sort' => 'desc', 'c.id' => 'asc'])->select()->toArray();
        $list = TreeHelper::tree($list);

        foreach ($list as &$item) {
            $item['id'] = intval($item['id'] ?? 0);
            $item['pid'] = intval($item['pid'] ?? 0);
            $rawTitle = (string) ($item['title'] ?? '');
            $item['title'] = (string) ($item['left_title'] ?? ($item['title'] ?? ''));
            $item['raw_title'] = $rawTitle;
            $item['icon'] = (string) ($item['icon'] ?? '');
            $item['type'] = (string) ($item['type'] ?? 'common');
            $item['type_label'] = (string) ($typeOptions[$item['type']] ?? $item['type']);
            $item['url_token'] = (string) ($item['url_token'] ?? '');
            $item['status'] = intval($item['status'] ?? 1);
            $item['sort'] = intval($item['sort'] ?? 0);
            $item['parent_title'] = (string) ($item['parent_title'] ?? '');
            $item['flags'] = $this->buildFlags($item);
        }
        unset($item);

        return $list;
    }

    protected function getTypeOptions(): array
    {
        $options = ['common' => '通用', 'question' => '问题', 'article' => '文章'];
        $extra = config('aws.category');
        if (is_array($extra)) {
            $options = array_merge($options, $extra);
        }
        return $options;
    }

    protected function buildTypeTabs(array $typeOptions): array
    {
        $tabs = [
            ['label' => '全部', 'value' => ''],
        ];

        foreach ($typeOptions as $value => $label) {
            $tabs[] = ['label' => (string) $label, 'value' => (string) $value];
        }

        return $tabs;
    }

    protected function buildTypeSelectOptions(array $typeOptions): array
    {
        $options = [];
        foreach ($typeOptions as $value => $label) {
            $options[] = [
                'label' => (string) $label,
                'value' => (string) $value,
            ];
        }
        return $options;
    }

    protected function normalizeIds($ids): array
    {
        $ids = is_array($ids) ? $ids : explode(',', (string) $ids);
        return array_values(array_filter(array_map('intval', $ids)));
    }

    protected function buildFlags(array $info): array
    {
        $flags = [];

        if (intval($info['pid'] ?? 0) === 0) {
            $flags[] = '根分类';
        }
        if (intval($info['status'] ?? 1) !== 1) {
            $flags[] = '已禁用';
        }

        return $flags;
    }

    protected function buildDetailFields(array $info, array $typeOptions): array
    {
        return [
            ['label' => '父级分类', 'value' => (string) (($info['parent_title'] ?? '') ?: '无')],
            ['label' => '分类类型', 'value' => (string) ($typeOptions[$info['type'] ?? ''] ?? ($info['type'] ?? '-'))],
            ['label' => '分类标识', 'value' => (string) (($info['url_token'] ?? '') ?: '-')],
            ['label' => '排序值', 'value' => (string) intval($info['sort'] ?? 0)],
            ['label' => '状态', 'value' => intval($info['status'] ?? 1) === 1 ? '正常' : '禁用'],
        ];
    }
}
