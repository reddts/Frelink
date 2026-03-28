{extend name="$theme_block" /}

{block name="header"}
<div class="w-top-img" style="height:300px;background: url('{$static_url}images/help.png') center center;background-size:cover;">
    <div style="background: rgba(0,0,0,.25);height:300px;">
        {include file="global/nav"}
    </div>
</div>
{/block}

{block name="main"}
<style>
    .aw-help-page {
        margin-top: -96px;
        margin-bottom: 24px;
    }
    .aw-help-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) minmax(300px, 0.95fr);
        gap: 18px;
        padding: 24px;
        border: 1px solid #e7eef7;
        border-radius: 26px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 18px 44px rgba(15, 23, 42, 0.08);
    }
    .aw-help-hero-kicker {
        display: inline-flex;
        align-items: center;
        margin-bottom: 12px;
        padding: 6px 12px;
        border-radius: 999px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
    }
    .aw-help-hero h1 {
        margin: 0 0 12px;
        color: #0f172a;
        font-size: 32px;
        font-weight: 800;
        line-height: 1.15;
    }
    .aw-help-hero-desc {
        margin: 0 0 18px;
        color: #475569;
        font-size: 15px;
        line-height: 1.85;
    }
    .aw-help-search {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 18px;
        padding: 10px;
        border-radius: 18px;
        border: 1px solid #dbe7f3;
        background: rgba(255,255,255,.82);
    }
    .aw-help-search i {
        color: #64748b;
        font-size: 16px;
        padding-left: 8px;
    }
    .aw-help-search input {
        flex: 1;
        height: 48px;
        border: 0;
        background: transparent;
        color: #0f172a;
        font-size: 14px;
    }
    .aw-help-search input:focus {
        outline: none;
    }
    .aw-help-search button {
        height: 44px;
        min-width: 104px;
        border-radius: 14px;
    }
    .aw-help-stat-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 16px;
    }
    .aw-help-stat-card {
        padding: 14px 12px;
        border-radius: 16px;
        border: 1px solid #e5edf6;
        background: #fff;
    }
    .aw-help-stat-card strong {
        display: block;
        margin-bottom: 6px;
        color: #0f172a;
        font-size: 20px;
        font-weight: 800;
    }
    .aw-help-stat-card span {
        color: #64748b;
        font-size: 12px;
        line-height: 1.45;
    }
    .aw-help-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .aw-help-chip {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid #dbe7f3;
        background: #fff;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
    }
    .aw-help-panel {
        padding: 18px;
        border-radius: 20px;
        border: 1px solid #e5edf6;
        background: #fff;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.04);
    }
    .aw-help-panel + .aw-help-panel {
        margin-top: 12px;
    }
    .aw-help-panel h3 {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: 16px;
        font-weight: 800;
    }
    .aw-help-panel p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.75;
    }
    .aw-help-panel .btn {
        border-radius: 999px;
    }
    .aw-help-section {
        margin-top: 18px;
        padding: 22px;
        border: 1px solid #e7eef7;
        border-radius: 24px;
        background: #fff;
        box-shadow: 0 14px 36px rgba(15, 23, 42, 0.05);
    }
    .aw-help-section-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }
    .aw-help-section-head h2 {
        margin: 0 0 6px;
        color: #0f172a;
        font-size: 22px;
        font-weight: 800;
    }
    .aw-help-section-head p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.7;
    }
    .aw-help-topic-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }
    .aw-help-topic-card {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 18px;
        border: 1px solid #e5edf6;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        color: #0f172a;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }
    .aw-help-topic-title {
        color: #0f172a;
        font-size: 16px;
        font-weight: 800;
        line-height: 1.35;
    }
    .aw-help-topic-meta,
    .aw-help-topic-desc {
        color: #64748b;
        font-size: 12px;
        line-height: 1.7;
    }
    .aw-help-tag-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .aw-help-tag-list a {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid #dbe7f3;
        background: #fff;
        color: #1d4ed8;
        font-size: 12px;
        white-space: nowrap;
    }
    .aw-help-chapter-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }
    .aw-help-chapter-card {
        display: flex;
        flex-direction: column;
        min-height: 100%;
        padding: 18px;
        border-radius: 18px;
        border: 1px solid #e5edf6;
        background: #fff;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }
    .aw-help-chapter-head {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 10px;
    }
    .aw-help-chapter-icon {
        flex: 0 0 auto;
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: linear-gradient(180deg, #eff6ff 0%, #f8fbff 100%);
        background-size: cover;
        background-position: center center;
        box-shadow: inset 0 0 0 1px rgba(37, 99, 235, 0.08);
    }
    .aw-help-chapter-title {
        display: block;
        margin-bottom: 4px;
        color: #0f172a;
        font-size: 16px;
        font-weight: 800;
        line-height: 1.35;
    }
    .aw-help-chapter-sub {
        color: #64748b;
        font-size: 12px;
        line-height: 1.6;
    }
    .aw-help-chapter-desc {
        margin: 0 0 12px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.75;
    }
    .aw-help-badge-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px;
    }
    .aw-help-badge-row span {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid #dbe7f3;
        background: #f8fbff;
        color: #475569;
        font-size: 12px;
    }
    .aw-help-related {
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid #eef2f7;
    }
    .aw-help-related .tag {
        display: inline-flex;
        align-items: center;
        margin: 0 8px 8px 0;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid #dbe7f3;
        background: #fff;
        color: #1d4ed8;
        font-size: 12px;
    }
    .aw-help-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 220px;
        border: 1px dashed #dbe7f3;
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        color: #64748b;
        font-size: 14px;
    }
    @media (max-width: 991.98px) {
        .aw-help-hero,
        .aw-help-topic-grid,
        .aw-help-chapter-grid {
            grid-template-columns: 1fr;
        }
        .aw-help-stat-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 767.98px) {
        .aw-help-page {
            margin-top: -74px;
        }
        .aw-help-hero,
        .aw-help-section {
            padding: 16px;
            border-radius: 20px;
        }
        .aw-help-hero h1 {
            font-size: 26px;
        }
        .aw-help-stat-grid {
            grid-template-columns: 1fr;
        }
        .aw-help-section-head {
            flex-direction: column;
        }
    }
