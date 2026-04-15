function getRecipient() {
    return $.trim($('input[name=recipient_uid]').val() || '');
}

function closeInboxLayerIfPresent(triggerElement) {
    // inbox 弹窗默认通过 AWS.api.open(type=2) 以 iframe 打开，需在父窗口关闭
    if (window.parent && window.parent !== window && window.parent.layer && window.name) {
        var parentIndex = window.parent.layer.getFrameIndex(window.name);
        if (typeof parentIndex === 'number' && parentIndex >= 0) {
            window.parent.layer.close(parentIndex);
            return;
        }
    }

    if (window.layer && window.jQuery) {
        var $trigger = $(triggerElement);
        var $layer = $trigger.closest('.layui-layer');
        if (!$layer.length) {
            $layer = $trigger.parents().find('.layui-layer').first();
        }
        if (!$layer.length) {
            return;
        }

        var layerId = $layer.attr('id') || '';
        var layerIndex = parseInt(layerId.replace('layui-layer', ''), 10);
        if (!isNaN(layerIndex)) {
            layer.close(layerIndex);
        }
    }
}

function refreshInboxDialog() {
    var recipient = getRecipient();
    if (!recipient || !$('#inbox-dialog-container').length) {
        return;
    }
    $('#inbox-dialog-container').empty();
    window.AWS.api.ajaxLoadMore('#inbox-dialog-container', baseUrl + "/ajax/dialog", { recipient_uid: recipient });
}

function whenAwsReady(callback, attempts) {
    var remaining = typeof attempts === 'number' ? attempts : 120;

    function run() {
        if (window.AWS && window.AWS.api && window.jQuery) {
            callback();
            return;
        }

        if (remaining <= 0) {
            return;
        }

        remaining -= 1;
        window.setTimeout(run, 50);
    }

    if (typeof window.__onDomReady === 'function') {
        window.__onDomReady(run);
    } else {
        run();
    }
}

whenAwsReady(function () {
    refreshInboxDialog();
});

$(document).on('click', '.aw-ajax-submit', function (e) {
    var that = this;
    var options = $.extend({}, $(that).data() || {});
    var form = $($(that).parents('form')[0]);
    var recipient = getRecipient();
    var message = $.trim(form.find('textarea[name=message]').val() || '');
    if (!recipient) {
        layer.msg('请选择要发送私信的用户');
        return false;
    }
    if (!message) {
        layer.msg('请输入私信内容');
        return false;
    }
    delete options.success;
    delete options.error;
    $.ajax({
        url: form.attr('action'),
        dataType: 'json',
        type: 'post',
        data: form.serialize(),
        success: function (result) {
            if (result.code > 0) {
                $('textarea[name=message]').val('');
                refreshInboxDialog();
                closeInboxLayerIfPresent(that);
            } else {
                layer.msg(result.msg);
            }
        },
        error: function (error) {
            if ($.trim(error.responseText) !== '') {
                layer.closeAll();
                if (window.AWS && window.AWS.api) {
                    window.AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                } else {
                    layer.msg('发生错误: ' + error.responseText);
                }
            }
        }
    });
});
