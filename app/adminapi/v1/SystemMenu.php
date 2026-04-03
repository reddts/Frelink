<?php

namespace app\adminapi\v1;

use app\common\controller\AdminApi;
use app\common\service\admin\AdminMenuService;

class SystemMenu extends AdminApi
{
    protected $menuService;

    protected function initialize()
    {
        parent::initialize();
        $this->menuService = new AdminMenuService();
    }

    public function index()
    {
        $group = trim((string) $this->request->param('group', 'nav'));
        if (!in_array($group, ['nav', 'footer'], true)) {
            $group = 'nav';
        }

        $this->apiResult([
            'group' => $group,
            'groups' => $this->menuService->getGroupTabs(),
            'list' => $this->menuService->getTreeList($group),
            'parent_options' => $this->menuService->getParentOptions($group),
        ]);
    }

    public function detail()
    {
        $id = intval($this->request->param('id', 0));
        $info = $this->menuService->getDetail($id);
        if (!$info) {
            $this->apiError('菜单不存在');
        }

        $this->apiResult($info);
    }

    public function save()
    {
        if (!$this->request->isPost()) {
            $this->apiError('请求参数错误');
        }

        $result = $this->menuService->save($this->request->post());
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

        $result = $this->menuService->delete(intval($this->request->post('id', 0)));
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

        $result = $this->menuService->toggleState(
            intval($this->request->post('id', 0)),
            trim((string) $this->request->post('field', ''))
        );
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '修改失败'));
        }

        $this->apiSuccess((string) ($result['msg'] ?? '修改成功'));
    }
}
