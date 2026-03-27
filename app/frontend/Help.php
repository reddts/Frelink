<?php
namespace app\frontend;

use app\common\controller\Frontend;
use app\common\library\helper\HtmlHelper;
use app\model\Help as HelpModel;
class Help extends Frontend
{
    public function index()
    {
        $page = $this->request->param('page',1,'intval');
        $data = HelpModel::getHelpChapterList($page);
        $this->assign('map_summary', HelpModel::getKnowledgeMapSummary());
        $this->assign('topic_connections', HelpModel::getKnowledgeMapTopicConnections(8, 2));
        $this->assign($data);
        $this->TDK('知识地图 - ' . get_setting('site_name'));
        return $this->fetch();
    }

    public function detail()
    {
        $token = $this->request->param('token','','trim');
        $page = $this->request->param('page',1,'intval');
        $contentType = $this->request->param('content_type', 'all', 'trim');
        $allowedContentTypes = ['all', 'question', 'article'];
        if (!in_array($contentType, $allowedContentTypes, true)) {
            $contentType = 'all';
        }
        $info = db('help_chapter')->where(['url_token'=>$token,'status'=>1])->find();
        if(!$info) $this->error('帮助章节不存在','index');
        $data = HelpModel::getRelationHelpChapterList(
            $info['id'],
            $page,
            10,
            'tabMain',
            $contentType === 'all' ? '' : $contentType
        );
        $info['description'] = HtmlHelper::normalizeContentHtml(htmlspecialchars_decode((string) $info['description']));
        $chapterStats = HelpModel::getChapterRelationStats($info['id']);
        $relatedTopics = HelpModel::getChapterRelatedTopics($info, 6);
        $faqList = [];
        $contentList = [];

        foreach (($data['list'] ?? []) as $item) {
            if (($item['item_type'] ?? '') === 'question') {
                $faqList[] = $item;
            } else {
                $contentList[] = $item;
            }
        }

        $this->assign($data);
        $this->assign([
            'info'=>$info,
            'chapter_stats'=>$chapterStats,
            'related_topics'=>$relatedTopics,
            'faq_list'=>$faqList,
            'content_list'=>$contentList,
            'current_content_type' => $contentType,
        ]);
        $this->TDK($info['title'] . ' - 知识章节', '', str_cut(strip_tags((string)$info['description']), 0, 120));
        return $this->fetch();
    }
}
