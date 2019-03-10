f12pages = {
    move: function (id, mode, container) {

        $.ajax({
            url: "/pages/page/move",
            data: {id: id, mode: mode},
            method: 'POST',
            success: function () {
                f12pages.updateMenus();
            },
            error: function (response) {
                processError(response);
            }
        })
    },
    updateMenus: function () {
        if ($('#dropdownMenuControl').length > 0)
            $.pjax.reload({container: "#dropdownMenuControl"});

        if ($('#sideMenuControl').length > 0)
            $.pjax.reload({container: "#sideMenuControl"});
    }
}

$(document).on('click', 'div.f12-page-control-dropdown button', function () {
    $(this).parent().find('div').slideDown(100);
})

$(document).on('blur', 'div.f12-page-control-dropdown button', function () {
    let button = $(this);
    setTimeout(function () {
        button.parent().find('div').slideUp(100);
    }, 200);
})