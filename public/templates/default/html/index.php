{extend name="$theme_block" /}
{block name="header"}
{if get_theme_setting('home.search_enable')=='Y'}
<style>
    .w-top-img-default {
        background: url('{$static_url}images/top-img.png') center center;
        background-size: auto 100%;
    }
    @supports (background-image: image-set(url("x.webp") 1x)) {
        .w-top-img-default {
            background-image: image-set(
                url('{$static_url}images/top-img.webp') 1x,
                url('{$static_url}images/top-img.png') 1x
            );
        }
    }
    .aw-home-content-map {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin: 18px 0 22px;
    }
    .aw-home-map-card {
        display: block;
        padding: 18px;
        border-radius: 16px;
        border: 1px solid #e5edf6;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
        color: #0f172a;
    }
    .aw-home-map-card:hover {
        text-decoration: none;
        transform: translateY(-2px);
        transition: all 0.2s ease;
    }
    .aw-home-map-card strong {
        display: block;
        margin-bottom: 6px;
        font-size: 16px;
    }
    .aw-home-map-card span {
        display: block;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }
    .aw-home-curated {
        margin-bottom: 20px;
    }
    .aw-home-curated-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }
    .aw-home-curated-card {
        padding: 18px;
        border-radius: 16px;
        border: 1px solid #e5edf6;
        background: #fff;
    }
    .aw-home-curated-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .aw-home-curated-head h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
    }
    .aw-home-curated-head a {
        color: #2563eb;
        font-size: 13px;
    }
    .aw-home-curated-list a {
        display: block;
        padding: 10px 0;
        border-top: 1px solid #eef2f7;
        color: #0f172a;
    }
    .aw-home-curated-list a:first-child {
        border-top: 0;
        padding-top: 0;
    }
    .aw-home-curated-list small {
        display: block;
        margin-bottom: 4px;
        color: #2563eb;
    }
    .aw-home-curated-list p {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }
    .aw-home-feed-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 12px 24px 0;
    }
    .aw-home-feed-filter a {
        padding: 6px 14px;
        border-radius: 999px;
        border: 1px solid #dbe7f3;
        background: #fff;
        color: #60758b;
        font-size: 13px;
    }
    .aw-home-feed-filter a.active {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #fff;
    }
    .aw-home-feed-note {
        padding: 10px 24px 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.7;
    }
    .aw-home-section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .aw-home-section-title h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }
    .aw-home-section-title a {
        color: #2563eb;
        font-size: 13px;
    }
    .aw-home-topic-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }
    .aw-home-topic-card {
        display: block;
        padding: 14px;
        border-radius: 14px;
        border: 1px solid #e6edf5;
        background: #fff;
        color: #0f172a;
    }
    .aw-home-topic-card strong {
        display: block;
        margin-bottom: 6px;
    }
    .aw-home-topic-card span {
        color: #64748b;
        font-size: 13px;
    }
    .aw-home-feed-shell {
        margin-top: 18px;
        border-top: 1px solid #eef2f7;
        padding-top: 16px;
    }
    .aw-home-feed-head {
        padding: 0 24px 10px;
    }
    .aw-home-feed-head h4 {
        margin: 0 0 4px;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }
    .aw-home-feed-head p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.7;
    }
    @media (max-width: 991.98px) {
        .aw-home-content-map,
        .aw-home-curated-grid,
        .aw-home-topic-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<div class="w-top-img {if empty($theme_config['home']['search_bg'])}w-top-img-default{/if}" {if !empty($theme_config['home']['search_bg'])}style="background: url('{$theme_config['home']['search_bg']}') center center; background-size:auto 100%;"{/if}>
    {include file="global/nav"}
    <div class="container index-search">
        <div class="row">
            <h2 class="col-12">{$theme_config['home']['search_title_text']|default=L('公开、开放、可检索的知识系统')}</h2>
            <p class="mb-3 w-100" style="color: #eee">{$theme_config['home']['search_min_text']|default=L('用综述沉淀脉络，用主题追踪变化，用观察保留判断，用 FAQ 承接检索，不再依赖问答社区逻辑。')}</p>
            <div class="col-12">
                <form action="{:url('search/index')}" method="get" id="homeSearch">
                <span>
                    <i class="iconfont">&#xe610;</i>
                    <input type="text" autocomplete="off"  value="{:input('get.q')}"  name="q" placeholder="{:L('搜索综述、观察、FAQ、主题或帮助')}">
                    <button type="button" class="btn gradientBtn px-4 ml-1" style="height: 42px;" onclick="$('#homeSearch').submit();" >{:L('搜索')}</button>
                </span>
                </form>
                <div class="mt-3 d-flex flex-wrap justify-content-center">
                    <a class="btn btn-sm btn-light m-1" href="{:url('article/index',['type'=>'research'])}">{:L('综述')}</a>
                    <a class="btn btn-sm btn-light m-1" href="{:url('topic/index')}">{:L('主题')}</a>
                    <a class="btn btn-sm btn-light m-1" href="{:url('article/index',['type'=>'fragment'])}">{:L('观察')}</a>
                    <a class="btn btn-sm btn-light m-1" href="{:url('question/index')}">{:L('FAQ')}</a>
                    <a class="btn btn-sm btn-light m-1" href="{:url('article/index',['type'=>'faq'])}">{:L('帮助')}</a>
                </div>
            </div>
        </div>
    </div>
</div>
{else/}
{__block__}
{/if}
{/block}
{block name="main"}
<div class="container mt-2">
    <div class="row justify-content-between">
        <div class="aw-left radius col-md-9 bg-white mb-2">
            {if !$type && in_array($sort,['new','recommend','hot'])}
            <div class="px-4 pt-4">
                <div class="aw-home-content-map">
                    <a class="aw-home-map-card" href="{:url('article/index',['type'=>'research'])}">
                        <strong>{:L('综述')}</strong>
                        <span>{:L('适合沉淀资料脉络、核心分歧、阶段判断和后续观察点。')}</span>
                    </a>
                    <a class="aw-home-map-card" href="{:url('topic/index')}">
                        <strong>{:L('主题')}</strong>
                        <span>{:L('按主题聚合资料、变化和后续观察点，形成持续追踪入口。')}</span>
                    </a>
                    <a class="aw-home-map-card" href="{:url('article/index',['type'=>'fragment'])}">
                        <strong>{:L('观察')}</strong>
                        <span>{:L('保留思考现场、快速判断和尚未定稿但值得记录的洞见。')}</span>
                    </a>
                </div>
                <div class="aw-home-curated">
                    <div class="aw-home-curated-grid">
                        <section class="aw-home-curated-card">
                            <div class="aw-home-curated-head">
                                <h4>{:L('最新综述')}</h4>
                                <a href="{:url('article/index',['type'=>'research'])}">{:L('查看全部')}</a>
                            </div>
                            <div class="aw-home-curated-list">
                                {we:article limit="3" sort="new" type="research" empty="<p class='text-muted mb-0'>".L('暂无研究综述')."</p>"}
                                <a href="{:url('article/detail',['id'=>$v['id']])}" target="_blank">
                                    <small>{:frelink_article_type_label(isset($v['article_type']) ? $v['article_type'] : 'research')}</small>
                                    <strong>{$v['title']|raw}</strong>
                                    <p>{$v.message|raw}</p>
                                </a>
                                {/we:article}
                            </div>
                        </section>
                        <section class="aw-home-curated-card">
                            <div class="aw-home-curated-head">
                                <h4>{:L('最新观察')}</h4>
                                <a href="{:url('article/index',['type'=>'fragment'])}">{:L('查看全部')}</a>
                            </div>
                            <div class="aw-home-curated-list">
                                {we:article limit="3" sort="new" type="fragment" empty="<p class='text-muted mb-0'>".L('暂无思想碎片')."</p>"}
                                <a href="{:url('article/detail',['id'=>$v['id']])}" target="_blank">
                                    <small>{:frelink_article_type_label(isset($v['article_type']) ? $v['article_type'] : 'fragment')}</small>
                                    <strong>{$v['title']|raw}</strong>
                                    <p>{$v.message|raw}</p>
                                </a>
                                {/we:article}
                            </div>
                        </section>
                        <section class="aw-home-curated-card">
                            <div class="aw-home-curated-head">
                                <h4>{:L('常见 FAQ')}</h4>
                                <a href="{:url('question/index')}">{:L('查看全部')}</a>
                            </div>
                            <div class="aw-home-curated-list">
                                {we:question limit="3" sort="new" empty="<p class='text-muted mb-0'>".L('暂无 FAQ 条目')."</p>"}
                                <a href="{:url('question/detail',['id'=>$v['id']])}" target="_blank">
                                    <small>{:L('FAQ')}</small>
                                    <strong>{$v['title']|raw}</strong>
                                    <p>{$v['detail']|raw}</p>
                                </a>
                                {/we:question}
                            </div>
                        </section>
                        <section class="aw-home-curated-card">
                            <div class="aw-home-section-title">
                                <h4>{:L('核心主题')}</h4>
                                <a href="{:url('topic/index')}">{:L('查看全部')}</a>
                            </div>
                            <div class="aw-home-topic-grid">
                                {we:topic where="status=1" sort="discuss desc,focus desc,id desc" limit="4" empty="<p class='text-muted mb-0'>".L('暂无主题')."</p>"}
                                <a class="aw-home-topic-card" href="{:url('topic/detail',['id'=>$v['id']])}" target="_blank">
                                    <strong>{$v.title}</strong>
                                    <span>{:L('讨论')} {$v.discuss|default=0} · {:L('关注')} {$v.focus|default=0}</span>
                                </a>
                                {/we:topic}
                            </div>
                        </section>
                        <section class="aw-home-curated-card">
                            <div class="aw-home-section-title">
                                <h4>{:L('知识归档')}</h4>
                                <a href="{:url('help/index')}">{:L('查看全部')}</a>
                            </div>
                            <div class="aw-home-curated-list">
                                {if !empty($archive_chapters)}
                                {volist name="archive_chapters" id="chapter"}
                                <a href="{:url('help/detail',['token'=>$chapter['url_token']])}" target="_blank">
                                    <small>{:L('归档章节')} · {$chapter.relation_count|default=0} {:L('条内容')}</small>
                                    <strong>{$chapter.title}</strong>
                                    <p>{if !empty($chapter['chapters'][0]['info']['title'])}{$chapter['chapters'][0]['info']['title']}{else/}{:str_cut(strip_tags((string)$chapter['description']),0,70)}{/if}</p>
                                </a>
                                {/volist}
                                {else/}
                                <p class='text-muted mb-0'>{:L('暂无知识归档')}</p>
                                {/if}
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            {/if}
            <div class="aw-home-feed-shell">
            <div class="aw-home-feed-head">
                <h4>{:L('持续更新')}</h4>
                <p>{:L('以下内容流用于持续补充综述、观察、FAQ 和帮助条目，作为首页四个主入口之外的更新面。')}</p>
            </div>
            <div class="nav nav-tabs px-4 aw-pjax-a" role="tablist">
                {if $user_id}
                <a class="nav-item nav-link {if $current_sort=='focus'}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>'focus'])}">{:L('关注主题')}</a>
                {/if}
                <a class="nav-item nav-link {if $current_sort=='recommend'}active{/if}" data-pjax="tabMain" href="{:url('index/index',array_merge(['sort'=>'recommend'],$feed_query))}">{:L('精选')}</a>
                <a class="nav-item nav-link {if $current_sort=='new'}active{/if}" data-pjax="tabMain" href="{:url('index/index',array_merge(['sort'=>'new'],$feed_query))}">{:L('更新')}</a>
                <a class="nav-item nav-link {if $current_sort=='hot'}active{/if}" data-pjax="tabMain" href="{:url('index/index',array_merge(['sort'=>'hot'],$feed_query))}">{:L('高关注')}</a>
                <a class="nav-item nav-link {if $current_sort=='unresponsive'}active{/if}" data-pjax="tabMain" href="{:url('index/index',array_merge(['sort'=>'unresponsive'],$feed_query))}" >{:L('待补充 FAQ')}</a>
                {volist name=":config('aws.tabs')" id="v"}
                <a class="nav-link nav-item {if $type==$key}active{/if}" href="{:url('index/index',['sort'=>'new','type'=>$key])}" data-pjax="tabMain">{$v.title}</a>
                {/volist}
            </div>
            <div class="aw-home-feed-filter aw-pjax-a">
                <a class="{if !$type || $current_sort=='unresponsive'}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>$current_sort])}">{:L('全部内容')}</a>
                <a class="{if $type=='question'}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>$current_sort,'type'=>'question'])}">{:L('FAQ')}</a>
                {if $current_sort!='unresponsive'}
                <a class="{if $type=='article' && $article_type=='research'}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>$current_sort,'type'=>'article','article_type'=>'research'])}">{:L('综述')}</a>
                <a class="{if $type=='article' && $article_type=='fragment'}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>$current_sort,'type'=>'article','article_type'=>'fragment'])}">{:L('观察')}</a>
                <a class="{if $type=='article' && $article_type=='faq'}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>$current_sort,'type'=>'article','article_type'=>'faq'])}">{:L('帮助')}</a>
                {/if}
            </div>
            <div class="aw-home-feed-note">
                {if $current_sort=='unresponsive'}
                {:L('当前只看仍待补充的 FAQ 条目，不再混入其他内容类型。')}
                {elseif $type=='question'}
                {:L('当前只看 FAQ 条目，更适合承接检索、高频问题和明确答案。')}
                {elseif $type=='article' && $article_type=='research'/}
                {:L('当前只看综述，更适合系统理解、资料沉淀和长期追踪。')}
                {elseif $type=='article' && $article_type=='fragment'/}
                {:L('当前只看观察，更适合快速判断、灵感记录和现场洞见。')}
                {elseif $type=='article' && $article_type=='faq'/}
                {:L('当前只看帮助内容，更适合检索术语、规则和可复用方法。')}
                {else/}
                {:L('首页会混排综述、观察、FAQ 和帮助条目，帮助用户先找到合适的知识形态。')}
                {/if}
            </div>
            <div class="tab-content" id="tabMain">
                <div class="tab-pane fade show active">
                    <div class="aw-common-list">
                        {:widget('common/lists',['sort'=>$sort,'item_type'=>$type,'article_type'=>$article_type])}
                    </div>
                </div>
            </div>
            </div>
        </div>
        <div class="aw-right radius col-md-3 px-xs-0">
            <!--侧边栏顶部钩子-->
            {:hook('sidebarTop')}

            {if get_theme_setting('home.sidebar_show_items') && in_array('write_nav',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/writeNav')}
            {/if}

            {if get_theme_setting('home.sidebar_show_items') && in_array('announce',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/announce')}
            {/if}

            {if get_theme_setting('home.sidebar_show_items') && in_array('focus_topic',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/focusTopic',['uid'=>$user_id])}
            {/if}

            {if get_theme_setting('home.sidebar_show_items') && in_array('hot_topic',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/hotTopic',['uid'=>$user_id])}
            {/if}

            {if get_theme_setting('home.sidebar_show_items') && in_array('column',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/hotColumn',['uid'=>$user_id,'sort'=>'hot'])}
            {/if}

            {if get_theme_setting('home.sidebar_show_items') && in_array('hot_users',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/hotUsers',['uid'=>$user_id])}
            {/if}

            {if get_theme_setting('home.sidebar_show_items') && in_array('diy_content',get_theme_setting('home.sidebar_show_items'))}
            {$theme_config['home']['sidebar_diy_content']|raw|htmlspecialchars_decode}
            {/if}

            <!--侧边栏底部钩子-->
            {:hook('sidebarBottom')}
        </div>
    </div>
</div>

<!--友情链接小部件-->
{:widget('common/links')}

{/block}
