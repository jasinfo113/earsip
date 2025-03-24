var privacy, terms;
$(document).ready(function () {
    $("#tab_head li a").click(function () {
        var str = string_replace($(this).attr('href'), '#', '');
        if (str === 'privacy' && privacy === undefined) {
            dataPrivacy();
        } else if (str === 'terms' && terms === undefined) {
            dataTerms();
        }
    });
    $("form input").keydown(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode === 13) {
            event.preventDefault();
            showData($(this.form).attr('data'));
        }
    });
    dataPrivacy();
});

function showData(str) {
    if (str === 'privacy') {
        if (privacy) {
            privacy.destroy();
        }
        dataPrivacy();
    } else if (str === 'terms') {
        if (terms) {
            terms.destroy();
        }
        dataTerms();
    }
}

function dataPrivacy() {
    var string = $('#form_privacy').serializeObject();
    privacy = $('#form_privacy #table_data').DataTable({
        dom: '<"top">rt<"bottom"><"clear">',
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'cms/info/privacy/data'
        },
        paging: false,
        ordering: false,
        columns: [
            {data: 'action', className: 'row-action text-center'}, //Control
            {data: 'title', name: 'title', className: 'text-start'},
            {data: 'description', name: 'description', className: 'text-start'},
        ],
    });
}

function dataTerms() {
    var string = $('#form_terms').serializeObject();
    terms = $('#form_terms #table_data').DataTable({
        dom: '<"top">rt<"bottom"><"clear">',
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'cms/info/terms/data'
        },
        paging: false,
        ordering: false,
        columns: [
            {data: 'action', className: 'row-action text-center'}, //Control
            {data: 'title', name: 'title', className: 'text-start'},
            {data: 'description', name: 'description', className: 'text-start'},
        ],
    });
}
