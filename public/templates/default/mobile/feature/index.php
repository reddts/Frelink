{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
    <div class="aui-header-title">{:L('观察专题')}</div>
    <div class="aui-header-right"><a href="{:url('search/index')}" class="text-muted" data-pjax="pageMain"><i class="fa fa-search font-11"></i></a></div>
</header>
{/block}

{block name="main"}
<div class="main-container">
    <style>
        .aw-mobile-feature-hero {
            margin: 12px;
            padding: 18px 16px;
            border-radius: 22px;
            background:
                radial-gradient(circle at 16% 18%, rgba(96, 165, 250, 0.32), transparent 28%),
                radial-gradient(circle at 84% 22%, rgba(52, 211, 153, 0.22), transparent 24%),
                linear-gradient(135deg, #08152b 0%, #0b3158 44%, #0d6a64 100%);
            color: #fff;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.16);
        }
        .aw-mobile-feature-hero h2 {
            margin: 0 0 10px;
            font-size: 24px;
            font-weight: 700;
            line-height: 1.3;
        }
        .aw-mobile-feature-hero p {
            margin: 0;
            color: rgba(226, 232, 240, 0.88);
            font-size: 13px;
            line-height: 1.75;
        }
        .aw-mobile-feature-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }
        .aw-mobile-feature-tags span {
            display: inline-flex;
            align-items: center;
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.12);
            color: #fff;
            font-size: 11px;
            font-weight: 600;
        }
        .aw-mobile-feature-list {
            margin: 12px;
        }
        .aw-mobile-feature-card {
            display: block;
            margin-bottom: 12px;
            border-radius: 18px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.07);
            color: #0f172a;
        }
        .aw-mobile-feature-cover {
            height: 160px;
            background-size: cover !important;
            background-position: center center !important;
        }
        .aw-mobile-feature-body {
            padding: 14px;
        }
        .aw-mobile-feature-body h3 {
            margin: 0 0 8px;
            font-size: 17px;
            line-height: 1.45;
        }
        .aw-mobile-feature-body p {
            margin: 0 0 12px;
            color: #64748b;
            font-size: 12px;
            line-height: 1.7;
        }
        .aw-mobile-feature-topic-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 12px;
        }
        .aw-mobile-feature-topic-list span {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 600;
        }
        .aw-mobile-feature-action {
            color: #0f172a;
            font-size: 13px;
            font-weight: 700;
        }
    </style>

    <section class="aw-mobile-feature-hero">
        <h2>{:L('全部观察专题')}</h2>
        <p>{:L('把同一主题下的持续观察、阶段判断和重要资料收成长期更新的观察容器。')}</p>
        <div class="aw-mobile-feature-tags">
            <span>{:L('长期更新')}</span>
            <span>{:L('围绕主题')}</span>
            <span>{:L('保留判断')}</span>
        </div>
    </section>

    <div class="aw-mobile-feature-list">
        {if !empty($list)}
        {foreach $list as $v}
        <a href="{:url('feature/detail',['token'=>$v['url_token']])}" class="aw-mobile-feature-card" data-pjax="pageMain">
            <div class="aw-mobile-feature-cover" style="background: url('{$v.image}')"></div>
            <div class="aw-mobile-feature-body">
                <h3>{$v.title}</h3>
                <p>{$v.description|raw}</p>
                {if !empty($v['topics'])}
                <div class="aw-mobile-feature-topic-list">
                    {foreach $v['topics'] as $topic}
                    <span>{$topic.title}</span>
                    {/foreach}
                </div>
                {/if}
                <span class="aw-mobile-feature-action">{:L('进入观察专题')}</span>
            </div>
        </a>
        {/foreach}
        {$page|raw}
        {else/}
        <div class="bg-white rounded p-4 text-center text-muted">
            <img src="{$cdnUrl}/static/common/image/empty.svg" alt="{:L('暂无内容')}">
            <span class="d-block mt-2">{:L('暂无内容')}</span>
        </div>
        {/if}
    </div>
</div>
{/block}
