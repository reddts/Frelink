<?php
namespace app\frontend;

use app\common\controller\Frontend;
use app\common\library\helper\HtmlHelper;

/**
 * 站点公告
 */
class Announce extends Frontend
{
    public function index()
    {

    }

    public function detail()
    {
        $id = $this->request->get('id',0,'intval');
        $info = db('announce')->where(['status'=>1,'id'=>$id])->find();
        if(!$info)
        {
            $this->error('公告不存在');
        }
        $info['message'] = HtmlHelper::normalizeContentHtml(htmlspecialchars_decode($info['message']));
        $this->assign([
            'info'=>$info
        ]);
        return $this->fetch();
    }
}
