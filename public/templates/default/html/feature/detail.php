{extend name="$theme_block" /}
{block name="header"}
<style>
    .aw-feature-detail-shell {
        margin-top: -96px;
    }
    .aw-feature-detail-card {
        padding: 22px;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 18px 44px rgba(15, 23, 42, 0.08);
    }
    .aw-feature-detail-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 14px 0 18px;
    }
    .aw-feature-detail-meta span {
        display: inline-flex;
        align-items: center;
        padding: 7px 12px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 600;
    }
    .aw-feature-detail-note {
        margin-top: 16px;
        padding: 14px 16px;
        border-radius: 16px;
        background: linear-gradient(180deg, #f8fbff 0%, #eef7f3 100%);
        border: 1px solid #e4edf7;
        color: #475569;
        line-height: 1.8;
    }
    .aw-feature-detail-feed-note {
        padding: 14px 20px 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.7;
    }
    .aw-feature-detail-type-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 12px 20px 0;
    }
    .aw-feature-detail-type-tabs a {
        padding: 6px 14px;
        border-radius: 999px;
        border: 1px solid #dbe7f3;
        background: #fff;
        color: #60758b;
        font-size: 13px;
    }
    .aw-feature-detail-type-tabs a.active {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #fff;
    }
</style>
<div class="w-top-img" style="height:320px;background:
    linear-gradient(180deg, rgba(3,7,18,.22) 0%, rgba(3,7,18,.46) 100%),
    url('{$info.image}') center center;background-size:cover;">
    {include file="global/nav"}
</div>
{/block}
{block name="main"}
<div class="container px-0 aw-feature-detail-shell">
    <div class="aw-feature-detail-card">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb px-0 pt-0" style="background: transparent">
                <li class="breadcrumb-item"><a href="{$baseUrl}">{:L('首页')}</a></li>
                <li class="breadcrumb-item"><a href="{:url('feature/index')}">{:L('观察专题')}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{$info.title}</li>
            </ol>
        </nav>

        <h2 class="font-weight-bolder">{$info.title}</h2>
        <p class="w-100 text-muted mt-3">{$info.description|raw}</p>
        <div class="aw-feature-detail-meta">
            <span>{:L('观察专题')}</span>
            <span>{:L('持续更新')}</span>
            <span>{:L('围绕主题')}</span>
        </div>
        <div class="page-detail-topic mt-3">
            {if !empty($topics)}
            <ul id="awTopicList" class="d-inline p-0">
                {volist name="$topics" id="v"}
                <li class="d-inline-block aw-tag"><a href="{:url('topic/detail',['id'=>$v['id']])}" class="aw-topic" data-id="{$v.id}"><em class="tag">{$v.title}</em></a></li>
                {/volist}
            </ul>
            {/if}
        </div>
        <div class="aw-feature-detail-note">
            {:L('这个观察专题会持续收录同一主题下的重要动态、代表内容和阶段判断。优先把高价值观察沉淀到这里，再逐步升级成综述或知识章节。')}
        </div>
    </div>
</div>

<div class="container aw-wrap mt-2">
    <div class="row">
        <div class="aw-left col-md-9 col-sm-12 px-0">
            <div class="aw-nav-container clearfix bg-white pl-3">
                <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block">
                    <li class="nav-item"><a class="nav-link {if $sort=='new'}active{/if}" data-pjax="tabMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>'new'])}">{:L('观察动态')}</a></li>
                    <li class="nav-item"><a class="nav-link {if $sort=='hot'}active{/if}" data-pjax="tabMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>'hot'])}">{:L('热门内容')}</a></li>
                    <li class="nav-item"><a class="nav-link {if $sort=='best'}active{/if}" data-pjax="tabMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>'best'])}">{:L('最佳回复')}</a></li>
                </ul>
            </div>
            <div class="tab-content" id="tabMain">
                <div class="bg-white">
                    <div class="aw-feature-detail-type-tabs aw-pjax-a">
                        <a class="{if $content_type=='all'}active{/if}" data-pjax="tabMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>$sort,'content_type'=>'all'])}">{:frelink_content_label('all')}</a>
                        <a class="{if $content_type=='question'}active{/if}" data-pjax="tabMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>$sort,'content_type'=>'question'])}">{:frelink_content_label('question')}</a>
                        <a class="{if $content_type=='research'}active{/if}" data-pjax="tabMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>$sort,'content_type'=>'research'])}">{:frelink_content_label('research')}</a>
                        <a class="{if $content_type=='fragment'}active{/if}" data-pjax="tabMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>$sort,'content_type'=>'fragment'])}">{:frelink_content_label('fragment')}</a>
                        <a class="{if $content_type=='faq'}active{/if}" data-pjax="tabMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>$sort,'content_type'=>'faq'])}">{:frelink_content_label('faq')}</a>
                    </div>
                    <div class="aw-feature-detail-feed-note">
                        {if $sort=='best' && !in_array($content_type,['all','question'])}
                        {:L('最佳回复当前只适用于 FAQ 条目，切回 FAQ 或全部内容可查看代表性回答。')}
                        {elseif $content_type=='question'}
                        {:frelink_content_description('question')}
                        {elseif $content_type=='research' /}
                        {:frelink_content_description('research')}
                        {elseif $content_type=='fragment' /}
                        {:frelink_content_description('fragment')}
                        {elseif $content_type=='faq' /}
                        {:frelink_content_description('faq')}
                        {elseif $sort=='new'}
                        {:L('按时间查看这个观察专题里的持续更新和新近判断。')}
                        {elseif $sort=='hot' /}
                        {:L('优先查看在这个观察专题里更受关注、更容易引发后续讨论的内容。')}
                        {else/}
                        {:L('优先查看在这个观察专题里更具代表性、反馈质量更高的内容。')}
                        {/if}
                    </div>
                    <div class="aw-common-list">
                        {:widget('common/feature_content',['sort'=>$sort,'feature_id'=>$info['id'],'content_type'=>$content_type])}
                    </div>
                </div>
            </div>
        </div>

        <div class="aw-right radius col-md-3 px-xs-0 pr-0">
            <!--侧边栏顶部钩子-->
            {:hook('sidebarTop')}

            {:widget('sidebar/writeNav')}

            {:widget('sidebar/announce')}

            {:widget('sidebar/focusTopic',['uid'=>$user_id])}

            {:widget('sidebar/hotTopic',['uid'=>$user_id])}

            {:widget('sidebar/hotColumn',['uid'=>$user_id,'sort'=>'hot'])}

            {:widget('sidebar/hotUsers',['uid'=>$user_id])}

            <!--侧边栏底部钩子-->
            {:hook('sidebarBottom')}
        </div>
    </div>
</div>
{/block}
