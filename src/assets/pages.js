f12pages = {
    activeSoringPageId: null,
    prevParenId: 0,
    prevOpened: false,
    initSorting: () => {
        const url = $('#pages-table').data('sorting-url');
        $("#pages-table > tbody").sortable({
            // opacity: 0.5,
            revert: 1,
            items: "tr",
            connectWith: "#pages-table > tbody",
            stop: function (event, ui) {
                console.log(event.target);
                f12pages.updateSortingInformation(url);
            },
            start: function (event, ui) {
                f12pages.activeSoringPageId = $(ui.item).data('key');
            }

        });
    },
    updateSortingInformation: (url) => {
        const pages = [];
        let parents = [];
        $.each($('#pages-table tr'), (key, val) => {
            id = $(val).data('key');
            if (id == f12pages.activeSoringPageId) {
                if (f12pages.prevOpened) {
                    parent_id = f12pages.prevId
                } else {
                    parent_id = f12pages.prevParenId;
                }
            } else {
                parent_id = $(val).data('parent_id');
            }
            pages.push({
                id: id,
                norder: key,
                parent_id,
            });
            if ($(val).css('display') != 'none') {
                f12pages.prevOpened = $(val).find('td span.treegrid-expander').hasClass('treegrid-expander-expanded');
                console.log(f12pages.prevOpened);
                f12pages.prevParenId = $(val).data('parent_id');
                f12pages.prevId = id;
            }
        });

        $.ajax({
            url: url,
            data: JSON.stringify({pages}),
            contentType: 'application/json',
            type: 'POST',
            error: (response) => {
                processError(response);
            }, success: (response) => {
                $.pjax.reload({container: "#pages"});
                setTimeout(function () {
                    f12pages.initSorting();
                }, 500);
                f12notification.success('Порядок обновлен');
            }
        })
    },
    move: function (id, mode, container) {

        $.ajax({
            url: "/pages/admin/move",
            data: {id: id, mode: mode},
            method: 'POST',
            success: function () {
                f12pages.updateMenus();

                if ($(container).length > 0) {
                    $.pjax.reload({container: container});
                    setTimeout(function () {
                        f12pages.initSorting();
                    }, 500);
                }

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