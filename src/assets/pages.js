f12pages = {
    activeSoringPageId: null,
    prevParenId: 0,
    prevOpened: false,
    initPageForm: () => {
        $(document).on('keyup', '#ai-content-query', function () {
            f12pages.processPageForm();
        });
    },
    processPageForm() {
        const queryLenght = $('#ai-content-query').val().length;
        if (queryLenght > 10) {
            $('#ai-content-btn').attr('disabled', false);
        } else {
            $('#ai-content-btn').attr('disabled', true);
        }
    },
    makeContent: () => {
        let query = $('#ai-content-query').val();
        query = query.replace(/^\s+|\s+$/g, '');
        const lang = $('#page-lang').val();
        // if (query.length < 20) {
        //     f12notification.error('ChatGPT query is too short. Please enter at least 20 characters.');
        //     $('#ai-content-query').focus();
        //     return;
        // }
        $('#ai-content-btn').attr('disabled', true);
        $.ajax({
            url: "/pages/admin/make-content",
            data: {query, lang},
            method: 'POST',
            timeout: 60000,
            success: function (response) {
                console.log(response);
                $('#page-content').summernote('pasteHTML', response);
                $('#ai-content-btn').attr('disabled', false);
            },
            error: function (response, code) {
                $('#ai-content-btn').attr('disabled', false);
            }
        })
    },
    makeMeta: (mode) => {
        let pageContent = $('#page-announce').val() + $('#page-content').val();
        const stripTagsAndTrim = (str) => {
            return str.replace(/(<([^>]+)>)/gi, "").trim();
        }
        if (pageContent.length < 20) {
            f12notification.error('Page content is too short. Please enter at least 20 characters.');
            $('#page-content').focus();
            return;
        }
        pageContent = stripTagsAndTrim(pageContent);
        $('#ai-meta-btn').attr('disabled', true);
        $.ajax({
            url: "/pages/admin/make-meta",
            data: {pageContent: pageContent},
            method: 'POST',
            success: function (response) {
                if (mode == 'h1' || mode == 'all') {
                    $('#page-title').val(response.h1);
                }
                if (mode == 'title' || mode == 'all') {
                    $('#page-title_seo').val(response.title);
                }
                if (mode == 'descr' || mode == 'all') {
                    $('#page-description_seo').html(response.description);
                }
                $('#ai-meta-btn').attr('disabled', false);
            },
            error: function (response) {
                processError(response);
                $('#ai-meta-btn').attr('disabled', false);
            }
        })
    },
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