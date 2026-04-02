<?php

namespace app\common\controller;

use app\common\library\helper\AuthHelper;
use app\model\Users;
use think\App;
use think\helper\Str;

abstract class AdminApi extends Base
{
    protected $auth;
    protected $user_id = 0;
    protected $user_info = [];
    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['login', 'logout', 'me', 'menu', 'dashboard'];

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->checkIpAllowed();
        $this->auth = AuthHelper::instance();
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
        return $this->request->controller() . '/' . strtolower(Str::snake($this->request->action()));
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

        return [
            'uid' => intval($this->user_info['uid'] ?? 0),
            'user_name' => (string) ($this->user_info['user_name'] ?? ''),
            'nick_name' => (string) ($this->user_info['nick_name'] ?? ''),
            'email' => (string) ($this->user_info['email'] ?? ''),
            'mobile' => (string) ($this->user_info['mobile'] ?? ''),
            'avatar' => (string) ($this->user_info['avatar'] ?? '/static/common/image/default-avatar.svg'),
            'group_id' => intval($this->user_info['group_id'] ?? 0),
            'group_name' => (string) ($this->user_info['group_name'] ?? ''),
            'is_super_admin' => $this->auth->isSuperAdmin(),
            'permission' => $this->user_info['permission'] ?? [],
        ];
    }

    protected function getAdminPermissionNames(): array
    {
        if (!$this->user_id) {
            return [];
        }

        $ruleIds = $this->auth->getRuleIds($this->user_id);
        if (!$ruleIds) {
            return [];
        }

        return array_values(array_unique(db('admin_auth')
            ->whereIn('id', $ruleIds)
            ->column('name')));
    }

    protected function getAdminMenuTree(): array
    {
        if (!$this->user_id) {
            return [];
        }

        $allowedRules = array_flip($this->getAdminPermissionNames());
        $menuRules = db('admin_auth')
            ->where('status', 1)
            ->where('menu', 1)
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

            $items[] = [
                'id' => intval($rule['id']),
                'pid' => intval($rule['pid']),
                'title' => (string) ($rule['title'] ?? ''),
                'icon' => (string) ($rule['icon'] ?? 'fa fa-circle-o'),
                'rule_name' => $name,
                'path' => $this->buildFrontendPath($name),
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

        $params = [];
        if ($param !== '') {
            parse_str($param, $params);
        }

        if (str_starts_with($name, 'plugins/')) {
            return backend_plugins_url(substr($name, 8), $params, true, false);
        }

        return backend_url($name, $params, true, false);
    }

    protected function buildFrontendPath(string $name): string
    {
        if ($name === '' || strcasecmp($name, 'Index/index') === 0) {
            return '/dashboard';
        }

        return '/legacy/' . ltrim(strtolower($name), '/');
    }

    protected function resolveMigrationStatus(string $name): string
    {
        if ($name === '' || strcasecmp($name, 'Index/index') === 0) {
            return 'vben-ready';
        }

        return 'legacy';
    }
}
