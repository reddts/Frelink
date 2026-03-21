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
        <div class="d-flex flex-wrap text-muted font-12">
            <span class="mr-3">{:L('已归档内容')} {$chapter_stats.total_count|default=0}</span>
            <span class="mr-3">{:L('FAQ 条目')} {$chapter_stats.question_count|default=0}</span>
            <span>{:L('知识内容')} {$chapter_stats.article_count|default=0}</span>
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
            <a href="{:url('help/detail',['token'=>$info['url_token'],'content_type'=>'question'])}" class="btn btn-sm mr-2 mb-2 {if $current_content_type=='question'}btn-primary{else/}btn-light{/if}">{:L('FAQ 条目')}</a>
            <a href="{:url('help/detail',['token'=>$info['url_token'],'content_type'=>'article'])}" class="btn btn-sm mb-2 {if $current_content_type=='article'}btn-primary{else/}btn-light{/if}">{:L('知识内容')}</a>
        </div>
        {if !empty($list)}
        {if $current_content_type == 'all' || $current_content_type == 'question'}
        {if !empty($faq_list)}
        <div class="mb-4">
            <div class="font-weight-bold mb-2">{:L('FAQ 条目')}</div>
            <ul class="pb-2 help-link-list mb-0">
                {foreach $faq_list as $k=>$v}
                <li class="py-2">
                    <a href="{:url($v['item_type'].'/detail',['id'=>$v['item_id']])}" target="_blank">{$v.info.title}</a>
                    <small class="d-block text-muted mt-1">{:L('FAQ 条目')}</small>
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
