<?php
namespace app\backend\extend;

use app\common\controller\Backend;
use app\model\AgentChallengeLog;
use app\model\Users;
use think\facade\Request;

class AgentChallenge extends Backend
{
    protected $table = 'agent_challenge_log';

    public function index()
    {
        $columns = [
            ['id', '编号'],
            ['challenge_id', '挑战ID'],
            ['username', '提交用户名'],
            ['uid_text', '绑定用户'],
            ['difficulty', '难度', 'tag', '', [
                'easy' => 'easy',
                'normal' => 'normal',
                'hard' => 'hard',
            ]],
            ['category', '题型'],
            ['status', '结果', 'tag', 'issued', AgentChallengeLog::STATUS_LABELS],
            ['failure_reason', '失败原因', 'tag', '', AgentChallengeLog::FAILURE_REASON_LABELS],
            ['elapsed_ms', '耗时(ms)', 'number'],
            ['answer_correct', '答对', 'tag', 0, [0 => '否', 1 => '是']],
            ['issued_at', '出题时间', 'datetime'],
            ['deadline', '截止时间', 'datetime'],
            ['answered_at', '答题时间', 'datetime'],
            ['question', '题目'],
        ];

        $status = trim((string) $this->request->param('status', ''));
        $difficulty = trim((string) $this->request->param('difficulty', ''));
        $search = [
            ['text', 'challenge_id', '挑战ID', 'LIKE'],
            ['text', 'username', '提交用户名', 'LIKE'],
            ['select', 'status', '结果', '=', $status, AgentChallengeLog::STATUS_LABELS],
            ['select', 'difficulty', '难度', '=', $difficulty, [
                'easy' => 'easy',
                'normal' => 'normal',
                'hard' => 'hard',
            ]],
            ['text', 'category', '题型', 'LIKE'],
            ['datetime', 'issued_at', '出题时间'],
            ['datetime', 'answered_at', '答题时间'],
        ];

        if ($this->request->param('_list')) {
            if (!AgentChallengeLog::isAvailable()) {
                return [
                    'code' => 0,
                    'msg' => '',
                    'count' => 0,
                    'data' => [],
                ];
            }

            $sortableColumns = ['id', 'elapsed_ms', 'issued_at', 'deadline', 'answered_at', 'uid'];
            $orderByColumn = (string) $this->request->param('orderByColumn', 'id');
            if (!in_array($orderByColumn, $sortableColumns, true)) {
                $orderByColumn = 'id';
            }
            $isAsc = strtolower((string) $this->request->param('isAsc', 'desc')) === 'asc' ? 'asc' : 'desc';
            $where = $this->makeBuilder->getWhere($search);
            $pageSize = intval($this->request->param('pageSize', get_setting("contents_per_page", 15)));

            $list = db($this->table)
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query' => Request::get(),
                    'list_rows' => $pageSize,
                ])
                ->toArray();

            $uids = array_values(array_filter(array_unique(array_map('intval', array_column($list['data'] ?? [], 'uid')))));
            $userInfos = $uids ? Users::getUserInfoByIds($uids, 'uid,nick_name,user_name', 99) : [];

            foreach ($list['data'] as $key => $row) {
                $uid = intval($row['uid'] ?? 0);
                $list['data'][$key]['uid_text'] = $uid > 0 ? ($userInfos[$uid]['name'] ?? ('UID ' . $uid)) : '未绑定';
            }

