{extend name="$theme_block" /}
{block name="header"}
<style>
    .aw-feature-hero-metrics {
        display: flex;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 16px;
    }
    .aw-feature-hero-metrics span {
        display: inline-flex;
        align-items: center;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.14);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
    }
    .aw-feature-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .aw-feature-card {
        display: grid;
        grid-template-columns: 280px minmax(0, 1fr);
        gap: 18px;
        padding: 18px;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.06);
    }
    .aw-feature-card-cover {
        display: block;
        min-height: 180px;
        border-radius: 16px;
        background-size: cover !important;
        background-position: center center !important;
    }
    .aw-feature-card-body h4 {
        margin-bottom: 10px;
        font-size: 22px;
        line-height: 1.35;
    }
    .aw-feature-card-body p {
        margin-bottom: 14px;
        color: #64748b;
        line-height: 1.8;
    }
    .aw-feature-card-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 12px;
    }
    .aw-feature-card-meta span {
        display: inline-flex;
        align-items: center;
        padding: 7px 12px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 600;
    }
    .aw-feature-card-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-top: 14px;
    }
    .aw-feature-card-actions a.aw-feature-link {
        color: #0f172a;
        font-weight: 700;
    }
    .aw-feature-card-actions small {
        color: #64748b;
    }
    @media (max-width: 991.98px) {
        .aw-feature-card {
            grid-template-columns: 1fr;
        }
        .aw-feature-card-cover {
            min-height: 220px;
        }
    }
</style>
<div class="w-top-img" style="height:320px;background:
    radial-gradient(circle at 18% 18%, rgba(96,165,250,.35), transparent 32%),
    radial-gradient(circle at 82% 24%, rgba(52,211,153,.25), transparent 28%),
    linear-gradient(135deg, rgba(3,37,65,.86) 0%, rgba(17,94,89,.74) 55%, rgba(15,23,42,.82) 100%),
    url('{$static_url}images/feature.png') center center;background-size:cover;">
    <div style="background: linear-gradient(180deg, rgba(5,12,24,.25) 0%, rgba(5,12,24,.42) 100%);height:320px;">
        {include file="global/nav"}
        <div class="container" style="margin-top: 78px">
            <div class="row text-center">
                <h2 class="col-12 text-white">{:L('全部观察专题')}</h2>
                <p class="mb-3 w-100" style="color: rgba(255,255,255,.86)">{:L('把同一主题下的持续观察、阶段判断和重要资料收成长期更新的观察容器。')}</p>
                <div class="col-12">
                    <div class="aw-feature-hero-metrics">
                        <span>{:L('长期更新')}</span>
                        <span>{:L('围绕主题')}</span>
                        <span>{:L('保留判断')}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}
{block name="main"}
<div class="container mt-2 aw-wrap">
    <div id="tabMain">
        {if $list}
        <div class="aw-feature-list">
        {foreach $list as $k=>$v}
        <div class="aw-feature-card">
            <div>
                <a href="{:url('feature/detail',['token'=>$v['url_token']])}" data-pjax="wrapMain">
                <div class="aw-feature-card-cover" style="background: url('{$v.image}')"></div>
                </a>
            </div>
            <div class="aw-feature-card-body">
                <div class="aw-feature-card-meta">
                    <span>{:L('观察专题')}</span>
                    <span>{:L('按主题沉淀')}</span>
                    <span>{:L('持续更新')}</span>
                </div>
                <h4><a href="{:url('feature/detail',['token'=>$v['url_token']])}" data-pjax="wrapMain">{$v.title}</a></h4>
                <p class="text-muted">{$v.description|raw}</p>
                {if !empty($v['topics'])}
                <div class="mt-3">
                    {foreach $v['topics'] as $topic}
                    <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                    {/foreach}
                </div>
                {/if}
                <div class="aw-feature-card-actions">
                    <a class="aw-feature-link" href="{:url('feature/detail',['token'=>$v['url_token']])}" data-pjax="wrapMain">{:L('进入观察专题')}</a>
                    <small>{:L('继续查看主题、动态与归档内容')}</small>
                </div>
            </div>
        </div>
        {/foreach}
        </div>
        {$page|raw}
        {else/}
        <p class="text-center py-3 text-muted">
            <img src="{$cdnUrl}/static/common/image/empty.svg">
            <span class="d-block">{:L('暂无内容')}</span>
        </p>
        {/if}
    </div>
</div>
{/block}
