{extend name="$theme_block" /}
{block name="style"}
<style>
    .aw-article-filterbar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 12px 24px;
        border-top: 1px solid #eef2f7;
        border-bottom: 1px solid #eef2f7;
        background: #fbfdff;
    }
    .aw-article-filterbar .nav-link {
        padding: 6px 14px;
        border-radius: 999px;
        border: 1px solid #dbe7f3;
        color: #60758b;
        font-size: 13px;
        line-height: 1.2;
        background: #fff;
    }
    .aw-article-filterbar .nav-link.active,
    .aw-article-filterbar .nav-link:hover {
        color: #fff;
        background: #1d4ed8;
        border-color: #1d4ed8;
        text-decoration: none;
    }
    .aw-article-filterbar .nav-link.aw-article-filter-type.active,
    .aw-article-filterbar .nav-link.aw-article-filter-type:hover {
        color: #1d4ed8;
        background: #eff6ff;
        border-color: #bfdbfe;
    }
    .aw-knowledge-hero {
        position: relative;
        overflow: hidden;
        margin: 0;
        padding: 30px 28px 26px;
        border-radius: 0;
        background:
            radial-gradient(circle at 18% 20%, rgba(56, 189, 248, 0.24), transparent 24%),
            radial-gradient(circle at 82% 18%, rgba(52, 211, 153, 0.22), transparent 26%),
            linear-gradient(135deg, #0b1830 0%, #0f3d68 48%, #0e7a6f 100%);
    }
    .aw-knowledge-hero::after {
        content: "";
        position: absolute;
        right: -90px;
        bottom: -120px;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.16) 0%, rgba(255, 255, 255, 0.02) 48%, transparent 72%);
        pointer-events: none;
    }
    .aw-knowledge-hero h1 {
        position: relative;
        z-index: 1;
        margin: 0 0 8px;
        font-size: 34px;
        font-weight: 800;
        color: #fff;
    }
    .aw-knowledge-hero p {
        position: relative;
        z-index: 1;
        margin: 0;
        max-width: 64ch;
        color: rgba(226, 232, 240, 0.9);
        font-size: 15px;
        line-height: 1.7;
    }
    .aw-page-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.14);
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
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.14);
        color: rgba(255, 255, 255, 0.92);
        font-size: 13px;
        font-weight: 700;
    }
    .aw-article-filter-divider {
        display: inline-flex;
        align-items: center;
        padding: 0 2px;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 700;
    }
    .aw-knowledge-spotlights {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        padding: 18px 16px 18px;
        border-bottom: 1px solid #dbe5ed;
        background: #fff;
    }
    .aw-knowledge-spotlight {
        position: relative;
        display: block;
        min-height: 132px;
        padding: 18px;
        border-radius: 18px;
        background: linear-gradient(180deg, #f8fbfd 0%, #eef6fb 100%);
        border: 1px solid #d9e4ec;
        color: #0f172a;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .aw-knowledge-spotlight::before {
        content: "";
        width: 44px;
        height: 4px;
        border-radius: 999px;
        margin-bottom: 10px;
        background: linear-gradient(90deg, #1d4ed8 0%, #0f766e 100%);
        display: block;
    }
    .aw-knowledge-spotlight:hover {
        text-decoration: none;
        transform: translateY(-2px);
        border-color: #bcd6e3;
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.08);
    }
    .aw-knowledge-spotlight strong {
        display: block;
        margin-bottom: 10px;
        padding: 0;
        border-radius: 0;
        background: none;
        color: #0f172a;
        font-size: 16px;
        font-weight: 800;
    }
    .aw-knowledge-spotlight p {
        margin: 0 0 10px;
        color: #5b6b7f;
        font-size: 13px;
        line-height: 1.7;
    }
    .aw-knowledge-spotlight-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 10px;
        color: #5b6b7f;
        font-size: 12px;
        line-height: 1.7;
    }
    .aw-knowledge-spotlight-latest {
        color: #5b6b7f;
        font-size: 13px;
        line-height: 1.7;
    }
    .aw-knowledge-spotlight-action {
        display: inline-flex;
        margin-top: 12px;
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
    }
    @media (max-width: 991.98px) {
        .aw-knowledge-spotlights {
            grid-template-columns: 1fr;
        }
    }
    .aw-right .hot-list dl dd {
        padding: 0;
    }
    .aw-side-entry-panel {
        padding: 14px;
        border: 1px solid #d9e4ec;
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbfd 100%);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
    }
    .aw-side-entry-grid {
        display: grid;
        gap: 12px;
    }
    .aw-side-entry-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 16px;
        border: 1px solid #d9e4ec;
        background: #fff;
        color: #0f172a;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .aw-side-entry-card:hover {
        text-decoration: none;
        color: #0f172a;
        transform: translateY(-1px);
        border-color: #c4d5e2;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    }
    .aw-side-entry-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 14px;
        font-size: 18px;
        flex: 0 0 42px;
    }
    .aw-side-entry-copy {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .aw-side-entry-copy strong {
        color: #0f172a;
        font-size: 14px;
        font-weight: 800;
        line-height: 1.3;
    }
    .aw-side-entry-copy em {
        margin-top: 3px;
        color: #64748b;
        font-style: normal;
        font-size: 12px;
        line-height: 1.5;
    }
    .aw-side-entry-card-research .aw-side-entry-icon {
        color: #4338ca;
        background: #e0e7ff;
    }
    .aw-side-entry-card-fragment .aw-side-entry-icon {
        color: #0f766e;
        background: #ccfbf1;
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
                    <h1>{if $article_type=='all'}{:L('知识内容')}{else/}{:frelink_article_type_label($article_type)}{/if}</h1>
                    <p>{:frelink_content_description($article_type)}</p>
                    <div class="aw-page-chips">
                        <span>{:L('综述优先沉淀')}</span>
                        <span>{:L('观察持续更新')}</span>
                        <span>{:L('沿主题继续延展')}</span>
                    </div>
                </div>
                {if $article_type=='all' && !empty($article_type_spotlights)}
                <div class="aw-knowledge-spotlights">
                    {foreach $article_type_spotlights as $spotlight}
                    <a class="aw-knowledge-spotlight" href="{:url('article/index',['sort'=>'new','category_id'=>$category,'type'=>$spotlight['type']])}" data-pjax="wrapMain">
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
                <nav class="aw-article-filterbar aw-pjax-a" aria-label="{:L('知识内容筛选')}">
                    <a class="nav-item nav-link {if $sort=='recommend'}active{/if}" data-pjax="wrapMain" href="{:url('article/index',['sort'=>'recommend','category_id'=>$category,'type'=>$article_type])}" {if $sort=='recommend'}aria-current="page"{/if}>{:L('精选')}</a>
                    <a class="nav-item nav-link {if $sort=='new'}active{/if}" data-pjax="wrapMain" href="{:url('article/index',['sort'=>'new','category_id'=>$category,'type'=>$article_type])}" {if $sort=='new'}aria-current="page"{/if}>{:L('更新')}</a>
                    <a class="nav-item nav-link {if $sort=='hot'}active{/if}" data-pjax="wrapMain" href="{:url('article/index',['sort'=>'hot','category_id'=>$category,'type'=>$article_type])}" {if $sort=='hot'}aria-current="page"{/if}>{:L('高关注')}</a>
                    <span class="aw-article-filter-divider">{:L('分类')}</span>
                    {foreach $article_type_options as $typeKey => $label}
                    <a class="nav-link aw-article-filter-type {if $article_type==$typeKey}active{/if}" data-pjax="wrapMain" href="{:url('article/index',['sort'=>$sort,'category_id'=>$category,'type'=>$typeKey])}" {if $article_type==$typeKey}aria-current="page"{/if}>{$label}</a>
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
                                        <div class="col-md-3 aw-list-img"><img src="{$v.cover|default=$v['img_list'][0]}" class="rounded aw-cut-img" alt="{$v['title']}" width="100%" onerror="this.onerror=null;this.src='/static/common/image/default-cover.svg'" loading="lazy" decoding="async"></div>
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

                <div class="r-box aw-side-entry-panel mb-2">
                    <div class="aw-side-entry-grid">
                        <a class="aw-side-entry-card aw-side-entry-card-research" href="{:frelink_publish_url('article',['article_type'=>'research'])}">
                            <span class="aw-side-entry-icon"><i class="far fa-file-alt"></i></span>
                            <span class="aw-side-entry-copy">
                                <strong>{:L('新建综述')}</strong>
                                <em>{:L('整理脉络与结论')}</em>
                            </span>
                        </a>
                        <a class="aw-side-entry-card aw-side-entry-card-fragment" href="{:frelink_publish_url('article',['article_type'=>'fragment'])}">
                            <span class="aw-side-entry-icon"><i class="fa fa-compass"></i></span>
                            <span class="aw-side-entry-copy">
                                <strong>{:L('新建观察')}</strong>
                                <em>{:L('记录变化与信号')}</em>
                            </span>
                        </a>
                    </div>
                </div>

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
