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
        ];
    }
}
