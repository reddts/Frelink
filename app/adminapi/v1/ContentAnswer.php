<?php

namespace app\adminapi\v1;

use app\common\controller\AdminApi;
use app\common\service\admin\ContentAnswerService;

class ContentAnswer extends AdminApi
{
    protected $service;

    protected function initialize()
    {
        parent::initialize();
        $this->service = new ContentAnswerService();
    }

    public function index()
    {
        $this->apiResult($this->service->getOverview(
            intval($this->request->param('status', 1)),
            trim((string) $this->request->param('keyword', ''))
        ));
    }

    public function detail()
    {
        $detail = $this->service->getDetail(intval($this->request->param('id', 0)));
        if (!$detail) {
            $this->apiError('回答不存在');
        }
        $this->apiResult($detail);
    }

    public function save()
    {
        if (!$this->request->isPost()) {
            $this->apiError('请求参数错误');
        }

        $result = $this->service->save($this->request->post());
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '保存失败'));
        }
        $this->apiSuccess((string) ($result['msg'] ?? '保存成功'), $result['data'] ?? []);
    }

    public function delete()
    {
        $result = $this->service->delete(
            $this->request->param('id', ''),
            intval($this->request->param('real', 0)) === 1
        );
        if (intval($result['code'] ?? 0) !== 1) {
            $this->apiError((string) ($result['msg'] ?? '删除失败'));
        }
        $this->apiSuccess((string) ($result['msg'] ?? '删除成功'));
    }
}
