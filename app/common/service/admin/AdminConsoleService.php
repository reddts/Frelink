<?php

namespace app\common\service\admin;

use app\common\library\helper\AuthHelper;
use app\model\Users;

class AdminConsoleService
{
    protected AuthHelper $auth;

    public function __construct(?AuthHelper $auth = null)
    {
        $this->auth = $auth ?: AuthHelper::instance();
    }

    public function getAdminUserInfo(int $adminUid): array
    {
        if ($adminUid <= 0) {
            return [];
        }

        $userInfo = Users::getUserInfo($adminUid);
        return $userInfo ?: [];
    }

    public function formatAdminProfile(array $userInfo): array
    {
        if (!$userInfo) {
            return [];
        }

        return [
            'uid' => intval($userInfo['uid'] ?? 0),
            'user_name' => (string) ($userInfo['user_name'] ?? ''),
            'nick_name' => (string) ($userInfo['nick_name'] ?? ''),
            'email' => (string) ($userInfo['email'] ?? ''),
            'mobile' => (string) ($userInfo['mobile'] ?? ''),
            'avatar' => (string) ($userInfo['avatar'] ?? '/static/common/image/default-avatar.svg'),
            'group_id' => intval($userInfo['group_id'] ?? 0),
            'group_name' => (string) ($userInfo['group_name'] ?? ''),
            'is_super_admin' => $this->auth->isSuperAdmin(),
            'permission' => $userInfo['permission'] ?? [],
        ];
    }

    public function getAdminPermissionNames(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }

        $ruleIds = $this->auth->getRuleIds($userId);
        if (!$ruleIds) {
            return [];
        }

