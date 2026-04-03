<?php

namespace app\common\service\admin;

use app\model\Article as ArticleModel;

class ContentArticleService
{
    public function getOverview(int $status = 1, string $keyword = ''): array
    {
        return [
            'status' => $status === 0 ? 0 : 1,
            'tabs' => [
                ['label' => '列表', 'value' => 1],
                ['label' => '已删除', 'value' => 0],
            ],
            'list' => $this->getList($status, $keyword),
        ];
    }

    public function getDetail(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        $info = db('article')
            ->alias('a')
            ->leftJoin('users u', 'a.uid = u.uid')
            ->where('a.id', $id)
            ->field('a.*,u.user_name,u.url_token')
            ->find();

        if (!$info) {
            return [];
        }

        return [
            'id' => intval($info['id'] ?? 0),
            'uid' => intval($info['uid'] ?? 0),
            'title' => (string) ($info['title'] ?? ''),
            'message' => htmlspecialchars_decode((string) ($info['message'] ?? '')),
            'seo_title' => (string) ($info['seo_title'] ?? ''),
            'seo_keywords' => (string) ($info['seo_keywords'] ?? ''),
            'seo_description' => (string) ($info['seo_description'] ?? ''),
            'user_name' => (string) ($info['user_name'] ?? ''),
            'url_token' => (string) ($info['url_token'] ?? ''),
            'status' => intval($info['status'] ?? 0),
            'preview_url' => get_url('article/detail', ['id' => intval($info['id'] ?? 0)], true, false),
            'edit_url' => get_url('article/publish', ['id' => intval($info['id'] ?? 0)], true, false),
            'create_time_text' => !empty($info['create_time']) ? date('Y-m-d H:i:s', intval($info['create_time'])) : '-',
            'update_time_text' => !empty($info['update_time']) ? date('Y-m-d H:i:s', intval($info['update_time'])) : '-',
            'comment_count' => intval($info['comment_count'] ?? 0),
            'agree_count' => intval($info['agree_count'] ?? 0),
            'view_count' => intval($info['view_count'] ?? 0),
            'set_top' => intval($info['set_top'] ?? 0),
            'is_recommend' => intval($info['is_recommend'] ?? 0),
            'status_label' => intval($info['status'] ?? 0) === 1 ? '正常' : '已删除',
            'flags' => $this->buildFlags($info),
            'detail_fields' => $this->buildDetailFields($info),
        ];
    }

    public function saveSeo(array $data): array
    {
        $id = intval($data['id'] ?? 0);
        if ($id <= 0) {
            return ['code' => 0, 'msg' => '文章不存在'];
        }

        $updated = ArticleModel::update([
            'id' => $id,
            'seo_title' => trim((string) ($data['seo_title'] ?? '')),
            'seo_keywords' => trim((string) ($data['seo_keywords'] ?? '')),
            'seo_description' => trim((string) ($data['seo_description'] ?? '')),
        ]);

        if (!$updated) {
            return ['code' => 0, 'msg' => '保存失败'];
        }

        return ['code' => 1, 'msg' => '保存成功', 'data' => ['id' => $id]];
    }

    public function delete($ids): array
    {
        if (!ArticleModel::removeArticle($this->normalizeIds($ids))) {
            return ['code' => 0, 'msg' => ArticleModel::getError() ?: '删除失败'];
        }

        return ['code' => 1, 'msg' => '删除成功'];
    }

    public function manager($ids, string $type): array
    {
        $normalized = $this->normalizeIds($ids);
        if (!$normalized) {
            return ['code' => 0, 'msg' => '请选择要操作的数据'];
        }

        if ($type === 'recover') {
            if (!ArticleModel::recordArticle($normalized)) {
                return ['code' => 0, 'msg' => '恢复失败'];
            }
            return ['code' => 1, 'msg' => '恢复成功'];
        }

        if ($type === 'remove') {
            if (!ArticleModel::removeArticle($normalized, true)) {
                return ['code' => 0, 'msg' => ArticleModel::getError() ?: '删除失败'];
            }
            return ['code' => 1, 'msg' => '删除成功'];
        }

        return ['code' => 0, 'msg' => '操作类型错误'];
    }

