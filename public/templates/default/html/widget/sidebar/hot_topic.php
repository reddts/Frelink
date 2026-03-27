{if !empty($topic_list)}
<div class="r-box mb-2 hot-topic">
    <style>
        .hot-topic .hot-list {
            padding: 0 20px 14px;
        }
        .hot-topic .hot-list dl {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 0;
            padding: 14px 0;
            border-bottom: 1px solid #eef2f7;
        }
        .hot-topic .hot-list dl:last-child {
            border-bottom: 0;
            padding-bottom: 4px;
        }
        .hot-topic .hot-list dt {
            flex: 0 0 41px;
            width: 41px;
            max-width: 41px;
            margin-bottom: 0;
        }
        .hot-topic .hot-list dt div {
            width: 41px;
            height: 41px;
            border-radius: 12px;
            background-size: cover;
            background-position: center center;
        }
        .hot-topic .hot-list dd {
            flex: 1 1 auto;
            min-width: 0;
            width: auto !important;
            margin-bottom: 0;
        }
        .hot-topic .hot-list dd h3 {
            margin-bottom: 6px;
            font-size: 16px;
            line-height: 1.35;
        }
        .hot-topic .hot-list dd h3 a {
            display: block;
            color: #0f172a;
        }
        .hot-topic .hot-list dd label {
            display: inline-flex;
            align-items: center;
            margin-right: 10px;
            margin-bottom: 0;
            color: #94a3b8;
            font-size: 12px;
        }
        .hot-topic .hot-list dd label i {
            margin-left: 2px;
            color: #4338ca;
            font-style: normal;
        }
    </style>
    <div class="r-title">
        <h4>{:L('核心主题')}</h4>
        <a href="{:url('topic/index',['type'=>'discuss'])}" target="_blank"><label class="iconfont">&#xe660;</label></a>
    </div>
    <div class="hot-list">
        {volist name="topic_list" id="v"}
        <dl>
            <dt>
                <a href="{:url('topic/detail',['id'=>$v['id']])}" class="aw-topic" data-id="{$v.id}">
                    <div style='background-image:url("{$v['pic']|default='/static/common/image/topic.svg'}")'></div>
                </a>
            </dt>
            <dd>
                <h3 class="aw-one-line"><a href="{:url('topic/detail',['id'=>$v['id']])}" class="aw-topic" data-id="{$v.id}">{$v.title}</a></h3>
                <label>{:L('讨论')}:<i>{$v.discuss}</i></label>
                <label>{:L('关注')}:<i>{$v.focus}</i></label>
            </dd>
        </dl>
        {/volist}
    </div>
</div>
{/if}
