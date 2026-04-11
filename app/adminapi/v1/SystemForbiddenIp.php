<?php

namespace app\adminapi\v1;

use app\common\controller\AdminApi;
use app\common\service\admin\AdminForbiddenIpService;

class SystemForbiddenIp extends AdminApi
{
    protected $service;

    protected function initialize()
    {
        parent::initialize();
        $this->service = new AdminForbiddenIpService();
    }

    public function index()
    {
        $ip = trim((string) $this->request->param('ip', ''));
        $this->apiResult($this->service->getOverview($ip));
    }

    public function add()
    {
        if (!$this->request->isPost()) {
            $this->apiError('请求参数错误');
        }

        $result = $this->service->add(trim((string) $this->request->post('ip', '')));
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '操作失败'));
        }

        $this->apiSuccess((string) ($result['msg'] ?? '操作成功'));
    }

    public function remove()
    {
        $result = $this->service->remove($this->request->param('id', ''));
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '操作失败'));
        }

        $this->apiSuccess((string) ($result['msg'] ?? '操作成功'));
    }
}
