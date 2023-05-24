$(document).on('change', 'form.autosubmit', function () {
    submitForm($(this));
});

$(document).on('keyup', 'form.autosubmit', function () {
    submitForm($(this));
});

function submitForm(form) {
    method = form.attr('method');
    action = form.attr('action');
    container = form.data('container');
    $.pjax.reload({
        url: action,
        method: method,
        container: container,
        data: form.serialize()
    })

}