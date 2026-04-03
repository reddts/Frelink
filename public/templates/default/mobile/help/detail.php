{extend name="$theme_block" /}

{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="{:url('help/index')}" class="text-muted" data-pjax="pageMain"><i class="fa fa-angle-left font-14"></i></a></div>
    <div class="aui-header-title">{:L('知识章节')}</div>
</header>
{/block}

{block name="main"}
<div class="main-container mt-1 mescroll" id="ajaxPage">
    <div class="bg-white px-3 py-3 mb-2">
        <h1 class="font-12 font-weight-bold mb-2">{$info.title}</h1>
        {if !empty($info['description'])}
        <div class="text-muted font-9 aw-content mb-3">{$info.description|raw}</div>
        {/if}
        <div class="d-flex flex-wrap">
            <span class="badge badge-light border mr-2 mb-2">{:L('已归档内容')} {$chapter_stats.total_count|default=0}</span>
            <span class="badge badge-light border mr-2 mb-2">FAQ {$chapter_stats.question_count|default=0}</span>
            <span class="badge badge-light border mr-2 mb-2">{:L('知识内容')} {$chapter_stats.article_count|default=0}</span>
            <span class="badge badge-light border mb-2">{:L('相关主题')} {:count($related_topics)}</span>
        </div>
    </div>

    {if !empty($related_topics)}
    <div class="bg-white px-3 py-3 mb-2">
        <strong class="d-block mb-2">{:L('相关主题')}</strong>
        <div class="d-flex flex-wrap">
            {foreach $related_topics as $topic}
            <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="badge badge-light border text-primary mr-2 mb-2" data-pjax="pageMain">{$topic.title}</a>
            {/foreach}
        </div>
    </div>
    {/if}

    <div class="bg-white px-3 py-3 mb-2">
        <div class="d-flex flex-wrap mb-2">
            <a href="{:url('help/detail',['token'=>$info['url_token'],'content_type'=>'all'])}" class="btn btn-sm mr-2 mb-2 {if $current_content_type=='all'}btn-primary{else/}btn-light{/if}" data-pjax="pageMain">{:L('全部内容')}</a>
            <a href="{:url('help/detail',['token'=>$info['url_token'],'content_type'=>'question'])}" class="btn btn-sm mr-2 mb-2 {if $current_content_type=='question'}btn-primary{else/}btn-light{/if}" data-pjax="pageMain">FAQ</a>
            <a href="{:url('help/detail',['token'=>$info['url_token'],'content_type'=>'article'])}" class="btn btn-sm mb-2 {if $current_content_type=='article'}btn-primary{else/}btn-light{/if}" data-pjax="pageMain">{:L('知识内容')}</a>
        </div>

        {if !empty($list)}
        {foreach $list as $item}
        <a href="{:url($item['item_type'].'/detail',['id'=>$item['item_id']])}" class="d-block border-bottom py-3 text-dark" data-pjax="pageMain">
            <strong class="d-block mb-1">{$item.info.title}</strong>
            <div class="text-muted font-8">
                {if $item['item_type']=='question'}
                FAQ
                {else/}
                {:L('知识内容')}
                {/if}
            </div>
        </a>
        {/foreach}
        {$page|raw}
        {else/}
        <div class="text-center py-4 text-muted">
            <img src="{$cdnUrl}/static/common/image/empty.svg" alt="" style="width:72px;max-width:72px;">
            <span class="d-block mt-2">{:L('暂无内容')}</span>
        </div>
        {/if}
    </div>
</div>
{/block}

{block name="footer"}{/block}
