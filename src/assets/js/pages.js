function pageMove(id, mode, container) {

    $.ajax({
        url: "/pages/page/move",
        data: {id: id, mode: mode},
        method: 'POST',
        success: function () {
            $.pjax.reload({container: container});
        },
        error: function (response) {
            processError(response);
        }
    })
}
