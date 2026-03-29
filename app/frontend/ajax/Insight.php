<?php
namespace app\frontend\ajax;

use app\common\controller\Frontend;
use app\model\Draft;
use app\model\Insight as InsightModel;
use app\model\Topic;

class Insight extends Frontend
{
    protected $needLogin = ['agent_draft'];

    public function agent_draft()
    {
        $itemType = trim((string)$this->request->post('item_type', 'article'));
        $days = intval($this->request->post('days', 7));
        $limit = intval($this->request->post('limit', 3));
        $topic = trim((string)$this->request->post('topic', ''));
        $mode = strtolower(trim((string)$this->request->post('mode', 'manual')));
        $itemId = intval($this->request->post('item_id', 0));

        $result = InsightModel::buildAgentDraft($itemType, $days, $limit, $topic, $mode);
        $draft = $result['draft'] ?? [];
        if (empty($draft)) {
            $this->error('草稿生成失败');
        }

        if ($itemType === 'article') {
            $draft['message'] = $draft['message'] ?? $draft['detail'] ?? '';
            $draft['detail'] = $draft['detail'] ?? $draft['message'] ?? '';
        }

        $topics = [];
        if (!empty($draft['topics']) && is_array($draft['topics'])) {
            $topics = Topic::getTopicByIds($draft['topics']) ?: [];
        }

        Draft::saveDraft($this->user_id, $result['item_type'] ?? $itemType, $draft, $itemId);

        $this->success('生成成功', [
            'item_type' => $result['item_type'] ?? $itemType,
            'draft' => $draft,
            'topics' => $topics,
            'summary' => $result['summary'] ?? [],
        ]);
    }
}
