<?php

namespace app\common\controller;

use app\common\library\helper\AuthHelper;
use app\common\service\admin\AdminConsoleService;
use app\model\Users;
use think\App;
use think\helper\Str;

abstract class AdminApi extends Base
{
    protected $auth;
    protected $user_id = 0;
    protected $user_info = [];
    protected $consoleService;
    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['login', 'logout', 'me', 'menu', 'dashboard'];

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->checkIpAllowed();
        $this->auth = AuthHelper::instance();
        $this->consoleService = new AdminConsoleService($this->auth);
        $this->bootstrapAdminSession();
        $this->guardLogin();
        $this->guardPermission();
        $this->initialize();
    }

    protected function initialize()
    {
    }

    protected function bootstrapAdminSession(): void
    {
        $adminUid = intval(session('admin_login_uid'));
        if ($adminUid <= 0) {
            return;
        }

        $userInfo = Users::getUserInfo($adminUid);
        if (!$userInfo) {
            session('admin_login_uid', null);
            session('admin_user_info', null);
            session('admin_login_user_info', null);
            return;
        }

        $this->user_id = intval($userInfo['uid'] ?? 0);
        $this->user_info = $userInfo;
    }

    protected function guardLogin(): void
    {
        if ($this->matchAction($this->noNeedLogin)) {
            return;
        }

        if (!$this->user_id) {
            $this->apiResult([], 99, '请先登录后进行操作');
        }
    }

    protected function guardPermission(): void
    {
        if (!$this->user_id || $this->matchAction($this->noNeedRight) || $this->auth->isSuperAdmin()) {
            return;
        }

        $checkPath = $this->buildPermissionPath();
        if (!$this->auth->check($checkPath, $this->user_id)) {
            $this->apiError('您没有访问权限');
        }
    }

    protected function buildPermissionPath(): string
    {
        $controller = strtolower((string) $this->request->controller());
        $action = strtolower(Str::snake($this->request->action()));

        $mapped = [
            'contentarticle/index' => 'content/Article/index',
            'contentarticle/detail' => 'content/Article/seo',
            'contentarticle/saveseo' => 'content/Article/seo',
            'contentarticle/save' => 'content/Article/seo',
            'contentarticle/delete' => 'content/Article/delete',
            'contentarticle/manager' => 'content/Article/manager',
            'contentquestion/index' => 'content/Question/index',
            'contentquestion/detail' => 'content/Question/seo',
            'contentquestion/saveseo' => 'content/Question/seo',
            'contentquestion/save' => 'content/Question/seo',
            'contentquestion/delete' => 'content/Question/delete',
            'contentquestion/manager' => 'content/Question/manager',
            'contentanswer/index' => 'content/Answer/index',
            'contentanswer/detail' => 'content/Answer/edit',
            'contentanswer/save' => 'content/Answer/edit',
            'contentanswer/delete' => 'content/Answer/delete',
            'contentapproval/index' => 'content/Approval/index',
            'contentapproval/detail' => 'content/Approval/edit',
            'contentapproval/approve' => 'content/Approval/state',
            'contentapproval/decline' => 'content/Approval/state',
            'contentapproval/delete' => 'content/Approval/delete',
            'contentapproval/forbid' => 'content/Approval/forbidden',
            'contentapproval/forbiddenip' => 'content/Approval/forbidden_ip',
            'contenttopic/index' => 'content/Topic/index',
            'contenttopic/detail' => 'content/Topic/edit',
            'contenttopic/save' => 'content/Topic/edit',
            'contenttopic/delete' => 'content/Topic/delete',
            'contentcategory/index' => 'content/Category/index',
            'contentcategory/detail' => 'content/Category/edit',
            'contentcategory/save' => 'content/Category/edit',
            'contentcategory/delete' => 'content/Category/delete',
            'contentannounce/index' => 'content/Announce/index',
            'contentannounce/detail' => 'content/Announce/edit',
            'contentannounce/save' => 'content/Announce/edit',
            'contentannounce/delete' => 'content/Announce/delete',
            'systemauth/index' => 'admin/Auth/index',
            'systemauth/detail' => 'admin/Auth/edit',
            'systemauth/meta' => 'admin/Auth/add',
            'systemauth/save' => 'admin/Auth/edit',
            'systemauth/delete' => 'admin/Auth/delete',
            'systemauth/state' => 'admin/Auth/edit',
            'systemmenu/index' => 'admin/Menu/index',
            'systemmenu/detail' => 'admin/Menu/edit',
            'systemmenu/save' => 'admin/Menu/edit',
            'systemmenu/delete' => 'admin/Menu/delete',
            'systemmenu/state' => 'admin/Menu/state',
            'systemgroup/index' => 'admin/Group/index',
            'systemgroup/detail' => 'admin/Group/edit',
            'systemgroup/createmeta' => 'admin/Group/add',
            'systemgroup/save' => 'admin/Group/edit',
            'systemgroup/delete' => 'admin/Group/delete',
            'systemconfig/index' => 'admin/Config/index',
            'systemconfig/detail' => 'admin/Config/edit',
            'systemconfig/meta' => 'admin/Config/add',
            'systemconfig/save' => 'admin/Config/edit',
            'systemconfig/delete' => 'admin/Config/delete',
            'systemconfig/groupdetail' => 'admin/Config/group_edit',
            'systemconfig/groupsave' => 'admin/Config/group_edit',
            'systemconfig/groupdelete' => 'admin/Config/group_delete',
            'systemconfig/configpage' => 'admin/Config/config',
            'systemconfig/configpagesave' => 'admin/Config/config',
            'systemuser/index' => 'member/Users/index',
            'systemuser/detail' => 'member/Users/edit',
            'systemuser/save' => 'member/Users/edit',
            'systemuser/create' => 'member/Users/add',
            'systemuser/approve' => 'member/Users/approval',
            'systemuser/decline' => 'member/Users/decline',
            'systemuser/forbid' => 'member/Users/forbidden',
            'systemuser/unforbid' => 'member/Users/un_forbidden',
            'systemuser/forbiddenip' => 'member/Users/forbidden_ip',
            'systemuser/recover' => 'member/Users/manager',
            'systemuser/remove' => 'member/Users/manager',
            'systemuser/integrallogs' => 'member/Users/integral',
            'systemuser/integralaward' => 'member/Users/integral',
            'systemverify/index' => 'member/Verify/index',
            'systemverify/detail' => 'member/Verify/preview',
            'systemverify/approve' => 'member/Verify/manager',
            'systemverify/decline' => 'member/Verify/manager',
            'systemforbiddenip/index' => 'member/Forbidden/ips',
            'systemforbiddenip/add' => 'member/Forbidden/add_ip',
            'systemforbiddenip/remove' => 'member/Forbidden/un_forbidden_ip',
        ];

        $key = $controller . '/' . $action;
        if (isset($mapped[$key])) {
            return $mapped[$key];
        }

        return $this->request->controller() . '/' . $action;
    }

    protected function matchAction($actions): bool
    {
        if (is_string($actions)) {
            $actions = explode(',', $actions);
        }
        if (!is_array($actions) || !$actions) {
            return false;
        }

        $action = strtolower($this->request->action());
        foreach ($actions as $candidate) {
            $candidate = strtolower(trim((string) $candidate));
            if ($candidate === '*' || $candidate === $action) {
                return true;
            }
        }
        return false;
    }

    protected function getAdminProfile(): array
    {
        if (!$this->user_info) {
            return [];
        }

        return $this->consoleService->formatAdminProfile($this->user_info);
    }

    protected function getAdminPermissionNames(): array
    {
        if (!$this->user_id) {
            return [];
        }

        return $this->consoleService->getAdminPermissionNames($this->user_id);
    }

    protected function getAdminMenuTree(): array
    {
        if (!$this->user_id) {
            return [];
        }

        $allowedRules = array_flip($this->getAdminPermissionNames());
        $menuRules = db('admin_auth')
            ->where('status', 1)
            ->order(['sort' => 'asc', 'id' => 'asc'])
            ->select()
            ->toArray();

        if (!$menuRules) {
            return [];
        }

        $items = [];
        foreach ($menuRules as $rule) {
            $name = trim((string) ($rule['name'] ?? ''));
            if ($name !== '' && !isset($allowedRules[$name])) {
                continue;
            }

            $ruleId = intval($rule['id'] ?? 0);
            $items[] = [
                'id' => $ruleId,
                'pid' => intval($rule['pid']),
                'title' => (string) ($rule['title'] ?? ''),
                'icon' => (string) ($rule['icon'] ?? 'fa fa-circle-o'),
                'rule_name' => $name,
                'path' => $this->buildFrontendPath($name, $ruleId),
                'legacy_url' => $this->buildLegacyUrl($name, (string) ($rule['param'] ?? '')),
                'migration_status' => $this->resolveMigrationStatus($name),
                'children' => [],
            ];
        }

        return $this->buildTree($items);
    }

    protected function buildTree(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $grouped[intval($item['pid'])][] = $item;
        }

        $walker = function (int $pid) use (&$walker, $grouped): array {
            $branch = [];
            foreach ($grouped[$pid] ?? [] as $item) {
                $item['children'] = $walker(intval($item['id']));
                $branch[] = $item;
            }
            return $branch;
        };

        return $walker(0);
    }

    protected function buildLegacyUrl(string $name, string $param = ''): string
    {
        if ($name === '') {
            return '';
        }

        if ($this->isExternalMenuTarget($name)) {
            return $name;
        }

        if ($this->isUnsafeMenuTarget($name)) {
            return '';
        }

        $params = [];
        if ($param !== '') {
            parse_str($param, $params);
        }

        try {
            if (str_starts_with($name, 'plugins/')) {
                return backend_plugins_url(substr($name, 8), $params, true, false);
            }

            return backend_url($name, $params, true, false);
        } catch (\Throwable $e) {
            return '';
        }
    }

    protected function buildFrontendPath(string $name, int $id = 0): string
    {
        if ($name === '' || strcasecmp($name, 'Index/index') === 0) {
            return '/dashboard';
        }

        $mapped = [
            'admin/auth/index' => '/system/auths',
            'admin/menu/index' => '/system/menus',
            'admin/group/index' => '/system/groups',
            'admin/config/index' => '/system/configs',
            'admin/config/group' => '/system/configs',
            'content/article/index' => '/content/articles',
            'content/question/index' => '/content/questions',
            'content/answer/index' => '/content/answers',
            'content/approval/index' => '/content/approvals',
            'content/topic/index' => '/content/topics',
            'content/category/index' => '/content/categories',
            'content/announce/index' => '/content/announces',
            'member/users/index' => '/system/users',
            'member/verify/index' => '/system/verifies',
            'member/forbidden/ips' => '/system/forbidden-ips',
        ];

        $normalized = strtolower($name);
        if (isset($mapped[$normalized])) {
            return $mapped[$normalized];
        }

        return '/legacy/' . $this->buildLegacyRouteKey($name, $id);
    }

    protected function resolveMigrationStatus(string $name): string
    {
        if ($name === '' || strcasecmp($name, 'Index/index') === 0) {
            return 'vben-ready';
        }

        if (in_array(strtolower($name), [
            'admin/auth/index',
            'admin/menu/index',
            'admin/group/index',
            'admin/config/index',
            'admin/config/group',
            'content/article/index',
            'content/question/index',
            'content/answer/index',
            'content/approval/index',
            'content/topic/index',
            'content/category/index',
            'content/announce/index',
            'member/users/index',
            'member/verify/index',
            'member/forbidden/ips',
        ], true)) {
            return 'vben-ready';
        }

        return 'legacy';
    }

    protected function buildLegacyRouteKey(string $name, int $id = 0): string
    {
        $normalized = strtolower($name);
        $normalized = preg_replace('/[^a-z0-9]+/', '-', $normalized) ?: '';
        $normalized = trim($normalized, '-');

        if ($normalized !== '') {
            return $normalized;
        }

        if ($id > 0) {
            return 'menu-' . $id;
        }

        return 'legacy-entry';
    }

    protected function isExternalMenuTarget(string $name): bool
    {
        return preg_match('/^(https?:)?\/\//i', $name) === 1;
    }

    protected function isUnsafeMenuTarget(string $name): bool
    {
        $normalized = strtolower(trim($name));
        return $normalized === '#' || str_starts_with($normalized, 'javascript:');
    }
}
