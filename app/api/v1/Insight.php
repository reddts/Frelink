<?php

namespace app\api\v1;

use app\common\controller\Api;
use app\model\Insight as InsightModel;

class Insight extends Api
{
    protected $needLogin = ['summary', 'keywords', 'content_trends', 'topic_trends', 'topic_graph', 'opportunities', 'recommendations', 'publish_assist', 'weekly_execution', 'writing_workflow', 'agent_brief', 'agent_draft'];
    protected $beforeActionList = [
        'authorizeInsightAccess' => ['only' => 'summary,keywords,content_trends,topic_trends,topic_graph,opportunities,recommendations,publish_assist,weekly_execution,writing_workflow,agent_brief,agent_draft'],
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

    public function topic_graph()
    {
        $days = intval($this->request->param('days', 30));
        $limit = intval($this->request->param('limit', 10));
        $this->apiResult(InsightModel::getTopicGraph($days, $limit));
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

    public function writing_workflow()
    {
        $days = intval($this->request->param('days', 7));
        $limit = intval($this->request->param('limit', 3));
        $mode = strtolower(trim((string)$this->request->param('mode', 'all')));
        $topic = trim((string)$this->request->param('topic', ''));
        $itemType = trim((string)$this->request->param('item_type', 'article'));
        $format = strtolower(trim((string)$this->request->param('format', 'json')));
        $workflow = InsightModel::getWritingWorkflow($mode, $days, $limit, $topic, $itemType);

        if ($format === 'markdown') {
            return response(
                InsightModel::renderWritingWorkflowBrief($workflow),
                200,
                ['Content-Type' => 'text/plain; charset=utf-8']
            );
        }

        $this->apiResult($workflow);
    }

    public function agent_brief()
    {
        $days = intval($this->request->param('days', 7));
        $limit = intval($this->request->param('limit', 3));
        $mode = strtolower(trim((string)$this->request->param('mode', 'all')));
        $topic = trim((string)$this->request->param('topic', ''));
        $itemType = trim((string)$this->request->param('item_type', 'article'));
        $format = strtolower(trim((string)$this->request->param('format', 'json')));
        $brief = InsightModel::getAgentBrief($days, $limit, $mode, $topic, $itemType);

        if ($format === 'markdown') {
            return response(
                InsightModel::renderAgentBrief($brief),
                200,
                ['Content-Type' => 'text/plain; charset=utf-8']
            );
        }

        $this->apiResult($brief);
    }

    public function agent_draft()
    {
        $itemType = trim((string)$this->request->param('item_type', 'article'));
        $days = intval($this->request->param('days', 7));
        $limit = intval($this->request->param('limit', 3));
        $topic = trim((string)$this->request->param('topic', ''));
        $mode = strtolower(trim((string)$this->request->param('mode', 'manual')));
        $itemId = intval($this->request->param('item_id', 0));
        $result = InsightModel::buildAgentDraft($itemType, $days, $limit, $topic, $mode);
        $draft = $result['draft'] ?? [];

        if (empty($draft)) {
            $this->apiResult([], 0, '草稿生成失败');
        }

        if ($itemType === 'article') {
            $draft['message'] = $draft['message'] ?? $draft['detail'] ?? '';
            $draft['detail'] = $draft['detail'] ?? $draft['message'] ?? '';
        }

        $topicItems = [];
        if (!empty($draft['topics']) && is_array($draft['topics'])) {
            $topicItems = \app\model\Topic::getTopicByIds($draft['topics']) ?: [];
        }

        \app\model\Draft::saveDraft($this->user_id, $result['item_type'] ?? $itemType, $draft, $itemId);

        $this->apiResult([
            'item_type' => $result['item_type'] ?? $itemType,
            'draft' => $draft,
            'topics' => $topicItems,
            'summary' => $result['summary'] ?? [],
        ]);
    }

    protected function authorizeInsightAccess(): void
    {
        if (!$this->currentUserCanAccessInsight()) {
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
