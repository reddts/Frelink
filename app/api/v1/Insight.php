<?php

namespace app\api\v1;

use app\common\controller\Api;
use app\model\Insight as InsightModel;

class Insight extends Api
{
    protected $needLogin = ['summary', 'keywords', 'content_trends', 'topic_trends', 'opportunities', 'recommendations', 'publish_assist', 'weekly_execution'];
    protected $beforeActionList = [
        'authorizeInsightAccess' => ['only' => 'summary,keywords,content_trends,topic_trends,opportunities,recommendations,publish_assist,weekly_execution'],
    ];

    public function track()
    {
        $payload = $this->getPayload();
        $payload['uid'] = $this->user_id ?: 0;
        $payload['ip'] = $this->request->ip();
        $payload['user_agent'] = $this->request->server('HTTP_USER_AGENT', '');
        $result = InsightModel::trackEvent($payload);
        if (!$result['status']) {
            $this->apiResult([], 0, $result['message']);
        }
        $this->apiResult([], 1, $result['message']);
    }

    public function summary()
    {
        $days = intval($this->request->param('days', 7));
        $this->apiResult(InsightModel::getWindowSummary($days));
    }

    public function keywords()
    {
        $days = intval($this->request->param('days', 7));
        $limit = intval($this->request->param('limit', 10));
        $this->apiResult(InsightModel::getTopKeywords($days, $limit));
    }

    public function opportunities()
    {
        $days = intval($this->request->param('days', 7));
        $limit = intval($this->request->param('limit', 10));
        $this->apiResult(InsightModel::getSearchOpportunities($days, $limit));
    }

    public function content_trends()
    {
        $days = intval($this->request->param('days', 7));
        $limit = intval($this->request->param('limit', 10));
        $itemType = $this->request->param('item_type', '', 'trim');
        $this->apiResult(InsightModel::getContentTrends($days, $limit, $itemType));
    }

    public function topic_trends()
    {
        $days = intval($this->request->param('days', 7));
        $limit = intval($this->request->param('limit', 10));
        $this->apiResult(InsightModel::getTopicTrends($days, $limit));
    }

    public function recommendations()
    {
        $days = intval($this->request->param('days', 7));
        $limit = intval($this->request->param('limit', 10));
        $this->apiResult(InsightModel::getRecommendations($days, $limit));
    }

    public function publish_assist()
    {
        $days = intval($this->request->param('days', 7));
        $limit = intval($this->request->param('limit', 6));
        $itemType = $this->request->param('item_type', 'question', 'trim');
        $this->apiResult(InsightModel::getPublishAssist($itemType, $days, $limit));
    }

    public function weekly_execution()
    {
        $days = intval($this->request->param('days', 7));
        $limit = intval($this->request->param('limit', 3));
        $format = strtolower(trim((string)$this->request->param('format', 'json')));
        $plan = InsightModel::getWeeklyExecutionPlan($days, $limit);

        if ($format === 'markdown') {
            return response(
                InsightModel::renderWeeklyExecutionBrief($plan),
                200,
                ['Content-Type' => 'text/plain; charset=utf-8']
            );
        }

        $this->apiResult($plan);
    }

    protected function authorizeInsightAccess(): void
    {
        if (!isSuperAdmin() && !isNormalAdmin() && get_user_permission('recommend_post') !== 'Y') {
            $this->apiError('您没有查看运营洞察的权限');
        }
    }

    protected function getPayload(): array
    {
        $data = $this->request->post();
        if (!empty($data)) {
            return $data;
        }

        $raw = file_get_contents('php://input');
        if (!$raw) {
            return [];
        }

        $json = json_decode($raw, true);
        return is_array($json) ? $json : [];
    }
}
