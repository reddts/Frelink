<?php

namespace app\adminapi\v1;

use app\common\controller\AdminApi;
use app\common\service\admin\AdminVerifyService;

class SystemVerify extends AdminApi
{
    protected $service;

    protected function initialize()
    {
        parent::initialize();
        $this->service = new AdminVerifyService();
    }

    public function index()
    {
        $status = intval($this->request->param('status', 1));
        $type = trim((string) $this->request->param('type', ''));
        $this->apiResult($this->service->getOverview($status, $type));
    }

    public function detail()
    {
        $id = intval($this->request->param('id', 0));
        $detail = $this->service->getDetail($id);
        if (!$detail) {
            $this->apiError('审核数据不存在');
        }

        $this->apiResult($detail);
    }

    public function approve()
    {
        $result = $this->service->approve($this->request->param('id', ''));
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '操作失败'));
        }

        $this->apiSuccess((string) ($result['msg'] ?? '操作成功'));
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
}
