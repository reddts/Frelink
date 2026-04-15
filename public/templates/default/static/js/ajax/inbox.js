function getRecipient() {
    return $.trim($('input[name=recipient_uid]').val() || '');
}

function closeInboxLayerIfPresent(triggerElement) {
    // inbox 弹窗默认通过 AWS.api.open(type=2) 以 iframe 打开，需在父窗口关闭
    if (window.parent && window.parent !== window && window.parent.layer && window.name) {
        var parentIndexRaw = window.parent.layer.getFrameIndex(window.name);
        var parentIndex = parseInt(parentIndexRaw, 10);
        if (!isNaN(parentIndex) && parentIndex >= 0) {
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

function refreshInboxDialog(done) {
    var recipient = getRecipient();
    var $container = $('#inbox-dialog-container');
    if (!recipient || !$container.length) {
        if (typeof done === 'function') {
            done();
        }
        return;
    }

    var finished = false;
    function finish() {
        if (finished) {
            return;
        }
        finished = true;
        if (typeof done === 'function') {
            done();
        }
    }

    function fallbackRefresh() {
        $.ajax({
            url: baseUrl + "/ajax/dialog",
            type: 'post',
            dataType: 'html',
            data: {
                recipient_uid: recipient,
                page: 1,
                _ajax: 1
            },
            success: function (html) {
                $container.html(html);
                finish();
            },
            error: function () {
                finish();
            }
        });
    }

    $container.empty();

    if (window.AWS && window.AWS.api && typeof window.AWS.api.ajaxLoadMore === 'function') {
        try {
            window.AWS.api.ajaxLoadMore('#inbox-dialog-container', baseUrl + "/ajax/dialog", { recipient_uid: recipient }, function (res, page, next) {
                var html = (res && res.data && typeof res.data.html !== 'undefined') ? res.data.html : res;
                var total = (res && res.data && res.data.last_page) ? res.data.last_page : ($($(html)[0]).data('total') || 1);
                next(html, page < total);
                finish();
            });
            return;
        } catch (e) {
            fallbackRefresh();
            return;
        }
    }

    fallbackRefresh();
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
                refreshInboxDialog(function () {
                    closeInboxLayerIfPresent(that);
                });
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