</style>
<div class="container aw-help-page">
    <section class="aw-help-hero">
        <div>
            <div class="aw-help-hero-kicker">{:L('知识地图')}</div>
            <h1>{:L('知识地图与公开知识文档')}</h1>
            <p class="aw-help-hero-desc">{:L('把 FAQ、术语解释、研究资料和规则说明组织成长期可检索的知识地图。这里不是一次性的信息流，而是可维护、可追踪、可回收利用的公开知识结构。')}</p>
            <form action="{:url('search/index')}" method="get" id="homeSearch" class="aw-help-search">
                <i class="iconfont">&#xe610;</i>
                <input type="text" autocomplete="off" value="{:input('get.q')}" name="q" placeholder="{:L('请输入你想查找的 FAQ、知识章节或主题')}">
                <button type="submit" class="btn btn-primary">{:L('搜索')}</button>
            </form>
            <div class="aw-help-stat-grid">
                <div class="aw-help-stat-card">
                    <strong>{$map_summary.chapter_count|default=0}</strong>
                    <span>{:L('知识章节')}</span>
                </div>
                <div class="aw-help-stat-card">
                    <strong>{$map_summary.relation_count|default=0}</strong>
                    <span>{:L('已归档内容')}</span>
                </div>
                <div class="aw-help-stat-card">
                    <strong>{$map_summary.question_count|default=0}</strong>
                    <span>{:L('FAQ')}</span>
                </div>
                <div class="aw-help-stat-card">
                    <strong>{$map_summary.article_count|default=0}</strong>
                    <span>{:L('知识内容')}</span>
                </div>
                <div class="aw-help-stat-card">
                    <strong>{$map_summary.topic_count|default=0}</strong>
                    <span>{:L('相关主题')}</span>
                </div>
            </div>
            <div class="aw-help-chip-row">
                <a class="aw-help-chip" href="{:url('help/index')}">{:L('先看章节结构')}</a>
                <a class="aw-help-chip" href="{:url('topic/index')}">{:L('再找相关主题')}</a>
                <a class="aw-help-chip" href="{:url('question/index')}">{:L('继续顺着 FAQ 读')}</a>
                <a class="aw-help-chip" href="{$baseUrl}/docs/api-v1.openapi.json" target="_blank">{:L('OpenAPI')}</a>
            </div>
        </div>
        <div>
            <div class="aw-help-panel">
                <h3>{:L('这张知识地图怎么用')}</h3>
                <p>{:L('先从章节进入，再沿着相关主题和归档内容继续追踪。优先看已经形成连接的内容，避免在零散页面之间来回跳转。')}</p>
            </div>
            <div class="aw-help-panel">
                <h3>{:L('长期追踪入口')}</h3>
                <p>{:L('主题连接、归档内容和 FAQ 会一起构成一个可持续更新的知识结构，适合继续补充综述、观察和帮助文档。')}</p>
            </div>
            <div class="aw-help-panel">
                <h3>{:L('API 接口文档')}</h3>
                <p class="mb-3">{:L('接口说明由代码自动生成，适合给 agent、前端调用和运维排查直接查看。')}</p>
                <div class="d-flex flex-wrap">
                    <a href="{:url('help/api')}" class="btn btn-sm btn-primary mr-2 mb-2" target="_blank">{:L('查看 API 文档')}</a>
                    <a href="{$baseUrl}/docs/api-v1.openapi.json" class="btn btn-sm btn-outline-primary mb-2" target="_blank" download>{:L('下载 OpenAPI')}</a>
                </div>
            </div>
        </div>
    </section>

    {if !empty($topic_connections)}
    <section class="aw-help-section">
        <div class="aw-help-section-head">
            <div>
                <h2>{:L('长期主题连接')}</h2>
                <p>{:L('优先展示已经和知识地图形成真实关系的主题，方便从主题进入章节，再继续找到归档内容。')}</p>
            </div>
            <a href="{:url('topic/index')}" class="text-primary">{:L('查看全部')}</a>
        </div>
        <div class="aw-help-topic-grid">
            {foreach $topic_connections as $topic}
            <article class="aw-help-topic-card">
                <a class="aw-help-topic-title" href="{:url('topic/detail',['id'=>$topic['id']])}" target="_blank">{$topic.title}</a>
                <div class="aw-help-topic-meta">{:L('已连接章节')} {$topic.chapter_count|default=0} · {:L('已归档内容')} {$topic.matched_count|default=0}</div>
                {if !empty($topic.description)}
                <p class="aw-help-topic-desc">{$topic.description}</p>
                {/if}
                {if !empty($topic['chapters'])}
                <div class="aw-help-tag-list">
                    {foreach $topic['chapters'] as $chapter}
                    <a href="{:url('help/detail',['token'=>$chapter['url_token']])}" target="_blank">{$chapter.title}</a>
                    {/foreach}
                </div>
                {/if}
            </article>
            {/foreach}
        </div>
    </section>
    {/if}

    <section class="aw-help-section">
        <div class="aw-help-section-head">
            <div>
                <h2>{:L('知识章节')}</h2>
                <p>{:L('每个章节都是一个长期容器，下面的卡片会同时展示 FAQ、知识内容和相关主题的数量。')}</p>
            </div>
            <div class="aw-help-chip-row">
                <span class="aw-help-chip">{:L('章节')} {$map_summary.chapter_count|default=0}</span>
                <span class="aw-help-chip">{:L('主题')} {$map_summary.topic_count|default=0}</span>
            </div>
        </div>
        {if $list}
        <div class="aw-help-chapter-grid">
            {foreach $list as $v}
            <article class="aw-help-chapter-card">
                <div class="aw-help-chapter-head">
                    {if $v.image}
                    <div class="aw-help-chapter-icon" style="background-image:url({$v.image});"></div>
                    {/if}
                    <div>
                        <a class="aw-help-chapter-title" href="{:url('help/detail',['token'=>$v.url_token])}" data-pjax="wrapMain">{$v.title}</a>
                        <div class="aw-help-chapter-sub">{:L('FAQ')} {$v.question_count|default=0} · {:L('知识内容')} {$v.article_count|default=0} · {:L('相关主题')} {$v.topic_count|default=0}</div>
                    </div>
                </div>
                {if !empty($v.description)}
                <p class="aw-help-chapter-desc">{:str_cut(strip_tags((string)$v['description']),0,96)}</p>
                {/if}
                <div class="aw-help-badge-row">
                    <span>{:L('FAQ')} {$v.question_count|default=0}</span>
                    <span>{:L('知识内容')} {$v.article_count|default=0}</span>
                    <span>{:L('相关主题')} {$v.topic_count|default=0}</span>
                </div>
                {if !empty($v.related_topics)}
                <div class="aw-help-related">
                    {foreach $v.related_topics as $topic}
                    <a class="tag" href="{:url('topic/detail',['id'=>$topic['id']])}" target="_blank">{$topic.title}</a>
                    {/foreach}
                </div>
                {/if}
                {if isset($v.chapters) && $v.chapters}
                <div class="aw-help-related">
                    {foreach $v['chapters'] as $v1}
                    <a class="tag" href="{:url($v1['item_type'].'/detail',['id'=>$v1['item_id']])}" target="_blank">{$v1.info.title}</a>
                    {/foreach}
                </div>
                {/if}
                <div class="text-muted font-12 mt-3">
                    {if $v.question_count>0 && $v.article_count>0}
                    {:L('这个章节同时覆盖 FAQ 和知识内容，更适合做长期主题容器。')}
                    {elseif $v.question_count>0}
                    {:L('这个章节当前以 FAQ 为主，适合作为答案入口继续沉淀。')}
                    {elseif $v.article_count>0}
                    {:L('这个章节当前以知识内容为主，适合作为综述和观察的长期归档位。')}
                    {else/}
                    {:L('这个章节还在起步阶段，后续可以继续补 FAQ、综述或观察内容。')}
                    {/if}
                </div>
                <div class="mt-3 pt-3 border-top text-center">
                    <a href="{:url('help/detail',['token'=>$v.url_token])}" data-pjax="wrapMain" class="text-primary">{:L('查看全部')}</a>
                </div>
            </article>
            {/foreach}
        </div>
        {$page|raw}
        {else/}
        <div class="aw-help-empty">
            <div class="text-center">
                <img src="{$cdnUrl}/static/common/image/empty.svg" alt="" style="width:96px;max-width:96px;">
                <div class="d-block mt-2">{:L('暂无内容')}</div>
            </div>
        </div>
        {/if}
    </section>
</div>
{/block}
