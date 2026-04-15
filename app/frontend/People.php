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
use app\common\library\helper\LogHelper;
use app\logic\common\FocusLogic;
use app\model\Users as UsersModel;

/**
 * 用户模块
 * Class People
 */
class People extends Frontend
{
    protected $needLogin = [];
    protected $allowedTypes = ['dynamic', 'question', 'answer', 'article', 'friend', 'fans', 'column', 'topic'];
	public function index()
	{
	    $url = $this->request->param('name');
        //防止sql注入报错
        $url = str_replace(['"','"'],'',$url);
        $url = is_array($url)?end($url):$url;

        $url = $url?: $this->user_info['url_token'];
        if(!$url)
        {
            $this->error('访问页面不存在');
        }

        $uid = db('users')->whereRaw('url_token = "'.$url.'" OR user_name="'.$url.'"')->value('uid');
        if(!$uid)
        {
            $this->error('用户不存在','/');
        }

        $user =UsersModel::getUserInfo((int)$uid);

        if(!$user || $user['status']===2)
        {
            $this->error('当前用户不存在','/');
        }

        UsersModel::updateUsersViews($uid,$this->user_id);
        $user['draft_count'] = db('draft')->where(['uid'=> (int)$uid])->count();
        $user['favorite_count'] = db('users_favorite')->where(['uid'=> (int)$uid])->count();
        $user['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id,'user',$user['uid']);

        $type = $this->request->param('type','dynamic');
        if (!in_array($type, $this->allowedTypes, true))
        {
            $type = 'dynamic';
        }

        $questionCount = LogHelper::getActionLogCount('publish_question',$user['uid'],$this->user_id);
        $answerCount = LogHelper::getActionLogCount('publish_answer',$user['uid'],$this->user_id);
        $articleCount = LogHelper::getActionLogCount('publish_article',$user['uid'],$this->user_id);

        $this->assign('type',$type);
        $this->assign('question_count',$questionCount);
        $this->assign('answer_count',$answerCount);
        $this->assign('article_count',$articleCount);
        $this->assign('user',$user);
        $this->assign('profile_stats', [
            ['label' => L('关注'), 'value' => (int)$user['friend_count']],
            ['label' => L('粉丝'), 'value' => (int)$user['fans_count']],
            ['label' => L($this->settings['score_unit']), 'value' => (int)$user['integral']],
            ['label' => L($this->settings['power_unit']), 'value' => (int)$user['reputation']],
            ['label' => L('访问'), 'value' => (int)$user['views_count']],
        ]);
        $this->assign('post_tabs', [
            ['type' => 'dynamic', 'label' => L('动态')],
            ['type' => 'question', 'label' => L('FAQ'), 'count' => (int)$questionCount],
            ['type' => 'answer', 'label' => L('补充'), 'count' => (int)$answerCount],
            ['type' => 'article', 'label' => L('内容'), 'count' => (int)$articleCount],
            ['type' => 'friend', 'label' => L('关注的人')],
            ['type' => 'fans', 'label' => L('关注TA的')],
            ['type' => 'column', 'label' => L('关注的专栏')],
            ['type' => 'topic', 'label' => L('关注的话题')],
        ]);

        $seo_title = trim(strip_tags($user['nick_name'])) . '的主页动态、问答与文章内容';
        $this->TDK($seo_title);
        return $this->fetch();
	}

	/**
	 * 用户列表
	 */
	public function lists()
	{
		$page = $this->request->param('page',1);
		$sort = $this->request->param('sort','integral');
        if($page>5)
        {
            $page=5;
        }
        $order = [];
        $where[] =['status','=',1];
        switch ($sort)
        {
            //威望榜
            case 'reputation':
                $order['reputation']='DESC';
                $where[] = ['reputation','>',3];
                break;
            //活跃榜
            default :
                $order['integral']='DESC';
                $where[] = ['integral','>',500];
                break;
        }
		$data = UsersModel::getUserList($where,$order,$page,20,$this->user_id);
		$this->assign($data);
		$this->assign('sort',$sort);
		$this->TDK('大咖');
		return $this->fetch();
	}
}