            return $list;
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->setPageTips($this->buildPageTips())
            ->addColumns($columns)
            ->setSearch($search)
            ->setDataUrl((string) url('index', array_merge(Request::get(), ['_list' => 1])))
            ->setLinkGroup([
                [
                    'title' => '全部',
                    'link' => (string) url('index', ['difficulty' => $difficulty]),
                    'active' => $status === ''
                ],
                [
                    'title' => '已出题',
                    'link' => (string) url('index', ['status' => 'issued', 'difficulty' => $difficulty]),
                    'active' => $status === 'issued'
                ],
                [
                    'title' => '答对',
                    'link' => (string) url('index', ['status' => 'success', 'difficulty' => $difficulty]),
                    'active' => $status === 'success'
                ],
                [
                    'title' => '答错',
                    'link' => (string) url('index', ['status' => 'wrong_answer', 'difficulty' => $difficulty]),
                    'active' => $status === 'wrong_answer'
                ],
                [
                    'title' => '超时',
                    'link' => (string) url('index', ['status' => 'timeout', 'difficulty' => $difficulty]),
                    'active' => $status === 'timeout'
                ],
                [
                    'title' => '失效',
                    'link' => (string) url('index', ['status' => 'missing', 'difficulty' => $difficulty]),
                    'active' => $status === 'missing'
                ],
            ])
            ->fetch();
    }

    protected function buildPageTips(): string
    {
        if (!AgentChallengeLog::isAvailable()) {
            return '说明：当前站点还没有 `agent_challenge_log` 表，请先执行升级脚本 `docs/agent-challenge-log-upgrade.sql` 与 `docs/agent-challenge-admin-upgrade.sql`。';
        }

        $overview = AgentChallengeLog::getOverview();
        if (empty($overview['total_logs'])) {
            return '说明：暂未发现 agent challenge 测试记录。后续当 agent 调用 `/api/Agent/challenge` 与 `/api/Agent/verify` 后，这里会显示测试结果、耗时和失败原因。';
        }

        $topReasons = array_map(static function ($item) {
            return ($item['label'] ?? '') . ' ' . intval($item['count'] ?? 0) . ' 次';
        }, $overview['top_failure_reasons'] ?? []);
        $topDifficulties = array_map(static function ($item) {
            return ($item['label'] ?? '') . ' ' . intval($item['count'] ?? 0) . ' 次';
        }, $overview['top_difficulties'] ?? []);
        $topCategories = array_map(static function ($item) {
            return ($item['label'] ?? '') . ' ' . intval($item['count'] ?? 0) . ' 次';
        }, $overview['top_categories'] ?? []);
        $topUsernames = array_map(static function ($item) {
            return ($item['label'] ?? '') . ' ' . intval($item['count'] ?? 0) . ' 次';
        }, $overview['top_usernames'] ?? []);
        $dailyStats = array_map(static function ($item) {
            return ($item['day'] ?? '') . ' 发题' . intval($item['issued_count'] ?? 0) . '/答对' . intval($item['success_count'] ?? 0) . '/失败' . intval($item['failure_count'] ?? 0) . '/通过率' . rtrim(rtrim(number_format((float) ($item['pass_rate'] ?? 0), 2, '.', ''), '0'), '.') . '%/均耗时' . intval($item['avg_response_ms'] ?? 0) . 'ms';
        }, $overview['daily_stats'] ?? []);
        $ttlTimeoutStats = array_map(static function ($item) {
            return ($item['label'] ?? '') . ' TTL ' . intval($item['ttl_seconds'] ?? 0) . 's/目标 ' . intval($item['target_response_ms'] ?? 0) . 'ms/发题' . intval($item['issued_count'] ?? 0) . '/已判定' . intval($item['resolved_count'] ?? 0) . '/超时' . intval($item['timeout_count'] ?? 0) . '/超时率' . rtrim(rtrim(number_format((float) ($item['timeout_rate'] ?? 0), 2, '.', ''), '0'), '.') . '%';
        }, $overview['ttl_timeout_stats'] ?? []);

        $parts = [
            '总测试记录：' . intval($overview['total_logs'] ?? 0),
            '参与用户名：' . intval($overview['unique_usernames'] ?? 0),
            '已绑定 Agent：' . intval($overview['bound_agent_count'] ?? 0),
            '待答题：' . intval($overview['issued_count'] ?? 0),
            '答对：' . intval($overview['success_count'] ?? 0),
            '失败：' . intval($overview['failure_count'] ?? 0),
            '成功率：' . rtrim(rtrim(number_format((float) ($overview['pass_rate'] ?? 0), 2, '.', ''), '0'), '.') . '%',
            '平均耗时：' . intval($overview['avg_response_ms'] ?? 0) . 'ms',
            '成功平均耗时：' . intval($overview['avg_success_response_ms'] ?? 0) . 'ms',
        ];

        if (!empty($overview['recent_test_at'])) {
            $parts[] = '最近测试：' . ($overview['recent_test_at_text'] ?? date('Y-m-d H:i:s', intval($overview['recent_test_at'])));
        }
        if ($topReasons) {
            $parts[] = '高频失败原因：' . implode('，', $topReasons);
        }
        if ($topDifficulties) {
            $parts[] = '高频难度：' . implode('，', $topDifficulties);
        }
        if ($topCategories) {
            $parts[] = '高频题型：' . implode('，', $topCategories);
        }
        if ($topUsernames) {
            $parts[] = '活跃测试者：' . implode('，', $topUsernames);
        }
        if ($dailyStats) {
            $parts[] = '近7天趋势：' . implode('；', $dailyStats);
        }
        if ($ttlTimeoutStats) {
            $parts[] = 'TTL档位观察：' . implode('；', $ttlTimeoutStats);
        }

        return '说明：用于观察 agent 是否在持续参与 challenge 测试，以及测试结果是否集中失败。<br>' . implode(' | ', $parts);
    }
}
