<?php

namespace app\common\service\admin;

use app\model\Topic as TopicModel;
use Overtrue\Pinyin\Pinyin;

class ContentTopicService
{
    public function getOverview(int $rootOnly = 0, string $keyword = ''): array
    {
        return [
            'root_only' => $rootOnly === 1 ? 1 : 0,
            'tabs' => [
                ['label' => '全部', 'value' => 0],
                ['label' => '根话题', 'value' => 1],
            ],
            'list' => $this->getList($rootOnly, $keyword),
        ];
    }

    public function getDetail(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        $info = db('topic')
            ->alias('t')
            ->leftJoin('topic p', 't.pid = p.id')
            ->where('t.id', $id)
            ->field('t.*,p.title as parent_title')
            ->find();

        if (!$info) {
            return [];
        }

        return [
            'id' => intval($info['id'] ?? 0),
            'pid' => intval($info['pid'] ?? 0),
            'title' => (string) ($info['title'] ?? ''),
            'description' => htmlspecialchars_decode((string) ($info['description'] ?? '')),
            'seo_title' => (string) ($info['seo_title'] ?? ''),
            'seo_keywords' => (string) ($info['seo_keywords'] ?? ''),
            'seo_description' => (string) ($info['seo_description'] ?? ''),
            'url_token' => (string) ($info['url_token'] ?? ''),
            'pic' => (string) ($info['pic'] ?? ''),
            'is_parent' => intval($info['is_parent'] ?? 0),
            'lock' => intval($info['lock'] ?? 0),
            'top' => intval($info['top'] ?? 0),
            'status' => intval($info['status'] ?? 1),
            'discuss' => intval($info['discuss'] ?? 0),
            'discuss_week' => intval($info['discuss_week'] ?? 0),
            'discuss_month' => intval($info['discuss_month'] ?? 0),
            'focus' => intval($info['focus'] ?? 0),
            'create_time_text' => !empty($info['create_time']) ? date('Y-m-d H:i:s', intval($info['create_time'])) : '-',
            'update_time_text' => !empty($info['update_time']) ? date('Y-m-d H:i:s', intval($info['update_time'])) : '-',
            'preview_url' => get_url('topic/detail', ['id' => intval($info['id'] ?? 0)], true, false),
            'parent_title' => (string) ($info['parent_title'] ?? ''),
            'flags' => $this->buildFlags($info),
            'detail_fields' => $this->buildDetailFields($info),
        ];
    }

    public function save(array $data): array
    {
        $id = intval($data['id'] ?? 0);
        if ($id <= 0) {
            return ['code' => 0, 'msg' => '话题不存在'];
        }

        $current = db('topic')->where('id', $id)->find();
        if (!$current) {
            return ['code' => 0, 'msg' => '话题不存在'];
        }

        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            return ['code' => 0, 'msg' => '话题标题不能为空'];
        }

        $pinyin = new Pinyin();
        $urlToken = $pinyin->permalink($title, '');
        if ($urlToken === '') {
            $urlToken = 'topic-' . $id;
        }

        $conflictId = intval(db('topic')
            ->where('id', '<>', $id)
            ->where('url_token', $urlToken)
            ->value('id'));
        if ($conflictId > 0) {
            $urlToken .= time();
        }

        $isParent = array_key_exists('is_parent', $data) ? intval($data['is_parent']) : intval($current['is_parent'] ?? 0);
        $pid = array_key_exists('pid', $data) ? intval($data['pid']) : intval($current['pid'] ?? 0);
        if ($isParent === 1) {
            $pid = 0;
        }

