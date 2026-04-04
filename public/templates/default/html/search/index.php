{extend name="$theme_block" /}
{block name="style"}
<style>
    body {
        background:
            radial-gradient(circle at top left, rgba(15, 118, 110, 0.10), transparent 26%),
            radial-gradient(circle at top right, rgba(29, 78, 216, 0.08), transparent 24%),
            linear-gradient(180deg, #eef4f7 0%, #f7fafc 38%, #f7fafc 100%);
    }

    #wrapMain .aw-search-shell {
        overflow: hidden;
        border-radius: 28px;
        border: 1px solid #d8e4ec;
        background: #fff;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
    }

    .aw-search-hero {
        position: relative;
        overflow: hidden;
        padding: 30px 28px 26px;
        background: linear-gradient(135deg, #111827 0%, #1d4ed8 56%, #0f766e 100%);
        color: #fff;
    }

    .aw-search-hero::after {
        content: "";
        position: absolute;
        right: -110px;
        bottom: -130px;
        width: 360px;
        height: 360px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.16) 0%, rgba(255, 255, 255, 0.02) 48%, transparent 72%);
        pointer-events: none;
    }

    .aw-page-kicker {
        position: relative;
        z-index: 1;
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

    .aw-search-hero h1 {
        position: relative;
        z-index: 1;
        margin: 0 0 12px;
        font-size: 34px;
        line-height: 1.14;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .aw-search-hero p {
        position: relative;
        z-index: 1;
        max-width: 64ch;
        margin: 0;
        color: rgba(255, 255, 255, 0.84);
        font-size: 15px;
        line-height: 1.8;
    }

    .aw-page-chips {
        position: relative;
        z-index: 1;
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

    .aw-search-content {
        background: #fff;
    }

    .aw-search-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 18px 22px 0;
        margin: 0;
        border: 0 !important;
        background: #fff;
    }

    .aw-search-tabs .nav-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 18px;
        margin: 0;
        border-radius: 999px;
        border: 1px solid #d9e4ec;
        background: linear-gradient(180deg, #fff 0%, #f6fbff 100%);
        color: #475569;
        font-size: 13px;
        font-weight: 700;
        line-height: 1;
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.05);
    }

    .aw-search-tabs .nav-link:hover {
        color: #0f172a;
        background: #eef5f7;
        border-color: #cddae5;
        text-decoration: none;
    }

    .aw-search-tabs .nav-link.active {
        color: #fff;
        background: linear-gradient(135deg, #1d4ed8 0%, #0f766e 100%);
        border-color: transparent;
        box-shadow: 0 10px 18px rgba(29, 78, 216, 0.18);
    }

    .search-detail-list {
        padding: 0 20px 24px;
        background: #f7fafc;
    }

    .search-summary-card {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
        padding: 18px 20px;
        border: 1px solid #dce6ee;
        border-radius: 22px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbfd 100%);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
    }

    .search-summary-card strong,
    .search-summary-card em {
        font-style: normal;
        color: #0f172a;
    }

    .search-summary-kicker {
        display: block;
        margin-bottom: 8px;
        color: #0f766e;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .search-summary-main {
        margin: 0;
        color: #475569;
        font-size: 15px;
        line-height: 1.8;
    }

    .search-summary-side {
        color: #64748b;
        font-size: 13px;
        line-height: 1.7;
        text-align: right;
    }

    .search-detail-list .aw-common-list {
        padding-bottom: 6px;
    }

    .search-detail-list .aw-common-list dl {
        position: relative;
        margin-bottom: 18px;
        padding: 18px 20px 16px;
        border: 1px solid #d9e4ec;
        border-radius: 24px;
        background: linear-gradient(180deg, #fff 0%, #fbfdff 100%);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .search-detail-list .aw-common-list dl:hover {
        transform: translateY(-1px);
        border-color: #cbd9e6;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }

    .search-detail-list .aw-common-list dl > dd:last-child {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        padding-top: 14px;
        margin-top: 16px;
        border-top: 1px solid #e5edf4;
    }

    .search-detail-list .n-title a,
    .search-detail-list .topic .font-weight-bold,
    .search-detail-list .users h3 a {
        color: #0f172a;
        font-weight: 800;
    }

    .search-detail-list .n-title a:hover,
    .search-detail-list .topic .font-weight-bold:hover,
    .search-detail-list .users h3 a:hover {
        color: #1d4ed8;
        text-decoration: none;
    }

    .search-detail-list .aw-two-line,
    .search-detail-list .aw-three-line,
    .search-detail-list .aw-one-line,
    .search-detail-list .text-muted,
    .search-detail-list p {
        color: #5b6b7f;
    }

    .search-detail-list .aw-topic .tag,
    .aw-search-empty-tag .tag,
    .aw-search-aside .topic-btn .tag {
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .aw-search-empty {
        padding: 28px 24px;
        border: 1px solid #dce6ee;
        border-radius: 24px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbfd 100%);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
    }

    .aw-search-empty h3 {
        margin-bottom: 10px;
        color: #0f172a;
        font-size: 24px;
        font-weight: 800;
    }

    .aw-search-empty p {
        margin-bottom: 12px;
        color: #5b6b7f;
        line-height: 1.8;
    }

    .aw-search-empty ul {
        margin-bottom: 0;
        padding-left: 18px;
        color: #64748b;
    }

    .aw-search-empty ul li {
        margin-bottom: 8px;
    }

    .aw-search-empty-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 16px;
    }

    .aw-search-aside .r-box {
        border: 1px solid #d9e4ec;
        border-radius: 22px;
        background: linear-gradient(180deg, #fff 0%, #fbfdff 100%);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .aw-search-aside .r-title {
        padding: 18px 18px 0;
    }

    .aw-search-aside .r-title h4 {
        margin-bottom: 0;
        color: #0f172a;
        font-size: 18px;
        font-weight: 800;
    }

    .aw-search-aside .hot-list {
        padding: 14px 18px 18px;
    }

    .aw-search-aside .topic-btn {
        margin: 0 10px 10px 0;
    }

    .search-detail-list .pagination {
        justify-content: center;
        margin-top: 8px;
        margin-bottom: 0;
    }

    @media (max-width: 767.98px) {
        #wrapMain .aw-search-shell {
            border-radius: 22px;
        }

        .aw-search-hero {
            padding: 22px 18px 20px;
        }

        .aw-search-hero h1 {
            font-size: 28px;
        }

        .aw-search-tabs {
            padding: 14px 14px 0;
            gap: 8px;
        }

        .aw-search-tabs .nav-link {
            min-height: 38px;
            padding: 0 14px;
        }

        .search-detail-list {
            padding: 0 14px 18px;
        }

        .search-summary-card,
        .search-detail-list .aw-common-list dl,
        .aw-search-empty {
            padding: 16px;
            border-radius: 20px;
        }

        .search-summary-side {
            text-align: left;
        }
    }
</style>
{/block}
{block name="main"}
<div class="aw-wrap mt-2" id="wrapMain">
    <div class="container" >
        <div class="row">
            <div class="aw-left col-md-9 col-sm-12 px-0 mb-2 aw-search-shell">
                <section class="aw-search-hero">
                    <div class="aw-page-kicker">Knowledge Search</div>
                    <h1>
                        {if $keywords}
                        {:L('搜索 “%s” 的知识结果', urldecode($keywords))}
                        {else/}
                        {:L('搜索 FAQ、综述与知识条目')}
                        {/if}
                    </h1>
                    <p>
                        {if $keywords}
                        {:L('统一检索 FAQ、知识内容、主题和用户，把分散信息收拢成可继续阅读和继续补充的知识入口。')}
                        {else/}
                        {:L('先输入问题、主题、概念或人名，再按类型筛选结果。搜索页会优先展示可复用的 FAQ、综述与主题归档。')}
                        {/if}
                    </p>
                    <div class="aw-page-chips">
                        <span>{:L('FAQ / 综述 / 主题 / 用户统一检索')}</span>
                        {if $keywords}
                        <span>{:L('当前共找到 %s 条结果', $total)}</span>
                        <span>{:L('支持继续按类型筛选')}</span>
                        {else/}
                        <span>{:L('支持按内容类型细分结果')}</span>
                        <span>{:L('优先沉淀高频搜索主题')}</span>
                        {/if}
                    </div>
                </section>
                <div id="SearchResultMain" class="aw-search-content">
                    <div class="position-relative">
                    <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block searchTypeTab aw-search-tabs">
                        <li class="nav-item"><a class="nav-link {if $type=='all' || !$type}active{/if}" data-pjax="SearchResultMain" href="{:url('search/index',['q'=>$keywords,'sort'=>$sort,'type'=>'all'])}">{:L('全部')}</a></li>
                        {volist name="tab_list" id="v"}
                        <li class="nav-item"><a class="nav-link {if $type==$v.name}active{/if}" data-pjax="SearchResultMain" href="{:url('search/index',['q'=>$keywords,'sort'=>$sort,'type'=>$v.name])}">{:L($v.title)}</a></li>
                        {/volist}
                    </ul>
                        </div>
                    <div class="search-detail-list" id="tabMain">
                        <div class="search-summary-card">
                            <div>
                                <span class="search-summary-kicker">{:L('结果概览')}</span>
                                <p class="search-summary-main">
                                    {if $keywords}
                                    <strong>“{:urldecode($keywords)}”</strong> {:L('的搜索结果已按知识类型整理，可继续切换标签缩小范围。')}
                                    {else/}
                                    {:L('请输入关键词开始搜索，系统会优先返回更稳定、更适合沉淀的知识内容。')}
                                    {/if}
                                </p>
                            </div>
                            <div class="search-summary-side">
                                {if $keywords}
                                <div>{:L('约 %s 条结果', $total)}</div>
                                <div>{:L('当前筛选：%s', $type && $type!='all' ? $type : L('全部'))}</div>
                                {else/}
                                <div>{:L('支持模糊检索与类型筛选')}</div>
                                <div>{:L('可搜索 FAQ、知识内容、主题、用户')}</div>
                                {/if}
                            </div>
                        </div>
                        {if !$keywords}
                        <div class="aw-search-empty">
                            <h3>{:L('先输入一个你想查的主题')}</h3>
                            <p>{:L('可以直接搜索问题、主题、概念、作者名或知识章节标题。输入后，结果会按 FAQ、知识内容、主题和用户统一整理。')}</p>
                            <p>{:L('如果暂时没有明确关键词，也可以从热门搜索开始。')}</p>
                            {if $search_list}
                            <div class="aw-search-empty-tags">
                                {foreach $search_list as $key=>$v}
                                <a href="{:url('search/index',['q'=>$v['keyword']])}" class="aw-search-empty-tag"><em class="tag">{$v.keyword}</em></a>
                                {/foreach}
                            </div>
                            {/if}
                        </div>
                        {elseif !empty($list)}
                        <div class="aw-common-list">
                            {volist name="$list" id="v"}
                            {if $v['search_type']=="question" }
                            <dl>
                                <dt>
                                    {if $v.is_anonymous}
                                    <a href="javascript:;" class="aw-username">
                                        <img src="/static/common/image/default-avatar.svg"  onerror="this.src='/static/common/image/default-avatar.svg'"  class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
                                    </a>
                                    {else/}
                                    <a href="{$v['user_info']['url']}" class="aw-user-name" data-id="{$v['user_info']['uid']}">
                                        <img src="{$v['user_info']['avatar']}"  onerror="this.src='/static/common/image/default-avatar.svg'"  class="circle" alt="{$v['user_info']['name']}">{$v['user_info']['name']}
                                    </a>
                                    {/if}
                                    <i>{:L('补充了 FAQ')}</i>
                                    <em class="time">{:date_friendly($v['create_time'])}</em>
                                    {if $v['topics']}
                                    <div class="tag d-inline-block">
                                        {volist name="$v['topics']" id="topic"}
                                        <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                                        {/volist}
                                    </div>
                                    {/if}
                                </dt>
                                <dd>
                                    <div class="n-title">
                                        <span class="tip-s1 badge badge-secondary">{:L('FAQ')}</span>
                                        {if $v.set_top}
                                        <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                                        {/if}
                                        <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title|raw}</a>
                                    </div>
                                    <div class="pcon {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}row{/if}">
                                        {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}
                                        <div class="col-md-12 t-imglist row">
                                            {volist name="$v['img_list']" id="img" key="k"}
                                            {if($k<4)}
                                            <div class="col-md-4 aw-list-img">
                                                <img src="{$img|default='/static/common/image/default-cover.svg'}" class="rounded w-100 aw-cut-img" style="margin-bottom: 5px;" >
                                            </div>
                                            {/if}
                                            {/volist}
                                        </div>
                                        <div class="ov-3 col-md-12">
                                            <div class="aw-two-line">
                                                {$v.detail|raw}
                                            </div>
                                        </div>
                                        {else/}
                                        <div class="aw-two-line">
                                            {$v.detail|raw}
                                        </div>
                                        {/if}
                                    </div>
                                </dd>
                                <dd>
                                    <label>
                                        <a type="button" class="{$v['has_focus'] ? 'ygz' : 'gz'} btn btn-primary btn-sm" onclick="AWS.User.focus(this,'question','{$v.id}')">{$v['has_focus'] ? L('已关注') : L('关注 FAQ')} <span class="badge focus-count">{$v.focus_count}</span></a>
                                    </label>
                                    <label class="ml-3 mr-3"><i class="iconfont">&#xe870;</i> {$v.agree_count}</label>
                                    <label class="mr-3"><i class="iconfont">&#xe601;</i> {$v['comment_count']}评论</label>
                                </dd>
                            </dl>
                            {/if}

                            {if $v['search_type']=="article" }
                            <dl>
                                <dd>
                                    <div class="n-title">
                                        <span class="tip-s2 badge badge-secondary">{:frelink_article_type_label($v['article_type'] ?? 'normal')}</span>
                                        {:hook('article_badge')}
                                        {if $v.set_top}
                                        <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                                        {/if}
                                        <a href="{:url('article/detail',['id'=>$v['id']])}">{$v['title']|raw}</a>
                                    </div>
                                    <div class="pcon {if ($v['img_list'] || $v.cover) && get_theme_setting('common.list_show_image')=='Y'}row{/if}">
                                        {if ($v['img_list'] || $v.cover) && get_theme_setting('common.list_show_image')=='Y'}
                                        <div class="col-md-3 aw-list-img"><img src="{$v.cover|default=$v['img_list'][0]}" class="rounded aw-cut-img" alt="{$v['title']}" width="100%"></div>
                                        <div class="ov-3 col-md-9">
                                            <div class="aw-three-line">
                                                {$v.message|raw}
                                            </div>
                                            {if $v['topics']}
                                            <div class="tags">
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
                                        {if $v['topics']}
                                        <div class="tags">
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
                                        <a type="button" class="btn btn-primary btn-sm" onclick="AWS.User.agree(this,'article','{$v['id']}');"><i class="iconfont">&#xe870;</i>{:L('赞同')}<span class="badge">{$v['agree_count']}</span></a>
                                    </label>
                                    <label class="ml-4 mr-2"><i class="iconfont">&#xe640;</i> {$v['view_count']?:''}{:L('浏览')}</label>
                                    <label class="mr-2"><i class="iconfont">&#xe601;</i> {$v['comment_count']}{:L('评论')}</label>
                                    <label class="mr-2"><i class="iconfont">&#xe699;</i><a href="{$v['user_info']['url']}" style="color: #8790a4" class="aw-username name" data-id="{$v['user_info']['uid']}">{$v['user_info']['name']}</a></label>
                                    <label><i class="iconfont">&#xe68a;</i>{:date_friendly($v['create_time'])}</label>
                                </dd>
                            </dl>
                            {/if}

                            {if $v['search_type']=="users" }
                            <dl class="users d-flex position-relative">
                                <dt class="flex-fill mr-3" style="width: 54px;height: 54px">
                                    <a href="{$v.url}" class="rounded d-block"><img src="{$v.avatar}"  onerror="this.src='/static/common/image/default-avatar.svg'"  alt="{$v.name}" style="width: 54px;height: 54px"></a>
                                    <span class="{$v['is_online'] ? 'online-dot' : 'offline-dot'}"></span>
                                </dt>
                                <dd class="flex-fill w-100 mb-0">
                                    <h3 class="font-11 mb-2"><a href="{$v.url}">{$v.name|raw}</a></h3>
                                    <p class="aw-two-line">{$v['signature']|default=L('这家伙还没有留下自我介绍～')|raw}</p>
                                </dd>
                                {if $user_id && $user_id!=$v.uid}
                                <dd class="position-absolute" style="right: 0;top:10px">
                                    <a href="javascript:;" class="mr-3 text-primary {$v['has_focus'] ? 'active ygz' : ''}" onclick="AWS.User.focus(this,'user','{$v.uid}')">
                                        {$v['has_focus'] ? '<span><i class="icon-minus-circle text-danger"></i>'. L('已关注').'</span>' : '<span><i class="icon-plus-circle text-primary"></i> '.L('关注').'</span>'}
                                    </a>
                                    <a href="javascript:;" class="aw-send-inbox text-smooth">{:L('私信')}</a>
                                </dd>
                                {/if}
                            </dl>
                            {/if}

                            {if $v['search_type']=="topic" }
                            <dl class="topic position-relative clearfix d-flex">
                                <dt class="flex-fill" style="max-width: 60px;margin-right: 15px">
                                    <a href="{:url('topic/detail',['id'=>$v['id']])}">
                                        <img src="{$v['pic']|default='/static/common/image/topic.svg'}" onerror="this.src='/static/common/image/topic.svg'" class="rounded" style="width: 60px;height: 60px">
                                    </a>
                                </dt>
                                <dd class="flex-fill" style="width: calc(100% - 75px);">
                                    <a href="{:url('topic/detail',['id'=>$v['id']])}" class="font-weight-bold"># {$v.title|raw}</a>
                                    <div class="mb-0 font-8 aw-one-line">{$v.description|raw}</div>
                                    <p class="font-8 text-muted">
                                        <span class="mr-3">{:L('正在讨论')}：{$v.discuss}</span>
                                        <span class="mr-3"><span class="aw-global-focus-count">{:L('关注人数')}：{$v.focus}</span></span>
                                        {if $user_id}
                                        <a href="javascript:;" class="cursor-pointer {$v['has_focus'] ? 'active ygz' : ''}" onclick="AWS.User.focus(this,'topic','{$v.id}')" >{$v['has_focus'] ? '<span><i class="icon-minus-circle text-danger"></i> '.L('已关注').'</span>' : '<span><i class="icon-plus-circle text-primary"></i> '.L('关注').'</span>'}</a>
                                        {/if}
                                    </p>
                                </dd>
                            </dl>
                            {/if}

                            {if $v['search_type']=="answer" }
                            <dl>
                                <dt>
                                    {if (!$v['answer_info'])}
                                    {if $v.is_anonymous}
                                    <a href="javascript:;" class="aw-username">
                                        <img src="/static/common/image/default-avatar.svg" onerror="this.src='/static/common/image/default-avatar.svg'" class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
                                    </a>
                                    {else/}
                                    <a href="{$v['user_info']['url']}" class="aw-user-name" data-id="{$v['user_info']['uid']}">
                                        <img src="{$v['user_info']['avatar']}" onerror="this.src='/static/common/image/default-avatar.svg'" class="circle" alt="{$v['user_info']['name']}">{$v['user_info']['name']}
                                    </a>
                                    {/if}
                                    <i>{:L('补充了 FAQ')}</i>
                                    <em class="time">{:date_friendly($v['create_time'])}</em>
                                    {else/}
                                    {if $v['answer_info']['is_anonymous']}
                                    <a href="javascript:;" class="aw-username" >
                                        <img src="/static/common/image/default-avatar.svg" onerror="this.src='/static/common/image/default-avatar.svg'" class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
                                    </a>
                                    {else/}
                                    <a href="{$v['answer_info']['user_info']['url']}" data-id="{$v['answer_info']['uid']}" class="aw-user-name" data-pjax="WrapBody">
                                        <img src="{$v['answer_info']['user_info']['avatar']}" onerror="this.src='/static/common/image/default-avatar.svg'" class="circle" alt="{$v['answer_info']['user_info']['name']}">{$v['answer_info']['user_info']['name']}
                                    </a>
                                    {/if}
                                    <i>{:L('补充了 FAQ')}（{$v['answer_count']}{:L('条补充')}）</i>
                                    <em class="time">{:date_friendly($v['answer_info']['create_time'])}</em>
                                    {/if}
                                    {if $v['topics']}
                                    <div class="tag d-inline-block">
                                        {volist name="$v['topics']" id="topic"}
                                        <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                                        {/volist}
                                    </div>
                                    {/if}
                                </dt>
                                <dd>
                                    <div class="n-title">
                                        <span class="tip-s1 badge badge-secondary">{:L('FAQ')}</span>
                                        {if $v.set_top}
                                        <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                                        {/if}
                                        {if (!$v['answer_info'])}
                                        <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title|raw}</a>
                                        {else/}
                                        <a href="{:url('question/detail',['id'=>$v['id'],'answer'=>$v['answer_info']['id']])}">{$v.title|raw}</a>
                                        {/if}
                                    </div>
                                    <div class="pcon {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}row{/if}">
                                        {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}
                                        <div class="col-md-12 t-imglist row">
                                            {volist name="$v['img_list']" id="img" key="k"}
                                            {if($k<4)}
                                            <div class="col-md-4 aw-list-img">
                                                <img src="{$img|default='/static/common/image/default-cover.svg'}" class="rounded w-100 aw-cut-img" style="margin-bottom: 5px;" >
                                            </div>
                                            {/if}
                                            {/volist}
                                        </div>
                                        <div class="ov-3 col-md-12">
                                            <div class="aw-two-line">
                                                {$v.detail|raw}
                                            </div>
                                        </div>
                                        {else/}
                                        <div class="aw-two-line">
                                            {$v.detail|raw}
                                        </div>
                                        {/if}
                                    </div>
                                </dd>
                                <dd>
                                    {if (!$v['answer_info'])}
                                    <label>
                                        <a type="button" class="{$v['has_focus'] ? 'ygz' : 'gz'} btn btn-primary btn-sm" onclick="AWS.User.focus(this,'question','{$v.id}')">{$v['has_focus'] ? L('已关注') : L('关注 FAQ')} <span class="badge focus-count">{$v.focus_count}</span></a>
                                    </label>
                                    <label class="ml-3 mr-3"><i class="iconfont">&#xe870;</i> {$v.agree_count}</label>
                                    <label class="mr-3"><i class="iconfont">&#xe601;</i> {$v['comment_count']}{:L('评论')}</label>
                                    {else/}
                                    <label class="dz">
                                        <a type="button" class="btn btn-primary btn-sm aw-ajax-agree  {$v['answer_info']['vote_value']==1 ? 'active' : ''}" title="{:L('点赞回答')}" onclick="AWS.User.agree(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe870;</i>{:L('赞同')}<span class="badge">{$v['answer_info']['agree_count']}</span></a>
                                        <a type="button" class="btn btn-primary btn-sm aw-ajax-against  {$v['answer_info']['vote_value']==-1 ? 'active' : ''}" title="{:L('反对回答')}" onclick="AWS.User.against(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe68f;</i>{:L('反对')}<span class="badge"></span></a>
                                    </label>
                                    <label class="ml-3 mr-3"><i class="iconfont">&#xe625;</i>{:L('%s 人感谢', $v['answer_info']['thanks_count'])}</label>
                                    <label class="mr-3"><i class="iconfont">&#xe601;</i>{$v['answer_info']['comment_count']}{:L('评论')}</label>
                                    {/if}
                                </dd>
                            </dl>
                            {/if}

                            {:hook('search_template_'.$v['search_type'],$v)}

                            {/volist}
                        </div>
                        {$page|raw}
                        {else/}
                        <div class="aw-search-empty">
                            <h3>{:L('没有找到直接匹配的内容')}</h3>
                            <p>{:L('当前没有找到与 “%s” 直接相符的结果。可以换一个更常见、更短或更接近主题本身的关键词再试一次。', urldecode($keywords))}</p>
                            <ul>
                                <li>{:L('检查是否有错别字或多余符号。')}</li>
                                <li>{:L('尝试用更短的主题词、别名或上位概念。')}</li>
                                <li>{:L('如果是具体问题，也可以先搜索主题，再从主题页继续查找。')}</li>
                            </ul>
                            {if $search_list}
                            <div class="aw-search-empty-tags">
                                {foreach $search_list as $key=>$v}
                                <a href="{:url('search/index',['q'=>$v['keyword']])}" class="aw-search-empty-tag"><em class="tag">{$v.keyword}</em></a>
                                {/foreach}
                            </div>
                            {/if}
                        </div>
                        {/if}
                    </div>
                </div>
            </div>
            <div class="aw-right col-md-3 col-sm-12 px-xs-0 aw-search-aside">
                <!--侧边栏顶部钩子-->
                {:hook('sidebarTop')}
                {if $search_list}
                <div class="r-box mb-2 hot-topic">
                    <div class="r-title">
                        <h4>{:L('热门搜索')}</h4>
                    </div>
                    <div class="hot-list hot-yh-list pb-2">
                        <div class="sidebarFocusTopic aw-tag">
                            {foreach $search_list as $key=>$v}
                            <a href="{:url('search/index',['q'=>$v['keyword']])}" class="topic-btn d-inline-block mb-2"><em class="tag">{$v.keyword}</em></a>
                            {/foreach}
                        </div>
                    </div>
                </div>
                {/if}

                <!--侧边栏底部钩子-->
                {:hook('sidebarBottom')}
            </div>
        </div>
    </div>
</div>
<script>
    $(function (){
        $(document).on('click', '.searchSortTab li a', function()
        {
            $('.searchSortTab li').removeClass('active');
            $(this).parent('li').addClass('active');
            $('.searchSortTabText span').text($(this).text());
        });
    });
</script>
{/block}
