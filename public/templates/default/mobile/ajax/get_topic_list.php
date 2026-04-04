{volist name="list" id="v"}
<div class="aw-mobile-topic-card topic-item">
    <div class="aw-mobile-topic-card-head">
        <a href="{:url('topic/detail',['id'=>$v['id']])}" data-pjax="pageMain">
            <img src="{$v['pic']|default='static/common/image/topic.svg'}" onerror="this.src='static/common/image/topic.svg'" class="aw-mobile-topic-card-cover" alt="{$v.title|raw}">
        </a>
        <a href="{:url('topic/detail',['id'=>$v['id']])}" data-pjax="pageMain" class="aw-mobile-topic-card-title">#{$v.title|raw}</a>
    </div>
    <div class="aw-mobile-topic-card-desc">{$v.description|raw}</div>
    <div class="aw-mobile-topic-card-meta">
        <div>{:L('讨论')} {$v.discuss} · {:L('关注')} {$v.focus}</div>
        {if $user_id}
        <a href="javascript:;"
           class="aw-mobile-topic-card-action {$v['has_focus'] ? 'ygz' : 'gz'}"
           onclick="AWS_MOBILE.User.focus(this,'topic','{$v['id']}');return false;"
           data-topic-id="{$v['id']}">{$v['has_focus'] ? L('已关注') : L('关注')}</a>
        {/if}
    </div>
</div>
{/volist}
