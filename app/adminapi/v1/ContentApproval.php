<?php

namespace app\adminapi\v1;

use app\common\controller\AdminApi;
use app\common\service\admin\ContentApprovalService;

class ContentApproval extends AdminApi
{
    protected $service;

    protected function initialize()
    {
        parent::initialize();
        $this->service = new ContentApprovalService();
    }

    public function index()
    {
        $this->apiResult($this->service->getOverview(
            intval($this->request->param('status', 0)),
            trim((string) $this->request->param('type', '')),
            trim((string) $this->request->param('is_agent', ''))
        ));
    }

    public function detail()
    {
        $detail = $this->service->getDetail(intval($this->request->param('id', 0)));
        if (!$detail) {
            $this->apiError('审核记录不存在');
        }
        $this->apiResult($detail);
    }

    public function approve()
    {
        $result = $this->service->approve($this->request->param('id', ''));
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '审核失败'));
        }
        $this->apiSuccess((string) ($result['msg'] ?? '审核成功'));
    }

    public function decline()
    {
        if (!$this->request->isPost()) {
            $this->apiError('请求参数错误');
        }

        $result = $this->service->decline(
            $this->request->post('id', ''),
            trim((string) $this->request->post('reason', ''))
        );
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

        $result = $this->service->forbid(
            $this->request->post('uid', ''),
            trim((string) $this->request->post('forbidden_time', '')),
            trim((string) $this->request->post('forbidden_reason', ''))
        );
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '操作失败'));
        }
        $this->apiSuccess((string) ($result['msg'] ?? '操作成功'));
    }

    public function forbiddenIp()
    {
        $result = $this->service->forbiddenIp($this->request->param('uid', ''));
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '操作失败'));
        }
        $this->apiSuccess((string) ($result['msg'] ?? '操作成功'));
    }
}
