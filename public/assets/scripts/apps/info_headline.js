var refItems = [], refActionItems = [];
var table;
$(document).ready(function () {
    $("#form_query input").keydown(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode === 13) {
            event.preventDefault();
            showData();
        }
    });
    refItems = setSelections("#form_query select.select-ref", "general/selection", "ref=references&key=info_headline");
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
            url: site_url + 'apps/info/headline/data'
        },
        columns: [
            {data: 'checkbox', className: 'row-checkbox' + (U_DLT ? '' : 'd-none'), width: '40px', orderable: false}, //checkbox
            {data: 'action', className: 'row-action text-center', width: '40px', orderable: false}, //Control
            {data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false}, //number
            {data: 'title', name: 'title', className: 'text-start'},
            {data: 'description', name: 'description', className: 'text-start'},
            {data: 'ref', name: 'ref', className: 'text-start'},
            {data: 'sort', name: 'sort', className: 'text-end', width: '60px'},
            {data: 'status', name: 'status', className: 'text-center', width: '80px'},
        ],
        order: [[6, 'asc']],
    });
}
