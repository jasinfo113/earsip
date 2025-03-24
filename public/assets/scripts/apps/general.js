var table;
$(document).ready(function () {
    $("#form_query #table_data").on("init.dt page.dt order.dt length.dt", function () {
        callbackData("#form_query", table);
    });
    $("#form_query input").keydown(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode === 13) {
            event.preventDefault();
            showData();
        }
    });
    dataTable();
});

function showData() {
    if (table) {
        table.destroy();
    }
    dataTable();
}

function dataTable() {
    var string = $('#form_query').serializeObject();
    table = $('#form_query #table_data').DataTable({
        dom: '<"top">rt<"bottom"><"clear">',
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'apps/general/data'
        },
        paging: false,
        ordering: false,
        columns: [
            {data: 'action', className: 'row-action text-center'}, //Control
            {data: 'logo', name: 'name', className: 'text-center'},
            {data: 'name', name: 'name', className: 'text-start'},
            {data: 'tagline', name: 'tagline', className: 'text-start'},
            {data: 'address', name: 'address', className: 'text-start'},
            {data: 'contact', name: 'sosmed', className: 'text-center'},
        ],
    });
}
