<div class="p-3">
    <div class="mb-3">
        <div class="font-weight-bold mb-1">归档内容</div>
        <div class="text-muted">{$item_info.title}</div>
    </div>
    <form method="post" action="{:url('index/archivePicker',['item_type'=>$item_type,'item_id'=>$item_id])}" class="archive-picker-form">
        {:token_field()}
        <div class="mb-3">
            <div class="font-weight-bold mb-2">选择知识章节</div>
            <div class="row">
                {if $help_chapter_options}
                {volist name="help_chapter_options" id="v"}
                <div class="col-md-6 mb-2">
                    <label class="border rounded p-2 d-block mb-0" style="cursor:pointer;">
                        <input type="checkbox" name="help_chapter_ids[]" value="{$v.id}" {if !empty($v.selected)}checked{/if}>
                        <span class="ml-1">{$v.title}</span>
                    </label>
                </div>
                {/volist}
                {else/}
                <div class="col-12 text-muted">当前没有可用知识章节</div>
                {/if}
            </div>
        </div>
        <div class="text-right">
            <button type="button" class="btn btn-light btn-sm mr-2" onclick="layer.closeAll()">取消</button>
            <button type="submit" class="btn btn-primary btn-sm">保存归档</button>
        </div>
    </form>
</div>
<script>
    $(document).off('submit.archivePicker').on('submit.archivePicker', '.archive-picker-form', function (e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            dataType: 'json',
            data: form.serialize(),
            success: function (result) {
                if (result.code) {
                    layer.msg(result.msg || '归档更新成功');
                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
                } else {
                    layer.msg(result.msg || '归档更新失败');
                }
            },
            error: function (xhr) {
                layer.msg(xhr.statusText || '归档更新失败');
            }
        });
    });
</script>
