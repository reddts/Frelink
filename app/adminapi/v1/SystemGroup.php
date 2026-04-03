<?php

namespace app\adminapi\v1;

use app\common\controller\AdminApi;
use app\common\service\admin\AdminGroupService;

class SystemGroup extends AdminApi
{
    protected $groupService;

    protected function initialize()
    {
        parent::initialize();
        $this->groupService = new AdminGroupService($this->auth);
    }

    public function index()
    {
        $keyword = trim((string) $this->request->param('keyword', ''));
        $this->apiResult([
            'keyword' => $keyword,
            'list' => $this->groupService->getList($keyword),
        ]);
    }

    public function detail()
    {
        $id = intval($this->request->param('id', 0));
        $detail = $this->groupService->getDetail($id);
        if (!$detail) {
            $this->apiError('系统组不存在');
        }
        $this->apiResult($detail);
    }

    public function createMeta()
    {
        $this->apiResult($this->groupService->getCreateMeta());
    }

    public function save()
    {
        if (!$this->request->isPost()) {
            $this->apiError('请求参数错误');
        }

        $result = $this->groupService->save($this->request->post());
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

        $result = $this->groupService->delete(intval($this->request->post('id', 0)));
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '删除失败'));
        }

        $this->apiSuccess((string) ($result['msg'] ?? '删除成功'));
    }
}