        $updated = db('topic')->where('id', $id)->update([
            'title' => $title,
            'description' => htmlspecialchars((string) ($data['description'] ?? htmlspecialchars_decode((string) ($current['description'] ?? ''))), ENT_QUOTES),
            'seo_title' => trim((string) ($data['seo_title'] ?? ($current['seo_title'] ?? ''))),
            'seo_keywords' => trim((string) ($data['seo_keywords'] ?? ($current['seo_keywords'] ?? ''))),
            'seo_description' => trim((string) ($data['seo_description'] ?? ($current['seo_description'] ?? ''))),
            'is_parent' => $isParent,
            'pid' => $pid,
            'lock' => array_key_exists('lock', $data) ? intval($data['lock']) : intval($current['lock'] ?? 0),
            'top' => array_key_exists('top', $data) ? intval($data['top']) : intval($current['top'] ?? 0),
            'url_token' => $urlToken,
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
            return ['code' => 0, 'msg' => '请选择要删除的话题'];
        }

        foreach ($normalized as $id) {
            if (!TopicModel::removeTopic($id)) {
                return ['code' => 0, 'msg' => TopicModel::getError() ?: '删除失败'];
            }
        }

        return ['code' => 1, 'msg' => '删除成功'];
    }

    protected function getList(int $rootOnly, string $keyword): array
    {
        $query = db('topic')
            ->alias('t')
            ->leftJoin('topic p', 't.pid = p.id')
            ->field('t.id,t.pid,t.title,t.url_token,t.pic,t.discuss,t.discuss_week,t.discuss_month,t.focus,t.is_parent,t.lock,t.top,t.status,t.create_time,t.update_time,p.title as parent_title');

        if ($rootOnly === 1) {
            $query->where('t.is_parent', 1);
        }

        if ($keyword !== '') {
            $query->whereLike('t.title', '%' . $keyword . '%');
        }

        $list = $query->order(['t.id' => 'desc'])->select()->toArray();
        foreach ($list as &$item) {
            $item['id'] = intval($item['id'] ?? 0);
            $item['pid'] = intval($item['pid'] ?? 0);
            $item['discuss'] = intval($item['discuss'] ?? 0);
            $item['discuss_week'] = intval($item['discuss_week'] ?? 0);
            $item['discuss_month'] = intval($item['discuss_month'] ?? 0);
            $item['focus'] = intval($item['focus'] ?? 0);
            $item['is_parent'] = intval($item['is_parent'] ?? 0);
            $item['lock'] = intval($item['lock'] ?? 0);
            $item['top'] = intval($item['top'] ?? 0);
            $item['status'] = intval($item['status'] ?? 1);
            $item['parent_title'] = (string) ($item['parent_title'] ?? '');
            $item['preview_url'] = get_url('topic/detail', ['id' => $item['id']], true, false);
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

    protected function buildDetailFields(array $info): array
    {
        return [
            ['label' => '父级话题', 'value' => (string) (($info['parent_title'] ?? '') ?: '无')],
            ['label' => '话题标识', 'value' => (string) ($info['url_token'] ?? '-')],
            ['label' => '讨论总数', 'value' => (string) intval($info['discuss'] ?? 0)],
            ['label' => '近7天讨论', 'value' => (string) intval($info['discuss_week'] ?? 0)],
            ['label' => '近30天讨论', 'value' => (string) intval($info['discuss_month'] ?? 0)],
            ['label' => '关注数', 'value' => (string) intval($info['focus'] ?? 0)],
            ['label' => '创建时间', 'value' => !empty($info['create_time']) ? date('Y-m-d H:i:s', intval($info['create_time'])) : '-'],
            ['label' => '更新时间', 'value' => !empty($info['update_time']) ? date('Y-m-d H:i:s', intval($info['update_time'])) : '-'],
        ];
    }

    protected function buildFlags(array $info): array
    {
        $flags = [];
        if (intval($info['is_parent'] ?? 0) === 1) {
            $flags[] = '根话题';
        }
        if (intval($info['lock'] ?? 0) === 1) {
            $flags[] = '已锁定';
        }
        if (intval($info['top'] ?? 0) === 1) {
            $flags[] = '推荐';
        }
        if (intval($info['status'] ?? 1) !== 1) {
            $flags[] = '已禁用';
        }
        return $flags;
    }
}
