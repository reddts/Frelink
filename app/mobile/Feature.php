<?php
namespace app\mobile;

use app\common\controller\Frontend;
use app\model\Feature as FeatureModel;
use app\model\Topic as TopicModel;

class Feature extends Frontend
{
    public function index()
    {
        $page = $this->request->param('page', 1, 'intval');
        $data = FeatureModel::getFeatureList([], ['id' => 'desc'], $page, 10, 'pageMain');

        $this->assign($data);
        $this->TDK(L('观察专题') . ' - ' . L('长期主题观察'));
        return $this->fetch();
    }

    public function detail()
    {
        $token = $this->request->param('token', '', 'trim');
        $sort = $this->request->param('sort', 'new', 'trim');
        $contentType = $this->request->param('content_type', 'all', 'trim');
        $contentType = in_array($contentType, ['all', 'question', 'research', 'fragment', 'faq'], true) ? $contentType : 'all';

        $info = db('feature')->where(['url_token' => $token])->find();
        if (!$info) {
            $this->error(L('专题不存在'), url('feature/index'));
        }

        $topicIds = db('feature_topic')
            ->where(['feature_id' => $info['id']])
            ->column('topic_id');

        $topics = TopicModel::getTopicByIds($topicIds);
        $data = FeatureModel::getRelationFeatureList($this->user_id, $info['id'], $sort, 1, 10, 'pageMain', $contentType);

        $this->assign([
            'info' => $info,
            'sort' => $sort,
            'content_type' => $contentType,
            'topics' => $topics,
            'list' => $data['list'] ?? [],
            'page' => $data['page'] ?? '',
            'total' => $data['total'] ?? 0,
        ]);
        $this->TDK(($info['title'] ?? L('观察专题')) . ' - ' . L('观察专题'));
        return $this->fetch();
    }
}
