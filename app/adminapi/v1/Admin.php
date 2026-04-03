<?php

namespace app\adminapi\v1;

use app\common\controller\AdminApi;
use app\common\library\helper\AuthHelper;

class Admin extends AdminApi
{
    protected $noNeedRight = ['*'];

    public function login()
    {
        if ($this->request->isPost()) {
            $username = trim((string) $this->request->post('username', ''));
            $password = (string) $this->request->post('password', '');
            $token = (string) $this->request->post('token', '');

            if ($username === '' || $password === '') {
                $this->apiError('用户名和密码不能为空');
            }

            if ($token !== '') {
                $decoded = authCode($password, 'DECODE', $token);
                if (is_string($decoded) && $decoded !== '') {
                    $password = $decoded;
                }
            }

            if (!AuthHelper::instance()->login($username, $password)) {
                $this->apiError('账号或密码错误');
            }

            session('admin_logout_locked', null);
            $this->bootstrapAdminSession();
            $this->apiSuccess('登录成功', $this->buildBootstrapPayload());
        }

        if ($this->user_id) {
            $this->apiSuccess('已登录', $this->buildBootstrapPayload());
        }

        $this->apiResult([
            'logged_in' => false,
            'login_path' => '/adminapi.php/Admin/login',
        ]);
    }

    public function logout()
    {
        session('admin_logout_locked', 1);
        session('admin_user_info', null);
        session('admin_login_user_info', null);
        session('admin_login_uid', null);
        $this->apiSuccess('退出成功', ['logged_out' => true]);
    }

    public function me()
    {
        $this->apiResult([
            'user' => $this->getAdminProfile(),
            'permissions' => $this->getAdminPermissionNames(),
        ]);
    }

    public function menu()
    {
        $this->apiResult([
            'home' => [
                'title' => '仪表盘',
                'path' => '/dashboard',
            ],
            'menus' => $this->getAdminMenuTree(),
        ]);
    }

    public function dashboard()
    {
        $this->apiResult($this->consoleService->getDashboardPayload());
    }

    protected function buildBootstrapPayload(): array
    {
        return [
            'user' => $this->getAdminProfile(),
            'permissions' => $this->getAdminPermissionNames(),
            'menus' => $this->getAdminMenuTree(),
            'home' => [
                'title' => '仪表盘',
                'path' => '/dashboard',
            ],
        ];
    }
}
