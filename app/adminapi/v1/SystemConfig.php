<?php

namespace app\adminapi\v1;

use app\common\controller\AdminApi;
use app\common\service\admin\AdminConfigService;

class SystemConfig extends AdminApi
{
    protected $configService;

    protected function initialize()
    {
        parent::initialize();
        $this->configService = new AdminConfigService();
    }

    public function index()
    {
        $groupId = intval($this->request->param('group_id', 0));
        $keyword = trim((string) $this->request->param('keyword', ''));

        $this->apiResult($this->configService->getOverview($groupId, $keyword));
    }

    public function detail()
    {
        $id = intval($this->request->param('id', 0));
        $detail = $this->configService->getDetail($id);
        if (!$detail) {
            $this->apiError('配置不存在');
        }

        $this->apiResult($detail);
    }

    public function meta()
    {
        $this->apiResult($this->configService->getEditorMeta());
    }

    public function save()
    {
        if (!$this->request->isPost()) {
            $this->apiError('请求参数错误');
        }

        $result = $this->configService->saveConfig($this->request->post());
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

        $result = $this->configService->deleteConfig(intval($this->request->post('id', 0)));
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '删除失败'));
        }

        $this->apiSuccess((string) ($result['msg'] ?? '删除成功'));
    }

    public function groupDetail()
    {
        $id = intval($this->request->param('id', 0));
        $detail = $this->configService->getGroupDetail($id);
        if (!$detail) {
            $this->apiError('配置分组不存在');
        }

        $this->apiResult($detail);
    }

    public function groupSave()
    {
        if (!$this->request->isPost()) {
            $this->apiError('请求参数错误');
        }

        $result = $this->configService->saveGroup($this->request->post());
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '保存失败'));
        }

        $this->apiSuccess((string) ($result['msg'] ?? '保存成功'), $result['data'] ?? []);
    }

    public function groupDelete()
    {
        if (!$this->request->isPost()) {
            $this->apiError('请求参数错误');
        }

        $result = $this->configService->deleteGroup(intval($this->request->post('id', 0)));
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '删除失败'));
        }

        $this->apiSuccess((string) ($result['msg'] ?? '删除成功'));
    }

    public function configPage()
    {
        $groupId = intval($this->request->param('group_id', 0));
        $this->apiResult($this->configService->getConfigPage($groupId));
    }

    public function configPageSave()
    {
        if (!$this->request->isPost()) {
            $this->apiError('请求参数错误');
        }

        $groupId = intval($this->request->post('group_id', 0));
        $values = $this->request->post('values/a', []);
        $result = $this->configService->saveConfigPage($groupId, $values);
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '保存失败'));
        }

        $this->apiSuccess((string) ($result['msg'] ?? '保存成功'));
    }
}
