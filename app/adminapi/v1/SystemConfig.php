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
}