    protected function getList(int $status, string $keyword): array
    {
        $query = db('article')
            ->alias('a')
            ->leftJoin('users u', 'a.uid = u.uid')
            ->where('a.status', $status === 0 ? 0 : 1)
            ->field('a.id,a.uid,a.title,a.comment_count,a.agree_count,a.view_count,a.set_top,a.is_recommend,a.create_time,a.update_time,u.user_name,u.url_token');

        if ($keyword !== '') {
            $query->whereLike('a.title', '%' . $keyword . '%');
        }

        $list = $query->order(['a.id' => 'desc'])->select()->toArray();
        foreach ($list as &$item) {
            $item['id'] = intval($item['id'] ?? 0);
            $item['uid'] = intval($item['uid'] ?? 0);
            $item['comment_count'] = intval($item['comment_count'] ?? 0);
            $item['agree_count'] = intval($item['agree_count'] ?? 0);
            $item['view_count'] = intval($item['view_count'] ?? 0);
            $item['set_top'] = intval($item['set_top'] ?? 0);
            $item['is_recommend'] = intval($item['is_recommend'] ?? 0);
            $item['preview_url'] = get_url('article/detail', ['id' => $item['id']], true, false);
            $item['edit_url'] = get_url('article/publish', ['id' => $item['id']], true, false);
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
        $fields = [
            ['label' => '作者', 'value' => (string) ($info['user_name'] ?? '未知用户')],
            ['label' => '状态', 'value' => intval($info['status'] ?? 0) === 1 ? '正常' : '已删除'],
            ['label' => '评论数', 'value' => (string) intval($info['comment_count'] ?? 0)],
            ['label' => '赞同数', 'value' => (string) intval($info['agree_count'] ?? 0)],
            ['label' => '浏览数', 'value' => (string) intval($info['view_count'] ?? 0)],
            ['label' => '推荐状态', 'value' => intval($info['is_recommend'] ?? 0) === 1 ? '推荐' : '普通'],
            ['label' => '置顶状态', 'value' => intval($info['set_top'] ?? 0) === 1 ? '已置顶' : '未置顶'],
            ['label' => '创建时间', 'value' => !empty($info['create_time']) ? date('Y-m-d H:i:s', intval($info['create_time'])) : '-'],
            ['label' => '更新时间', 'value' => !empty($info['update_time']) ? date('Y-m-d H:i:s', intval($info['update_time'])) : '-'],
        ];

        if (!empty($info['category_id'])) {
            $categoryTitle = db('category')->where('id', intval($info['category_id']))->value('title');
            if ($categoryTitle) {
                $fields[] = ['label' => '文章分类', 'value' => (string) $categoryTitle];
            }
        }

        if (!empty($info['column_id'])) {
            $columnTitle = db('column')->where('id', intval($info['column_id']))->value('name');
            if ($columnTitle) {
                $fields[] = ['label' => '文章专栏', 'value' => (string) $columnTitle];
            }
        }

        if (!empty($info['cover'])) {
            $fields[] = ['label' => '封面', 'value' => (string) $info['cover']];
        }

        $topicTitles = $this->getTopicTitles(intval($info['id'] ?? 0), 'article');
        if ($topicTitles) {
            $fields[] = ['label' => '关联话题', 'value' => implode(' / ', $topicTitles)];
        }

        return $fields;
    }

    protected function buildFlags(array $info): array
    {
        $flags = [];
        if (intval($info['status'] ?? 0) !== 1) {
            $flags[] = '已删除';
        }
        if (intval($info['is_recommend'] ?? 0) === 1) {
            $flags[] = '推荐';
        }
        if (intval($info['set_top'] ?? 0) === 1) {
            $flags[] = '置顶';
        }
        return $flags;
    }

    protected function getTopicTitles(int $itemId, string $itemType): array
    {
        if ($itemId <= 0) {
            return [];
        }

        $topicIds = db('topic_relation')
            ->where([
                'item_type' => $itemType,
                'item_id' => $itemId,
                'status' => 1,
            ])
            ->column('topic_id');

        if (!$topicIds) {
            return [];
        }

        return array_values(array_filter(db('topic')->whereIn('id', $topicIds)->column('title')));
    }
}