        return array_values(array_unique(db('admin_auth')->whereIn('id', $ruleIds)->column('name')));
    }

    public function getDashboardPayload(): array
    {
        $ratioMetrics = $this->buildDashboardRatios();
        $trendPanels = $this->buildDashboardTrends();

        return [
            'title' => 'Frelink 管理端',
            'subtitle' => '新管理端以独立 adminapi 体系推进后台重构，不再挂靠前台开放 API。',
            'stats' => [
                [
                    'key' => 'users',
                    'label' => '有效用户',
                    'value' => intval(db('users')->where('status', 1)->count()),
                ],
                [
                    'key' => 'articles',
                    'label' => '文章',
                    'value' => intval(db('article')->where('status', 1)->count()),
                ],
                [
                    'key' => 'questions',
                    'label' => '问题',
                    'value' => intval(db('question')->where('status', 1)->count()),
                ],
                [
                    'key' => 'answers',
                    'label' => '回答',
                    'value' => intval(db('answer')->where('status', 1)->count()),
                ],
                [
                    'key' => 'approval_question',
                    'label' => '待审问题',
                    'value' => intval(db('approval')->where(['status' => 0, 'type' => 'question'])->count()),
                ],
                [
                    'key' => 'approval_answer',
                    'label' => '待审回答',
                    'value' => intval(db('approval')->where(['status' => 0, 'type' => 'answer'])->count()),
                ],
            ],
            'quick_links' => [
                [
                    'title' => '旧后台首页',
                    'path' => backend_url('Index/index'),
                ],
                [
                    'title' => '内容审核',
                    'path' => backend_url('content/Approval/index'),
                ],
                [
                    'title' => '文章管理',
                    'path' => backend_url('content/Article/index'),
                ],
                [
                    'title' => '用户管理',
                    'path' => backend_url('member/Users/index'),
                ],
            ],
            'ratio_metrics' => $ratioMetrics,
            'trend_panels' => $trendPanels,
        ];
    }

    protected function buildDashboardRatios(): array
    {
        $totalContent = intval(db('article')->where('status', 1)->count()) + intval(db('question')->where('status', 1)->count());
        $viewedContent = intval(db('article')->where('status', 1)->where('view_count', '>', 0)->count())
            + intval(db('question')->where('status', 1)->where('view_count', '>', 0)->count());

        $totalUsers = intval(db('users')->where('status', 1)->count());
        $activeUsers = intval(db('users')->where('status', 1)->where('last_login_time', '>=', strtotime('-7 day'))->count());

        $recentContentTotal = $this->countRecentContent();
        $recentAgentContent = $this->countRecentAgentContent();

        $recentApprovalTotal = intval(db('approval')->where('create_time', '>=', strtotime('-7 day'))->count());
        $recentApprovalReviewed = intval(db('approval')->where('create_time', '>=', strtotime('-7 day'))->where('status', '>', 0)->count());

        return [
            [
                'key' => 'content_visit_rate',
                'label' => '访问覆盖率',
                'value' => $this->calculateRate($viewedContent, $totalContent),
                'unit' => '%',
                'description' => '至少被访问过一次的文章和问题占比',
            ],
            [
                'key' => 'user_active_rate',
                'label' => '用户活跃度',
                'value' => $this->calculateRate($activeUsers, $totalUsers),
                'unit' => '%',
                'description' => '近 7 天内发生登录的有效用户占比',
            ],
            [
                'key' => 'agent_participation_rate',
                'label' => '机器人参与度',
                'value' => $this->calculateRate($recentAgentContent, $recentContentTotal),
                'unit' => '%',
                'description' => '近 7 天新内容中由 Agent 参与产出的占比',
            ],
            [
                'key' => 'approval_clear_rate',
                'label' => '审核处理率',
                'value' => $this->calculateRate($recentApprovalReviewed, $recentApprovalTotal),
                'unit' => '%',
                'description' => '近 7 天新增审核记录中已处理完成的占比',
            ],
        ];
    }

    protected function buildDashboardTrends(): array
    {
        $labels = $this->getRecentDateLabels(7);

        return [
            [
                'key' => 'content_growth',
                'title' => '内容新增趋势',
                'description' => '按日查看问题、文章、回答的新增量',
                'labels' => $labels,
                'series' => [
                    [
                        'label' => '问题',
                        'color' => '#1465ff',
                        'values' => $this->buildDailySeries(7, static function (int $start, int $end) {
                            return intval(db('question')->where('status', 1)->whereBetween('create_time', [$start, $end])->count());
                        }),
                    ],
                    [
                        'label' => '文章',
                        'color' => '#0fa08c',
                        'values' => $this->buildDailySeries(7, static function (int $start, int $end) {
                            return intval(db('article')->where('status', 1)->whereBetween('create_time', [$start, $end])->count());
                        }),
                    ],
                    [
                        'label' => '回答',
                        'color' => '#ff8a3d',
                        'values' => $this->buildDailySeries(7, static function (int $start, int $end) {
                            return intval(db('answer')->where('status', 1)->whereBetween('create_time', [$start, $end])->count());
                        }),
                    ],
                ],
            ],
            [
                'key' => 'user_activity',
                'title' => '用户活跃趋势',
                'description' => '按日查看新增用户与登录活跃用户变化',
                'labels' => $labels,
                'series' => [
                    [
                        'label' => '新增用户',
                        'color' => '#7c4dff',
                        'values' => $this->buildDailySeries(7, static function (int $start, int $end) {
                            return intval(db('users')->where('status', 1)->whereBetween('create_time', [$start, $end])->count());
                        }),
                    ],
                    [
                        'label' => '活跃用户',
                        'color' => '#d6457b',
                        'values' => $this->buildDailySeries(7, static function (int $start, int $end) {
                            return intval(db('users')->where('status', 1)->whereBetween('last_login_time', [$start, $end])->count());
                        }),
                    ],
                ],
            ],
            [
                'key' => 'agent_activity',
                'title' => '机器人参与趋势',
                'description' => '按日查看 Agent 参与的文章、问题、回答产出',
                'labels' => $labels,
                'series' => [
                    [
                        'label' => 'Agent 问题',
                        'color' => '#00a6fb',
                        'values' => $this->buildAgentDailySeries('question', 7),
                    ],
                    [
                        'label' => 'Agent 文章',
                        'color' => '#2ec4b6',
                        'values' => $this->buildAgentDailySeries('article', 7),
                    ],
                    [
                        'label' => 'Agent 回答',
                        'color' => '#ff9f1c',
                        'values' => $this->buildAgentDailySeries('answer', 7),
                    ],
                ],
            ],
        ];
    }

    protected function buildDailySeries(int $days, callable $counter): array
    {
        $values = [];
        for ($offset = $days - 1; $offset >= 0; $offset--) {
            $start = strtotime(date('Y-m-d 00:00:00', strtotime("-{$offset} day")));
            $end = strtotime(date('Y-m-d 23:59:59', strtotime("-{$offset} day")));
            $values[] = intval($counter($start, $end));
        }
        return $values;
    }

    protected function buildAgentDailySeries(string $table, int $days): array
    {
        return $this->buildDailySeries($days, static function (int $start, int $end) use ($table) {
            return intval(
                db($table)
                    ->alias('i')
                    ->leftJoin('users u', 'i.uid = u.uid')
                    ->where('i.status', 1)
                    ->where('u.is_agent', 1)
                    ->whereBetween('i.create_time', [$start, $end])
                    ->count()
            );
        });
    }

    protected function getRecentDateLabels(int $days): array
    {
        $labels = [];
        for ($offset = $days - 1; $offset >= 0; $offset--) {
            $labels[] = date('m-d', strtotime("-{$offset} day"));
        }
        return $labels;
    }

    protected function countRecentContent(): int
    {
        $start = strtotime('-7 day');
        return intval(db('question')->where('status', 1)->where('create_time', '>=', $start)->count())
            + intval(db('article')->where('status', 1)->where('create_time', '>=', $start)->count())
            + intval(db('answer')->where('status', 1)->where('create_time', '>=', $start)->count());
    }

    protected function countRecentAgentContent(): int
    {
        $start = strtotime('-7 day');

        $questionCount = intval(
            db('question')
                ->alias('q')
                ->leftJoin('users u', 'q.uid = u.uid')
                ->where('q.status', 1)
                ->where('u.is_agent', 1)
                ->where('q.create_time', '>=', $start)
                ->count()
        );

        $articleCount = intval(
            db('article')
                ->alias('a')
                ->leftJoin('users u', 'a.uid = u.uid')
                ->where('a.status', 1)
                ->where('u.is_agent', 1)
                ->where('a.create_time', '>=', $start)
                ->count()
        );

        $answerCount = intval(
            db('answer')
                ->alias('a')
                ->leftJoin('users u', 'a.uid = u.uid')
                ->where('a.status', 1)
                ->where('u.is_agent', 1)
                ->where('a.create_time', '>=', $start)
                ->count()
        );

        return $questionCount + $articleCount + $answerCount;
    }

    protected function calculateRate(int $numerator, int $denominator): float
    {
        if ($denominator <= 0) {
            return 0;
        }

        return round(($numerator / $denominator) * 100, 1);
    }
}
