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
        ordering: false,
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'manage/client/data'
        },
        columns: [
            {data: 'checkbox', className: 'row-checkbox' + (U_DLT ? '' : 'd-none'), width: '40px', orderable: false}, //checkbox
            {data: 'action', className: 'row-action text-center', orderable: false}, //Control
            {data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false}, //number
            {data: 'name', name: 'name', className: 'text-start'},
            {data: 'penugasan', name: 'penugasan', className: 'text-start'},
            {data: 'api', name: 'api', className: 'text-center', width: '80px'},
            {data: 'web', name: 'web', className: 'text-center', width: '80px'},
            {data: 'status', name: 'status', className: 'text-center', width: '80px'},
        ],
        order: [[3, 'asc']],
    });
}
