{extend name="$theme_block" /}

{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
    <div class="aui-header-title">{:L('知识地图')}</div>
</header>
{/block}

{block name="main"}
<div class="main-container mt-1 mescroll" id="ajaxPage">
    <div class="bg-white px-3 py-3 mb-2">
        <h2 class="font-12 font-weight-bold mb-2">{:L('知识地图与公开知识文档')}</h2>
        <p class="text-muted font-9 mb-3">{:L('先从章节进入，再沿着相关主题和归档内容继续追踪，这里承接的是长期可维护的知识结构，而不是一次性信息流。')}</p>
        <div class="row mx-n1">
            <div class="col-6 px-1 mb-2">
                <div class="border rounded p-2 h-100">
                    <div class="font-12 font-weight-bold">{$map_summary.chapter_count|default=0}</div>
                    <div class="text-muted font-8">{:L('知识章节')}</div>
                </div>
            </div>
            <div class="col-6 px-1 mb-2">
                <div class="border rounded p-2 h-100">
                    <div class="font-12 font-weight-bold">{$map_summary.topic_count|default=0}</div>
                    <div class="text-muted font-8">{:L('相关主题')}</div>
                </div>
            </div>
            <div class="col-6 px-1">
                <div class="border rounded p-2 h-100">
                    <div class="font-12 font-weight-bold">{$map_summary.question_count|default=0}</div>
                    <div class="text-muted font-8">FAQ</div>
                </div>
            </div>
            <div class="col-6 px-1">
                <div class="border rounded p-2 h-100">
                    <div class="font-12 font-weight-bold">{$map_summary.article_count|default=0}</div>
                    <div class="text-muted font-8">{:L('知识内容')}</div>
                </div>
            </div>
        </div>
    </div>

    {if !empty($topic_connections)}
    <div class="bg-white px-3 py-3 mb-2">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <strong>{:L('从主题进入')}</strong>
            <span class="text-muted font-8">{:L('优先展示已经和知识地图形成真实关系的主题，方便从主题进入章节，再继续找到归档内容。')}</span>
        </div>
        {foreach $topic_connections as $topic}
        <div class="border rounded px-3 py-2 mb-2">
            <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="d-block text-dark mb-1" data-pjax="pageMain">
                <strong>{$topic.title}</strong>
            </a>
            <div class="text-muted font-8 mb-2">{:L('讨论')} {$topic.discuss|default=0} · {:L('关注')} {$topic.focus|default=0}</div>
            {if !empty($topic['chapters'])}
            <div class="d-flex flex-wrap">
                {foreach $topic['chapters'] as $chapter}
                <a href="{:url('help/detail',['token'=>$chapter['url_token']])}" class="badge badge-light border text-primary mr-2 mb-2" data-pjax="pageMain">{$chapter.title}</a>
                {/foreach}
            </div>
            {/if}
        </div>
        {/foreach}
    </div>
    {/if}

    <div class="bg-white px-3 py-3 mb-2">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <strong>{:L('知识章节')}</strong>
            <span class="text-muted font-8">{:L('每个章节都是一个长期容器，下面会同时展示 FAQ、知识内容和相关主题数量。')}</span>
        </div>
        {if !empty($list)}
        {foreach $list as $chapter}
        <a href="{:url('help/detail',['token'=>$chapter['url_token']])}" class="d-block border rounded px-3 py-3 text-dark mb-2" data-pjax="pageMain">
            <strong class="d-block mb-1">{$chapter.title}</strong>
            {if !empty($chapter['description'])}
            <div class="text-muted font-8 mb-2">{:str_cut(strip_tags((string)$chapter['description']),0,88)}</div>
            {/if}
            <div class="text-muted font-8">FAQ {$chapter.question_count|default=0} · {:L('知识内容')} {$chapter.article_count|default=0} · {:L('相关主题')} {$chapter.topic_count|default=0}</div>
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
