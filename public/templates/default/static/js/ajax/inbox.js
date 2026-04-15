function getRecipient() {
    return $.trim($('input[name=recipient_uid]').val() || '');
}

function refreshInboxDialog() {
    var recipient = getRecipient();
    if (!recipient || !$('#inbox-dialog-container').length) {
        return;
    }
    $('#inbox-dialog-container').empty();
    AWS.api.ajaxLoadMore('#inbox-dialog-container', baseUrl + "/ajax/dialog", { recipient_uid: recipient });
}

refreshInboxDialog();

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
            } else {
                layer.msg(result.msg);
            }
        },
        error: function (error) {
            if ($.trim(error.responseText) !== '') {
                layer.closeAll();
                AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
            }
        }
    });
});
