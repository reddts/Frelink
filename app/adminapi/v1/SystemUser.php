<?php

namespace app\adminapi\v1;

use app\common\controller\AdminApi;
use app\common\service\admin\AdminUserService;

class SystemUser extends AdminApi
{
    protected $userService;

    protected function initialize()
    {
        parent::initialize();
        $this->userService = new AdminUserService();
    }

    public function index()
    {
        $status = intval($this->request->param('status', 1));
        $keyword = trim((string) $this->request->param('keyword', ''));
        $forbiddenIp = intval($this->request->param('forbidden_ip', 0));

        $this->apiResult($this->userService->getOverview($status, $keyword, $forbiddenIp));
    }

    public function detail()
    {
        $uid = intval($this->request->param('uid', 0));
        $detail = $this->userService->getDetail($uid);
        if (!$detail) {
            $this->apiError('用户不存在');
        }

        $this->apiResult($detail);
    }

    public function save()
    {
        if (!$this->request->isPost()) {
            $this->apiError('请求参数错误');
        }

        $result = $this->userService->save($this->request->post());
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '保存失败'));
        }

        $this->apiSuccess((string) ($result['msg'] ?? '保存成功'), $result['data'] ?? []);
    }

    public function create()
    {
        if (!$this->request->isPost()) {
            $this->apiError('请求参数错误');
        }

        $result = $this->userService->create($this->request->post());
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '添加失败'));
        }

        $this->apiSuccess((string) ($result['msg'] ?? '添加成功'), $result['data'] ?? []);
    }

    public function approve()
    {
        $result = $this->userService->approve($this->request->param('id', ''));
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '操作失败'));
        }
        $this->apiSuccess((string) ($result['msg'] ?? '操作成功'));
    }

    public function decline()
    {
        $result = $this->userService->decline($this->request->param('id', ''));
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '操作失败'));
        }
        $this->apiSuccess((string) ($result['msg'] ?? '操作成功'));
    }

    public function forbid()
    {
        if (!$this->request->isPost()) {
            $this->apiError('请求参数错误');
        }

        $result = $this->userService->forbid(
            $this->request->post('id', ''),
            trim((string) $this->request->post('forbidden_time', '')),
            trim((string) $this->request->post('forbidden_reason', ''))
        );
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '操作失败'));
        }
        $this->apiSuccess((string) ($result['msg'] ?? '操作成功'));
    }

    public function unForbid()
    {
        $result = $this->userService->unForbid($this->request->param('id', ''));
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '操作失败'));
        }
        $this->apiSuccess((string) ($result['msg'] ?? '操作成功'));
    }

    public function forbiddenIp()
    {
        $relieve = trim((string) $this->request->param('action', 'forbidden')) === 'relieve';
        $result = $this->userService->forbiddenIp($this->request->param('id', ''), $relieve);
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '操作失败'));
        }
        $this->apiSuccess((string) ($result['msg'] ?? '操作成功'));
    }
}
