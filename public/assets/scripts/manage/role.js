var table;
$(document).ready(function () {
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
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'manage/user/role/data'
        },
        ordering: false,
        columns: [
            { data: 'action', className: 'row-action text-center', orderable: false }, //Control
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'name', name: 'name', className: 'text-start' },
            { data: 'description', name: 'description', className: 'text-start' },
            { data: 'status', name: 'status', className: 'text-center', width: '80px' },
        ],
        order: [[2, 'asc']],
    });
}
