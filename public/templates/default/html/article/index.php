{extend name="$theme_block" /}
{block name="style"}
<style>
    .aw-article-subnav {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 12px 24px;
        border-top: 1px solid #eef2f7;
        border-bottom: 1px solid #eef2f7;
        background: #fbfdff;
    }
    .aw-article-subnav .nav-link {
        padding: 6px 14px;
        border-radius: 999px;
        border: 1px solid #dbe7f3;
        color: #60758b;
        font-size: 13px;
        line-height: 1.2;
        background: #fff;
    }
    .aw-article-subnav .nav-link.active,
    .aw-article-subnav .nav-link:hover {
        color: #fff;
        background: #1d4ed8;
        border-color: #1d4ed8;
        text-decoration: none;
    }
    .aw-knowledge-hero {
        padding: 20px 24px 10px;
        border-bottom: 1px solid #eef2f7;
        background: linear-gradient(180deg, #fbfdff 0%, #fff 100%);
    }
    .aw-knowledge-hero h1 {
        margin: 0 0 8px;
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
    }
    .aw-knowledge-hero p {
        margin: 0;
        color: #64748b;
        line-height: 1.7;
    }
    .aw-page-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: rgba(255, 255, 255, 0.92);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .aw-page-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }
    .aw-page-chips span {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: rgba(255, 255, 255, 0.88);
        font-size: 13px;
    }
    .aw-knowledge-lanes {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        padding: 14px 24px 18px;
        border-bottom: 1px solid #eef2f7;
        background: #fff;
    }
    .aw-knowledge-spotlights {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        padding: 0 24px 18px;
        border-bottom: 1px solid #eef2f7;
        background: #fff;
    }
    .aw-knowledge-spotlight {
        display: block;
        padding: 16px;
        border-radius: 16px;
        background: linear-gradient(135deg, #f8fbff 0%, #eef6ff 100%);
        border: 1px solid #dbe7f3;
        color: #0f172a;
    }
    .aw-knowledge-spotlight:hover {
        text-decoration: none;
        transform: translateY(-1px);
        transition: all .2s ease;
    }
    .aw-knowledge-spotlight em {
        display: inline-flex;
        margin-bottom: 10px;
        padding: 5px 10px;
        border-radius: 999px;
        background: rgba(29, 78, 216, 0.08);
        color: #1d4ed8;
        font-style: normal;
        font-size: 12px;
        font-weight: 700;
    }
    .aw-knowledge-spotlight strong {
        display: block;
        margin-bottom: 6px;
        font-size: 18px;
    }
    .aw-knowledge-spotlight p {
        margin: 0 0 10px;
        color: #60758b;
        font-size: 13px;
        line-height: 1.7;
    }
    .aw-knowledge-spotlight-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 10px;
        color: #1e3a5f;
        font-size: 12px;
    }
    .aw-knowledge-spotlight-latest {
        color: #334155;
        font-size: 13px;
        line-height: 1.6;
    }
    .aw-knowledge-spotlight-action {
        display: inline-flex;
        margin-top: 12px;
        color: #1d4ed8;
        font-size: 13px;
        font-weight: 700;
    }
    .aw-knowledge-lane {
        display: block;
        padding: 14px;
        border: 1px solid #e5edf6;
        border-radius: 14px;
        background: #fbfdff;
        color: #0f172a;
    }
    .aw-knowledge-lane:hover {
        text-decoration: none;
        transform: translateY(-1px);
        transition: all .2s ease;
    }
    .aw-knowledge-lane strong {
        display: block;
        margin-bottom: 6px;
        font-size: 15px;
    }
    .aw-knowledge-lane span {
        display: block;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }
    @media (max-width: 991.98px) {
        .aw-knowledge-spotlights,
        .aw-knowledge-lanes {
            grid-template-columns: 1fr;
        }
    }
    .aw-right .hot-list dl dd {
        padding: 0;
    }
