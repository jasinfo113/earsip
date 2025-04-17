var table;
$(document).ready(function () {
    $("#form_query input").keydown(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode === 13) {
            event.preventDefault();
            showData();
        }
    });
    // setSelections("#form_query select.select-role", "general/selection", "ref=user_roles");
    // setSelections("#form_query select.select-status", "general/selection", "ref=user_status");
    dataTag();
});

function showData() {

    if (table) {
        table.destroy();
    }
    dataTag();

}

function dataTag() {
    var string = $('#form_query').serializeObject();
     table = $('#form_query #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'master/tags/data'
        },
        columns: [
            {data: 'checkbox', className: 'row-checkbox' + (U_DLT ? '' : 'd-none'), width: '40px', orderable: false}, //checkbox
            {data: 'action', className: 'row-action text-center', orderable: false}, //Control
            {data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false}, //number
            { data: 'name', name: 'name', className: 'text-start' },
            { data: 'description', name: 'description', className: 'text-start' },
            { data: 'status', name: 'status', className: 'text-center' },
        ],
        order: [[3, 'asc']],
    });
}



function exportData() {
    dialogOpenTab("Export data login pegawai?", "admin/pegawai/export", $("#form_pegawai").serialize());
}
