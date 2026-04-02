<?php

namespace app\api\v1;

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
            'login_path' => '/api/Admin/login',
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
        $stats = [
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
        ];

        $this->apiResult([
            'title' => 'Frelink 管理端',
            'subtitle' => 'M1 已完成登录、菜单、权限壳层接入，业务模块迁移按计划继续推进。',
            'stats' => $stats,
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
        ]);
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