</style>
{/block}
{block name="main"}
<div class="aw-wrap mt-2" id="wrapMain">
    {if $setting.enable_category=='Y'}
    {:widget('common/category',['type'=>'article','category'=>$category,'show_type'=>'list'])}
    {/if}
    <div class="container">
        <div class="row justify-content-between">
            <div class="aw-left radius col-md-9 bg-white mb-2">
                <div class="aw-knowledge-hero">
                    <div class="aw-page-kicker">Knowledge Content Atlas</div>
                    <h1>{:frelink_article_type_label($article_type)}</h1>
                    <p>{:frelink_content_description($article_type)}</p>
                    <div class="aw-page-chips">
                        <span>{:L('精选')}</span>
                        <span>{:L('更新')}</span>
                        <span>{:L('高关注')}</span>
                    </div>
                </div>
                {if $article_type=='all' && !empty($article_type_spotlights)}
                <div class="aw-knowledge-spotlights">
                    {foreach $article_type_spotlights as $spotlight}
                    <a class="aw-knowledge-spotlight" href="{:url('article/index',['sort'=>'new','category_id'=>$category,'type'=>$spotlight['type']])}" data-pjax="wrapMain">
                        <em>{:L('主内容入口')}</em>
                        <strong>{$spotlight.label}</strong>
                        <p>{$spotlight.description}</p>
                        <div class="aw-knowledge-spotlight-meta">
                            <span>{:L('已发布 %s 篇',$spotlight['count'])}</span>
                            {if !empty($spotlight['latest'])}
                            <span>{:L('最近更新')} {:date_friendly($spotlight['latest']['update_time'])}</span>
                            {/if}
                        </div>
                        {if !empty($spotlight['latest'])}
                        <div class="aw-knowledge-spotlight-latest">{:L('最近一篇：%s',$spotlight['latest']['title'])}</div>
                        {/if}
                        <span class="aw-knowledge-spotlight-action">{if $spotlight['type']=='research'}{:L('去看综述')}{else/}{:L('去看观察')}{/if}</span>
                    </a>
                    {/foreach}
                </div>
                {/if}
                <div class="aw-knowledge-lanes">
                    <a class="aw-knowledge-lane" href="{:url('article/index',['sort'=>'new','category_id'=>$category,'type'=>$article_type])}" data-pjax="wrapMain">
                        <strong>{:L('最新更新')}</strong>
                        <span>{:L('先看最近新增的内容，快速确认这一类知识最近补了什么。')}</span>
                    </a>
                    <a class="aw-knowledge-lane" href="{:url('article/index',['sort'=>'hot','category_id'=>$category,'type'=>$article_type])}" data-pjax="wrapMain">
                        <strong>{:L('高关注内容')}</strong>
                        <span>{:L('优先查看被持续阅读和讨论的条目，判断哪些主题最值得追踪。')}</span>
                    </a>
                    <a class="aw-knowledge-lane" href="{:url('topic/index')}">
                        <strong>{:L('转到主题')}</strong>
                        <span>{:L('如果你想看一组内容之间的关系和变化，继续沿主题入口展开。')}</span>
                    </a>
                </div>
                <nav class="nav nav-tabs aw-pjax-a px-4" aria-label="{:L('知识内容排序')}">
                    <a class="nav-item nav-link {if $sort=='recommend'}active{/if}" data-pjax="wrapMain" href="{:url('article/index',['sort'=>'recommend','category_id'=>$category,'type'=>$article_type])}" {if $sort=='recommend'}aria-current="page"{/if}>{:L('精选')}</a>
                    <a class="nav-item nav-link {if $sort=='new'}active{/if}" data-pjax="wrapMain" href="{:url('article/index',['sort'=>'new','category_id'=>$category,'type'=>$article_type])}" {if $sort=='new'}aria-current="page"{/if}>{:L('更新')}</a>
                    <a class="nav-item nav-link {if $sort=='hot'}active{/if}" data-pjax="wrapMain" href="{:url('article/index',['sort'=>'hot','category_id'=>$category,'type'=>$article_type])}" {if $sort=='hot'}aria-current="page"{/if}>{:L('高关注')}</a>
                </nav>
                <nav class="aw-article-subnav aw-pjax-a" aria-label="{:L('知识内容形态')}">
                    {foreach $article_type_options as $typeKey => $label}
                    <a class="nav-link {if $article_type==$typeKey}active{/if}" data-pjax="wrapMain" href="{:url('article/index',['sort'=>$sort,'category_id'=>$category,'type'=>$typeKey])}" {if $article_type==$typeKey}aria-current="page"{/if}>{$label}</a>
                    {/foreach}
                </nav>

                <div id="tabMain" class="tab-content" >
                    <div class="tab-pane fade show active">
                        <div class="aw-common-list">
                            {we:article sort="$sort" category_id="$category" type="$article_type"}
                            <!--自定义内容列表页拓展钩子,可自定义内容页插入内如，如每多少条内容显示一条广告-->
                            {:hook('postsListsExtend',['info'=>$v,'key'=>$key,'type'=>'article'])}
                            <dl class="js-analytics-impression" data-analytics-type="article" data-analytics-id="{$v['id']}" data-analytics-list="article_index" data-analytics-position="{$key + 1}" data-analytics-source="desktop_article_index">
                                <dd>
                                    <div class="n-title">
                                        <span class="tip-s2 badge badge-secondary">{:frelink_article_type_label(isset($v['article_type']) ? $v['article_type'] : 'normal')}</span>
                                        {:hook('article_badge',$v)}
                                        {if $v.set_top}
                                        <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                                        {/if}
                                        <a href="{:url('article/detail',['id'=>$v['id']])}" target="_blank" class="js-analytics-click" data-analytics-type="article" data-analytics-id="{$v['id']}" data-analytics-list="article_index" data-analytics-position="{$key + 1}" data-analytics-source="desktop_article_index">{$v['title']|raw}</a>
                                        {:hook('extend_title_label',['area'=>'article_list','info'=>$v])}
                                    </div>
                                    <div class="pcon {if ($v['img_list'] || $v.cover) && get_theme_setting('common.list_show_image')=='Y'}row{/if}">
                                        {if ($v['img_list'] || $v.cover) && get_theme_setting('common.list_show_image')=='Y'}
                                        <div class="col-md-3 aw-list-img"><img src="{$v.cover|default=$v['img_list'][0]}" class="rounded aw-cut-img" alt="{$v['title']}" width="100%" loading="lazy" decoding="async"></div>
                                        <div class="ov-3 col-md-9">
                                            <div class="aw-three-line">
                                                {$v.message|raw}
                                            </div>
                                            {if isset($v['topics']) && !empty($v['topics'])}
                                            <div class="tags mt-1">
                                                {volist name="$v['topics']" id="topic"}
                                                <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                                                {/volist}
                                            </div>
                                            {/if}
                                        </div>
                                        {else/}
                                        <div class="aw-three-line">
                                            {$v.message|raw}
                                        </div>
                                        {if isset($v['topics']) && !empty($v['topics'])}
                                        <div class="tags mt-1">
                                            {volist name="$v['topics']" id="topic"}
                                            <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                                            {/volist}
                                        </div>
                                        {/if}
                                        {/if}
                                    </div>
                                </dd>
                                <dd>
                                    <label class="dz">
                                        <a type="button" href="javascript:;" class="btn btn-primary btn-sm" onclick="AWS.User.agree(this,'article','{$v['id']}');"><i class="iconfont">&#xe870;</i>{:L('赞同')}<span class="badge">{$v['agree_count']}</span></a>
                                    </label>
                                    <label class="ml-4 mr-2"><i class="iconfont">&#xe640;</i> {:L('%s 浏览',$v.view_count)}</label>
                                    <label class="mr-2"><i class="iconfont">&#xe601;</i> {:L('%s 评论',$v.comment_count)}</label>
                                    <label class="mr-2"><i class="iconfont">&#xe699;</i><a href="{$v['user_info']['url']}" style="color: #8790a4" class="aw-username name" data-id="{$v['user_info']['uid']}">{$v['user_info']['name']}</a></label>
                                    <label><i class="iconfont">&#xe68a;</i>{:date_friendly($v['create_time'])}</label>
                                </dd>
                            </dl>
                            {/we:article}
                        </div>
                        {$page|raw}
                    </div>
                </div>
            </div>
            <div class="aw-right radius col-md-3 px-xs-0">

                <!--侧边栏顶部钩子-->
                {:hook('sidebarTop')}

                {if $theme_config['article']['sidebar_show_items'] && in_array('write_nav',$theme_config['article']['sidebar_show_items'])}
                {:widget('sidebar/writeNav')}
                {/if}

                {if $theme_config['article']['sidebar_show_items'] && in_array('focus_topic',$theme_config['article']['sidebar_show_items'])}
                {:widget('sidebar/focusTopic',['uid'=>$user_id])}
                {/if}

                {if $theme_config['article']['sidebar_show_items'] && in_array('hot_topic',$theme_config['article']['sidebar_show_items'])}
                {:widget('sidebar/hotTopic',['uid'=>$user_id])}
                {/if}

                {if $theme_config['article']['sidebar_show_items'] && in_array('column',$theme_config['article']['sidebar_show_items'])}
                {:widget('sidebar/hotColumn',['uid'=>$user_id,'sort'=>'hot'])}
                {/if}

                {if $theme_config['article']['sidebar_show_items'] && in_array('hot_users',$theme_config['article']['sidebar_show_items'])}
                {:widget('sidebar/hotUsers',['uid'=>$user_id])}
                {/if}

                <!--侧边栏底部钩子-->
                {:hook('sidebarBottom')}
            </div>
        </div>
    </div>
</div>
{/block}
