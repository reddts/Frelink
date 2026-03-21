<div class="p-3">
    <div class="mb-3">
        <div class="font-weight-bold mb-1">章节候选内容</div>
        <div class="text-muted">{$chapter.title}</div>
    </div>

    {if !empty($related_topics)}
    <div class="mb-4">
        <div class="font-weight-bold mb-2">建议关联主题</div>
        <div class="text-muted mb-2">该章节当前已覆盖的高频主题，可作为后续整理和主题页联动参考。</div>
        <div class="d-flex flex-wrap">
            {volist name="related_topics" id="topic"}
            <a href="{:get_url('topic/detail',['id'=>$topic['id']])}" target="_blank" class="border rounded px-3 py-2 mr-2 mb-2 text-dark">
                <strong class="d-block mb-1">{$topic.title}</strong>
                <small class="text-muted d-block">已匹配 {$topic.matched_count|default=0} · 讨论 {$topic.discuss|default=0} · 关注 {$topic.focus|default=0}</small>
            </a>
            {/volist}
        </div>
    </div>
    {/if}

    <div class="mb-4">
        <div class="font-weight-bold mb-2">候选 FAQ</div>
        {if $questions}
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>标题</th>
                    <th>匹配分</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                {volist name="questions" id="v"}
                <tr>
                    <td>
                        <div><a href="{:get_url('question/detail',['id'=>$v['id']])}" target="_blank">{$v.title}</a></div>
                        <small class="text-muted">补充 {$v.answer_count} · 浏览 {$v.view_count}</small>
                    </td>
                    <td>{$v.score}</td>
                    <td>
                        <a href="javascript:;" class="btn btn-xs btn-primary archive-attach-btn" data-url="{:url('index/attachArchive',['chapter_id'=>$chapter['id'],'item_type'=>'question','item_id'=>$v['id']])}">归档到本章节</a>
                    </td>
                </tr>
                {/volist}
                </tbody>
            </table>
        </div>
        {else/}
        <div class="text-muted">当前没有匹配到候选 FAQ</div>
        {/if}
    </div>

    <div>
        <div class="font-weight-bold mb-2">候选内容</div>
        {if $articles}
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>标题</th>
                    <th>匹配分</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                {volist name="articles" id="v"}
                <tr>
                    <td>
                        <div><a href="{:get_url('article/detail',['id'=>$v['id']])}" target="_blank">{$v.title}</a></div>
                        <small class="text-muted">{$v.article_type_label} · 浏览 {$v.view_count}</small>
                    </td>
                    <td>{$v.score}</td>
                    <td>
                        <a href="javascript:;" class="btn btn-xs btn-primary archive-attach-btn" data-url="{:url('index/attachArchive',['chapter_id'=>$chapter['id'],'item_type'=>'article','item_id'=>$v['id']])}">归档到本章节</a>
                    </td>
                </tr>
                {/volist}
                </tbody>
            </table>
        </div>
        {else/}
        <div class="text-muted">当前没有匹配到候选内容</div>
        {/if}
    </div>
</div>

<script>
    $(document).off('click.archiveAttach').on('click.archiveAttach', '.archive-attach-btn', function () {
        var url = $(this).data('url');
        $.get(url, function (result) {
            if (result.code) {
                layer.msg(result.msg || '已归档到章节');
                setTimeout(function () {
                    window.location.reload();
                }, 600);
            } else {
                layer.msg(result.msg || '归档失败');
            }
        }, 'json').fail(function (xhr) {
            layer.msg(xhr.statusText || '归档失败');
        });
    });
</script>
