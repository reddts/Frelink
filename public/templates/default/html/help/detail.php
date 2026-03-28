{extend name="$theme_block" /}

{block name="header"}
<div class="w-top-img" style="height:300px;background: url('{$static_url}images/help.png') center center;background-size:cover;">
    <div style="background: rgba(0,0,0,.25);height:300px;">
        {include file="global/nav"}
    </div>
</div>
{/block}

{block name="main"}
<div class="container" style="margin-top: -100px">
    <div class="bg-white p-3 aw-content-shell help-shell" style="border-radius: 10px">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb px-0 pt-0" style="background: transparent">
                <li class="breadcrumb-item"><a href="{$baseUrl}">{:L('首页')}</a></li>
                <li class="breadcrumb-item"><a href="{:url('help/index')}">{:L('知识地图')}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{$info.title}</li>
            </ol>
        </nav>
        <div class="d-flex pb-2">
            {if $info.image}
            <div class="flex-fill mr-2" style="background-image:url({$info.image});background-size:cover;width:38px;max-width:38px;height: 38px;border-radius: 5px;"></div>
            {/if}
            <h2 class="font-weight-bolder aw-content-title mb-0">{$info.title}</h2>
        </div>

        <div class="mb-3 w-100 text-muted mt-3 aw-content-meta help-summary">{$info.description|raw}</div>
        <div class="row">
            <div class="col-md-8 mb-2">
                <div class="d-flex flex-wrap">
                    <span class="badge badge-light border mr-2 mb-2 px-3 py-2">{:L('已归档内容')} {$chapter_stats.total_count|default=0}</span>
                    <span class="badge badge-light border mr-2 mb-2 px-3 py-2">{:L('FAQ')} {$chapter_stats.question_count|default=0}</span>
                    <span class="badge badge-light border mr-2 mb-2 px-3 py-2">{:L('知识内容')} {$chapter_stats.article_count|default=0}</span>
                    <span class="badge badge-light border mr-2 mb-2 px-3 py-2">{:L('相关主题')} {:count($related_topics)}</span>
                </div>
                <div class="text-muted font-12 mt-1">
                    {if $chapter_stats.question_count>0 && $chapter_stats.article_count>0}
                    {:L('这个章节同时覆盖 FAQ 和知识内容，更适合做长期主题容器。')}
                    {elseif $chapter_stats.question_count>0/}
                    {:L('这个章节当前以 FAQ 为主，适合作为答案入口继续沉淀。')}
                    {elseif $chapter_stats.article_count>0/}
                    {:L('这个章节当前以知识内容为主，适合作为综述和观察的长期归档位。')}
                    {else/}
                    {:L('这个章节还在起步阶段，后续可以继续补 FAQ、综述或观察内容。')}
                    {/if}
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="border rounded p-3 h-100" style="background:linear-gradient(180deg,#f7fbff 0%,#f3f8fc 100%);border-color:#e4edf4 !important;">
                    <div class="font-weight-bold text-dark mb-2">{:L('章节定位')}</div>
                    <div class="text-muted font-12">{:L('把这个章节看作一个长期主题容器：先看已归档内容，再顺着相关主题继续扩展。')}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container aw-wrap mt-2">
    {if !empty($related_topics)}
    <div class="bg-white p-3 aw-content-shell help-list-shell mb-2">
        <div class="font-weight-bold mb-2">{:L('相关主题')}</div>
        <div class="d-flex flex-wrap">
            {foreach $related_topics as $topic}
            <a href="{:url('topic/detail',['id'=>$topic['id']])}" target="_blank" class="border rounded px-3 py-2 mr-2 mb-2 text-dark">
                <strong class="d-block mb-1">{$topic.title}</strong>
                <small class="text-muted">
                    {if !empty($topic['matched_count'])}已匹配 {$topic.matched_count|default=0} · {/if}
                    {:L('讨论')} {$topic.discuss|default=0} · {:L('关注')} {$topic.focus|default=0}
                </small>
            </a>
            {/foreach}
        </div>
    </div>
    {/if}
    <div class="bg-white p-3 aw-content-shell help-list-shell">
        <div class="d-flex flex-wrap align-items-center mb-3">
            <a href="{:url('help/detail',['token'=>$info['url_token'],'content_type'=>'all'])}" class="btn btn-sm mr-2 mb-2 {if $current_content_type=='all'}btn-primary{else/}btn-light{/if}">{:L('全部内容')}</a>
            <a href="{:url('help/detail',['token'=>$info['url_token'],'content_type'=>'question'])}" class="btn btn-sm mr-2 mb-2 {if $current_content_type=='question'}btn-primary{else/}btn-light{/if}">{:L('FAQ')}</a>
            <a href="{:url('help/detail',['token'=>$info['url_token'],'content_type'=>'article'])}" class="btn btn-sm mb-2 {if $current_content_type=='article'}btn-primary{else/}btn-light{/if}">{:L('知识内容')}</a>
        </div>
        <div class="text-muted font-12 mb-3">
            {if $current_content_type=='question'}
            {:L('当前只看这个章节里的 FAQ，适合先确认高频 FAQ 和明确答案。')}
            {elseif $current_content_type=='article'/}
            {:L('当前只看这个章节里的知识内容，适合继续阅读综述、观察和帮助资料。')}
            {else/}
            {:L('当前展示这个章节里的全部归档内容，可从 FAQ 进入，也可继续顺着知识内容深入。')}
            {/if}
        </div>
        {if !empty($list)}
        {if $current_content_type == 'all' || $current_content_type == 'question'}
        {if !empty($faq_list)}
        <div class="mb-4">
            <div class="font-weight-bold mb-2">{:L('FAQ')}</div>
            <ul class="pb-2 help-link-list mb-0">
                {foreach $faq_list as $k=>$v}
                <li class="py-2">
                    <a href="{:url($v['item_type'].'/detail',['id'=>$v['item_id']])}" target="_blank">{$v.info.title}</a>
                    <small class="d-block text-muted mt-1">{:L('FAQ')}</small>
                </li>
                {/foreach}
            </ul>
        </div>
        {/if}
        {/if}
        {if $current_content_type == 'all' || $current_content_type == 'article'}
        {if !empty($content_list)}
        <div class="mb-2">
            <div class="font-weight-bold mb-2">{:L('知识内容')}</div>
            <ul class="pb-2 help-link-list mb-0">
                {foreach $content_list as $k=>$v}
                <li class="py-2">
                    <a href="{:url($v['item_type'].'/detail',['id'=>$v['item_id']])}" target="_blank">{$v.info.title}</a>
                    <small class="d-block text-muted mt-1">{:L('知识内容')}</small>
                </li>
                {/foreach}
            </ul>
        </div>
        {/if}
        {/if}
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
