<?php

namespace app\adminapi\v1;

use app\common\controller\AdminApi;
use app\common\service\admin\AdminAuthService;

class SystemAuth extends AdminApi
{
    protected $authService;

    protected function initialize()
    {
        parent::initialize();
        $this->authService = new AdminAuthService();
    }

    public function index()
    {
        $this->apiResult([
            'list' => $this->authService->getTreeList(),
            'parent_options' => $this->authService->getParentOptions(),
        ]);
    }

    public function detail()
    {
        $id = intval($this->request->param('id', 0));
        $detail = $this->authService->getDetail($id);
        if (!$detail) {
            $this->apiError('权限节点不存在');
        }

        $this->apiResult($detail);
    }

    public function meta()
    {
        $this->apiResult($this->authService->getEditorMeta());
    }

    public function save()
    {
        if (!$this->request->isPost()) {
            $this->apiError('请求参数错误');
        }

        $result = $this->authService->save($this->request->post());
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '保存失败'));
        }

        $this->apiSuccess((string) ($result['msg'] ?? '保存成功'), $result['data'] ?? []);
    }

    public function delete()
    {
        if (!$this->request->isPost()) {
            $this->apiError('请求参数错误');
        }

        $result = $this->authService->delete(intval($this->request->post('id', 0)));
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '删除失败'));
        }

        $this->apiSuccess((string) ($result['msg'] ?? '删除成功'));
    }

    public function state()
    {
        if (!$this->request->isPost()) {
            $this->apiError('请求参数错误');
        }

        $result = $this->authService->toggleState(intval($this->request->post('id', 0)));
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '修改失败'));
        }

        $this->apiSuccess((string) ($result['msg'] ?? '修改成功'));
    }
}
