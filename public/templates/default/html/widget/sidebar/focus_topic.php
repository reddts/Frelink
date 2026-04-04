{if $user_id && !empty($topic_list)}
<div class="r-box mb-2 aw-focus-topic-box">
    <style>
        .aw-focus-topic-box {
            padding: 18px 18px 14px;
            border: 1px solid #d9e4ec;
            border-radius: 24px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfd 100%);
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
        }

        .aw-focus-topic-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .aw-focus-topic-head h4 {
            margin: 0;
            color: #0f172a;
            font-size: 16px;
            font-weight: 800;
        }

        .aw-focus-topic-head a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 999px;
            border: 1px solid #d9e4ec;
            color: #64748b;
            background: #fff;
        }

        .aw-focus-topic-head a:hover {
            color: #0f172a;
            text-decoration: none;
            border-color: #c4d5e2;
            background: #f8fbfd;
        }

        .aw-focus-topic-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
        }

        .aw-focus-topic-tag {
            display: inline-flex;
            align-items: center;
            min-height: 38px;
            padding: 0 14px;
            border-radius: 12px;
            border: 1px solid #d9e4ec;
            background: #f3f7fa;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
            line-height: 1;
            transition: all 0.2s ease;
        }

        .aw-focus-topic-tag:hover,
        .aw-focus-topic-tag.is-active {
            color: #0f172a;
            text-decoration: none;
            border-color: #c9d8e6;
            background: linear-gradient(180deg, #ffffff 0%, #eef6fb 100%);
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.05);
        }

        .aw-focus-topic-preview {
            position: relative;
            overflow: hidden;
            padding: 16px;
            border-radius: 16px;
            border: 1px solid #e7eef5;
            background: linear-gradient(180deg, #fbfdff 0%, #f4f8fc 100%);
        }

        .aw-focus-topic-preview-item {
            display: none;
        }

        .aw-focus-topic-preview-item.is-active {
            display: block;
        }

        .aw-focus-topic-preview-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
        }

        .aw-focus-topic-preview-title a {
            color: #0f172a;
            font-size: 15px;
            font-weight: 800;
            line-height: 1.35;
        }

        .aw-focus-topic-preview-title a:hover {
            color: #0f766e;
            text-decoration: none;
        }

        .aw-focus-topic-preview-arrow {
            color: #0f172a;
            font-size: 20px;
            line-height: 1;
        }

        .aw-focus-topic-metrics {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            color: #0f172a;
            font-size: 12px;
            font-weight: 800;
        }

        .aw-focus-topic-metrics span {
            color: #64748b;
            font-weight: 600;
        }
    </style>
    <div class="aw-focus-topic-head">
        <h4>{:L('关注话题')}</h4>
        <a href="{:url('focus/index',['type'=>'topic'])}" target="_blank" aria-label="{:L('查看全部关注话题')}"><label class="iconfont">&#xe660;</label></a>
    </div>
    <div class="aw-focus-topic-tags">
        {foreach $topic_list as $key=>$v}
        <a href="{:url('topic/detail',['id'=>$v['id']])}" data-id="{$v['id']}" class="aw-focus-topic-tag{if $key==0} is-active{/if}">{$v.title}</a>
        {/foreach}
    </div>
    <div class="aw-focus-topic-preview">
        {foreach $topic_list as $key=>$v}
        <div class="aw-focus-topic-preview-item{if $key==0} is-active{/if}" id="topic{$v.id}">
            <div class="aw-focus-topic-preview-title">
                <a href="{:url('topic/detail',['id'=>$v['id']])}">{$v.title}</a>
                <a href="{:url('topic/detail',['id'=>$v['id']])}" class="aw-focus-topic-preview-arrow" aria-label="{:L('进入话题')}">&rarr;</a>
            </div>
            <div class="aw-focus-topic-metrics">
                <strong>{$v.question_count} <span>{:L('FAQ')}</span></strong>
                <strong>{$v.article_count} <span>{:L('内容')}</span></strong>
            </div>
        </div>
        {/foreach}
    </div>
</div>
<script>
    $('.aw-focus-topic-tags .aw-focus-topic-tag').on('mouseenter focus', function () {
        var topic = $(this).attr('data-id');
        $('.aw-focus-topic-tag').removeClass('is-active');
        $(this).addClass('is-active');
        $('.aw-focus-topic-preview-item').removeClass('is-active');
        $('#topic' + topic).addClass('is-active');
    });
</script>
{/if}
