{extend name="$theme_block" /}
{block name="header"}
{if get_theme_setting('home.search_enable')=='Y'}
<style>
    .w-top-img {
        height: auto !important;
        min-height: 0;
    }
    .w-top-img-default {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at 14% 18%, rgba(96, 165, 250, 0.42), transparent 28%),
            radial-gradient(circle at 82% 22%, rgba(52, 211, 153, 0.28), transparent 24%),
            linear-gradient(135deg, #08152b 0%, #0b3158 44%, #0d6a64 100%);
    }
    @supports (background-image: image-set(url("x.webp") 1x)) {
        .w-top-img-default {
            background-image:
                radial-gradient(circle at 14% 18%, rgba(96, 165, 250, 0.42), transparent 28%),
                radial-gradient(circle at 82% 22%, rgba(52, 211, 153, 0.28), transparent 24%),
                linear-gradient(135deg, #08152b 0%, #0b3158 44%, #0d6a64 100%),
                image-set(
                    url('{$static_url}images/top-img.webp') 1x,
                    url('{$static_url}images/top-img.png') 1x
                );
            background-position: center center, center center, center center, right center;
            background-size: auto, auto, auto, cover;
            background-repeat: no-repeat;
        }
    }
    .w-top-img-default::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(6, 12, 23, 0.08) 0%, rgba(6, 12, 23, 0.3) 100%);
        pointer-events: none;
    }
    .w-top-img-default .container.index-search {
        position: relative;
        z-index: 2;
    }
    .aw-home-hero {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(280px, 420px);
        gap: 28px;
        align-items: center;
        min-height: 330px;
        padding: 28px 0 78px;
    }
    .aw-home-hero-copy h2 {
        margin-bottom: 16px;
        font-size: 38px;
        line-height: 1.18;
        letter-spacing: -0.02em;
        color: #fff;
        text-align: left;
    }
    .aw-home-hero-copy p {
        max-width: 680px;
        margin-bottom: 16px;
        color: rgba(226, 232, 240, 0.92);
        font-size: 15px;
        line-height: 1.75;
        text-align: left;
    }
    .aw-home-search-shell {
        width: 100%;
        max-width: 720px;
        padding: 14px;
        border-radius: 20px;
        background: rgba(7, 16, 31, 0.34);
        border: 1px solid rgba(255,255,255,0.12);
        backdrop-filter: blur(12px);
    }
    .aw-home-search-shell span {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        gap: 10px;
    }
    .aw-home-search-shell span > i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255,255,255,.72);
        font-size: 18px;
        pointer-events: none;
        z-index: 1;
    }
    .aw-home-search-shell input {
        width: 100% !important;
        min-width: 0;
        height: 60px;
        padding: 0 18px 0 54px;
        border: 1px solid rgba(255,255,255,0.15);
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(6px);
        color: #fff;
        border-radius: 14px;
    }
    .aw-home-search-shell input::placeholder {
        color: rgba(255,255,255,.72);
    }
    .aw-home-search-shell .btn {
        flex: 0 0 auto;
        min-width: 120px;
        height: 60px !important;
        margin-left: 0 !important;
        padding: 0 24px !important;
        white-space: nowrap;
        line-height: 60px;
        font-size: 14px;
        font-weight: 700;
        border-radius: 14px;
    }
    .aw-home-quick-actions {
        margin-top: 16px;
        margin-bottom: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: flex-start !important;
        position: relative;
        z-index: 2;
    }
    .aw-home-quick-actions a {
        border: 1px solid rgba(255,255,255,0.14);
        background: rgba(255,255,255,0.1);
        color: #fff !important;
        border-radius: 999px;
        padding: 7px 14px;
    }
    .aw-home-quick-actions a:hover {
        background: rgba(255,255,255,0.18);
    }
    .aw-home-hero-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 16px;
        margin-bottom: 0;
        position: relative;
        z-index: 2;
    }
    .aw-home-hero-badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.14);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
    }
    .aw-home-hero-panel {
        position: relative;
        min-height: 286px;
        border-radius: 28px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.16);
        background:
            linear-gradient(180deg, rgba(255,255,255,0.12), rgba(255,255,255,0.04)),
            rgba(9, 18, 35, 0.28);
        box-shadow: 0 24px 64px rgba(2, 12, 27, 0.28);
        backdrop-filter: blur(10px);
    }
    .aw-home-hero-panel::after {
        content: "";
        position: absolute;
        left: 18px;
        right: 18px;
        bottom: 16px;
        height: 72px;
        border-radius: 999px;
        background: radial-gradient(circle at center, rgba(34, 211, 238, 0.24), transparent 70%);
        filter: blur(10px);
        opacity: 0.95;
        pointer-events: none;
    }
    .aw-home-hero-panel::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(135deg, rgba(255,255,255,0.18), transparent 42%),
            url('{$static_url}images/top-img.webp') right center / cover no-repeat;
        opacity: 0.78;
    }
    .aw-home-hero-kicker {
        position: absolute;
        left: 24px;
        top: 22px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        border-radius: 999px;
        background: rgba(7, 16, 31, 0.44);
        border: 1px solid rgba(255,255,255,0.14);
        color: rgba(255,255,255,0.92);
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.02em;
        backdrop-filter: blur(10px);
    }
    .aw-home-hero-kicker::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: linear-gradient(135deg, #22d3ee 0%, #34d399 100%);
        box-shadow: 0 0 0 4px rgba(34, 211, 238, 0.14);
    }
    .aw-home-hero-orbit {
        position: absolute;
        right: 32px;
        top: 74px;
        z-index: 2;
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: flex-end;
    }
    .aw-home-hero-orbit span {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(255,255,255,0.12);
        color: #fff;
        font-size: 12px;
        border: 1px solid rgba(255,255,255,0.14);
        backdrop-filter: blur(6px);
    }
    .aw-home-hero-card {
        position: absolute;
        left: 20px;
        right: 20px;
        bottom: 20px;
        z-index: 2;
        padding: 20px 20px 18px;
        border-radius: 22px;
        background: linear-gradient(180deg, rgba(7, 16, 31, 0.78) 0%, rgba(9, 19, 36, 0.68) 100%);
        border: 1px solid rgba(255,255,255,0.14);
        box-shadow: 0 18px 44px rgba(3, 7, 18, 0.28);
        color: #fff;
    }
    .aw-home-hero-card strong {
        display: block;
        margin-bottom: 8px;
        font-size: 18px;
        font-weight: 700;
    }
    .aw-home-hero-card p {
        margin: 0;
        color: rgba(226, 232, 240, 0.88);
        font-size: 13px;
        line-height: 1.7;
    }
    .aw-home-hero-card small {
        display: block;
        margin-bottom: 8px;
        color: rgba(125, 211, 252, 0.92);
        font-size: 11px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }
    .aw-home-content-map {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
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
    .aw-home-main-shell {
        position: relative;
        z-index: 3;
        margin-top: 0;
    }
    .aw-home-primary-column {
        padding-top: 10px;
        border-radius: 24px;
        border: 1px solid #e7eef7;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 18px 44px rgba(15, 23, 42, 0.06);
    }
    .aw-home-sidebar .r-box {
        overflow: hidden;
        border: 1px solid #e7eef7;
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 18px 44px rgba(15, 23, 42, 0.06);
    }
    .aw-home-sidebar .r-title {
        padding: 18px 20px 0;
    }
    .aw-home-sidebar .hot-list {
        padding: 0 20px 14px;
    }
    .aw-home-sidebar .hot-list dl {
        margin-bottom: 0;
        padding: 14px 0;
        border-color: #eef2f7 !important;
    }
    .aw-home-sidebar .hot-list dl:last-child {
        border-bottom: 0 !important;
        padding-bottom: 6px;
    }
    .aw-home-sidebar .hot-list dt a {
        color: #ef4444 !important;
        font-weight: 700;
        line-height: 1.6;
    }
    .aw-home-sidebar .hot-list dd {
        color: #64748b !important;
        line-height: 1.8;
    }
    @media (max-width: 991.98px) {
        .aw-home-hero {
            grid-template-columns: 1fr;
            min-height: auto;
            padding: 24px 0 84px;
        }
        .aw-home-hero-copy h2,
        .aw-home-hero-copy p {
            text-align: center;
        }
        .aw-home-search-shell {
            margin: 0 auto;
        }
        .aw-home-search-shell .btn {
            min-width: 108px;
        }
        .aw-home-quick-actions {
            justify-content: center !important;
        }
        .aw-home-hero-badges {
            justify-content: center;
        }
        .aw-home-hero-panel {
            min-height: 240px;
        }
        .aw-home-hero-kicker {
            left: 18px;
            top: 18px;
        }
        .aw-home-content-map,
        .aw-home-curated-grid,
        .aw-home-topic-grid {
            grid-template-columns: 1fr;
        }
        .w-top-img-default {
            overflow: visible;
        }
    }
    @media (max-width: 767.98px) {
        .aw-home-hero-copy h2 {
            font-size: 30px;
        }
        .aw-home-hero {
            padding: 20px 0 136px;
        }
        .aw-home-search-shell {
            padding: 12px;
        }
        .aw-home-search-shell span {
            flex-wrap: wrap;
        }
        .aw-home-search-shell input {
            height: 52px;
            padding-left: 48px;
        }
        .aw-home-search-shell .btn {
            width: 100%;
            min-width: 0;
            height: 48px !important;
            line-height: 48px;
        }
        .aw-home-quick-actions,
        .aw-home-hero-badges {
            display: none;
        }
        .aw-home-hero-panel {
            display: none;
        }
        .aw-home-main-shell {
            margin-top: 14px;
        }
        .aw-home-hero-orbit {
            right: 18px;
            top: 64px;
        }
        .aw-home-hero-orbit span:last-child {
            display: none;
        }
    }
</style>
<div class="w-top-img {if empty($theme_config['home']['search_bg'])}w-top-img-default{/if}" {if !empty($theme_config['home']['search_bg'])}style="background: url('{$theme_config['home']['search_bg']}') center center; background-size:auto 100%;"{/if}>
    {include file="global/nav"}
    <div class="container index-search">
        <div class="aw-home-hero">
            <div class="aw-home-hero-copy">
                <h2>{$theme_config['home']['search_title_text']|default=L('公开、开放、可检索的知识系统')}</h2>
                <p>{$theme_config['home']['search_min_text']|default=L('用综述沉淀脉络，用主题追踪变化，用观察保留判断，用 FAQ 承接检索，不再依赖问答社区逻辑。')}</p>
                <form action="{:url('search/index')}" method="get" id="homeSearch" class="aw-home-search-shell">
                    <span>
                        <i class="iconfont">&#xe610;</i>
                        <input type="text" autocomplete="off" value="{:input('get.q')}" name="q" placeholder="{:L('搜索综述、观察、FAQ、主题或帮助')}">
                        <button type="button" class="btn gradientBtn px-4 ml-1" style="height: 42px;" onclick="$('#homeSearch').submit();">{:L('搜索')}</button>
                    </span>
                </form>
                <div class="aw-home-quick-actions">
                    <a class="btn btn-sm m-0" href="{:url('article/index',['type'=>'research'])}">{:frelink_content_label('research')}</a>
                    <a class="btn btn-sm m-0" href="{:url('topic/index')}">{:frelink_content_label('topic')}</a>
                    <a class="btn btn-sm m-0" href="{:url('article/index',['type'=>'fragment'])}">{:frelink_content_label('fragment')}</a>
                    <a class="btn btn-sm m-0" href="{:url('question/index')}">{:frelink_content_label('question')}</a>
                    <a class="btn btn-sm m-0" href="{:url('article/index',['type'=>'faq'])}">{:frelink_content_label('faq')}</a>
                </div>
                <div class="aw-home-hero-badges">
                    <span class="aw-home-hero-badge">{:L('开放检索')}</span>
                    <span class="aw-home-hero-badge">{:L('长期归档')}</span>
                    <span class="aw-home-hero-badge">{:L('保留判断')}</span>
                </div>
            </div>
            <div class="aw-home-hero-panel">
                <div class="aw-home-hero-kicker">{:L('公开知识系统')}</div>
                <div class="aw-home-hero-orbit">
                    <span>{:frelink_content_label('research')}{:L('沉淀脉络')}</span>
                    <span>{:frelink_content_label('fragment')}{:L('保留现场')}</span>
                    <span>{:frelink_content_label('question')}{:L('承接检索')}</span>
                </div>
                <div class="aw-home-hero-card">
                    <small>{:L('内容不是越多越好')}</small>
                    <strong>{:L('让真正有价值的思想被看见')}</strong>
                    <p>{:L('不做博眼球的碎片流，把问题、观察、综述和知识章节组织成可以持续追踪的公开知识系统。')}</p>
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
<div class="container mt-2 aw-home-main-shell">
    <div class="row justify-content-between">
        <div class="aw-left radius col-md-9 bg-white mb-2 aw-home-primary-column">
            {if !$type && in_array($sort,['new','recommend','hot'])}
            <div class="px-4 pt-4">
                <div class="aw-home-content-map">
                    <a class="aw-home-map-card" href="{:url('article/index',['type'=>'research'])}">
                        <strong>{:frelink_content_label('research')}</strong>
                        <span>{:L('适合沉淀资料脉络、核心分歧、阶段判断和后续观察点。')}</span>
                    </a>
                    <a class="aw-home-map-card" href="{:url('topic/index')}">
                        <strong>{:frelink_content_label('topic')}</strong>
                        <span>{:L('按主题聚合资料、变化和后续观察点，形成持续追踪入口。')}</span>
                    </a>
                    <a class="aw-home-map-card" href="{:url('article/index',['type'=>'fragment'])}">
                        <strong>{:frelink_content_label('fragment')}</strong>
                        <span>{:L('保留思考现场、快速判断和尚未定稿但值得记录的洞见。')}</span>
                    </a>
                    <a class="aw-home-map-card" href="{:url('question/index')}">
                        <strong>{:frelink_content_label('question')}</strong>
                        <span>{:L('承接高频搜索、明确答案和持续补充说明，作为知识系统的检索入口。')}</span>
                    </a>
                </div>
                <div class="aw-home-curated">
                    <div class="aw-home-curated-grid">
                        <section class="aw-home-curated-card">
                            <div class="aw-home-curated-head">
                                <h4>{:L('最新')}{:frelink_content_label('research')}</h4>
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
                                <h4>{:L('最新')}{:frelink_content_label('fragment')}</h4>
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
                                <h4>{:L('常见')}{:frelink_content_label('question')}</h4>
                                <a href="{:url('question/index')}">{:L('查看全部')}</a>
                            </div>
                            <div class="aw-home-curated-list">
                                {we:question limit="3" sort="new" empty="<p class='text-muted mb-0'>".L('暂无 FAQ 条目')."</p>"}
                                <a href="{:url('question/detail',['id'=>$v['id']])}" target="_blank">
                                    <small>{:frelink_content_label('question')}</small>
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
                <a class="{if !$type || $current_sort=='unresponsive'}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>$current_sort])}">{:frelink_content_label('all')}</a>
                <a class="{if $type=='question'}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>$current_sort,'type'=>'question'])}">{:frelink_content_label('question')}</a>
                {if $current_sort!='unresponsive'}
                <a class="{if $type=='article' && $article_type=='research'}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>$current_sort,'type'=>'article','article_type'=>'research'])}">{:frelink_content_label('research')}</a>
                <a class="{if $type=='article' && $article_type=='fragment'}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>$current_sort,'type'=>'article','article_type'=>'fragment'])}">{:frelink_content_label('fragment')}</a>
                <a class="{if $type=='article' && $article_type=='faq'}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>$current_sort,'type'=>'article','article_type'=>'faq'])}">{:frelink_content_label('faq')}</a>
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
                {:frelink_content_description('all')}
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
        <div class="aw-right radius col-md-3 px-xs-0 aw-home-sidebar">
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
