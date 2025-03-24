var roleItems = [], menuItems = [];
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
    roleItems = setSelections("#form_query select.select-role", "general/selection", "ref=user_roles");
    menuItems = setSelections("#form_query select.select-menu", "general/selection", "ref=menu");
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
            url: site_url + 'manage/user/privilege/data'
        },
        ordering: false,
        columns: [
            { data: 'checkbox', className: 'row-checkbox' + (U_DLT ? '' : 'd-none'), width: '30px', orderable: false }, //checkbox
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'role', name: 'role', className: 'text-start' },
            { data: 'menu', name: 'menu', className: 'text-start' },
            { data: 'read', name: 'read', className: 'text-center', width: '80px' },
            { data: 'create', name: 'create', className: 'text-center', width: '80px' },
            { data: 'update', name: 'update', className: 'text-center', width: '80px' },
            { data: 'delete', name: 'delete', className: 'text-center', width: '80px' },
            { data: 'export', name: 'export', className: 'text-center', width: '80px' },
            { data: 'approve', name: 'approve', className: 'text-center', width: '80px' },
        ],
        order: [[2, 'asc']],
    });
}
