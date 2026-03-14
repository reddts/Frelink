<?php
// +----------------------------------------------------------------------
// | WeCenter 简称 WC
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://wecenter.isimpo.com
// +----------------------------------------------------------------------
// | WeCenter团队一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: WeCenter团队 <devteam@wecenter.com>
// +----------------------------------------------------------------------

namespace app\frontend;
use app\common\controller\Frontend;
use think\response\Response;

class Index extends Frontend
{
	public function index()
    {
        $sort = $this->request->param('sort','new');
        $type = $this->request->param('type');
        $page = $this->request->param('page', 1, 'intval');
        $isAjax = $this->request->isAjax() || $this->request->isPjax() || intval($this->request->param('_ajax', 0)) === 1 || intval($this->request->param('_ajax_open', 0)) === 1;
        $canPageCache = !$this->user_id && !$isAjax && $page === 1;
        $cacheKey = 'page_cache:index:' . md5(json_encode([
            'sort' => (string)$sort,
            'type' => (string)$type,
            'lang' => (string)$this->request->cookie('aws_lang', ''),
        ]));

        if ($canPageCache && ($cachedHtml = cache($cacheKey))) {
            return response($cachedHtml)->header(['X-Page-Cache' => 'HIT']);
        }

        $this->assign([
            'sort'=> $sort,
            'type'=>$type
        ]);
        $this->TDK(get_setting('site_name'));
        $html = $this->fetch();
        if ($canPageCache) {
            cache($cacheKey, $html, 60);
            return response($html)->header(['X-Page-Cache' => 'MISS']);
        }
		return $html;
	}
}
