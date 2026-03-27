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
use app\common\library\helper\HtmlHelper;

class Page extends Frontend
{
    public function index()
    {
        $id = $this->request->get('id',0);
        $url_name = $this->request->get('url_name','');
        $where[]=['status','=',1];
        if($id)
        {
            $where[]=['id','=',$id];
        }

        if($url_name)
        {
            $where[]=['url_name','=',$url_name];
        }

        $info = db('page')->where($where)->find();
        if($info)
        {
            $info['contents'] = HtmlHelper::normalizeContentHtml(htmlspecialchars_decode($info['contents']));
            $this->assign(['info' => $info]);
            $this->TDK($info['title'],$info['keywords'],$info['description']);
            return $this->fetch();
        }
        $this->error('页面不存在','/');
    }

    /**
     * 积分规则页面
     */
    public function score()
    {
        $list = db('integral_rule')->where([['integral','<>','0']])->select()->toArray();

        $this->assign([
            'list'=>$list
        ]);
        return $this->fetch();
    }

    public function api()
    {
        $docPath = rtrim(app()->getRootPath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'api-v1.md';
        if (!is_file($docPath)) {
            $this->error('API 文档尚未生成，请先执行 api:doc');
        }

        $markdown = file_get_contents($docPath);
        if ($markdown === false) {
            $this->error('API 文档读取失败');
        }

        $this->assign([
            'info' => [
                'title' => 'API 接口文档',
                'description' => '由 `api:doc` 命令自动生成的 Frelink API v1 接口说明。',
                'contents' => HtmlHelper::markdownToHtml($markdown),
            ],
        ]);
        $this->TDK('API 接口文档 - ' . get_setting('site_name'), '', 'Frelink API v1 自动生成接口说明文档');
        return $this->fetch('index');
    }
}
