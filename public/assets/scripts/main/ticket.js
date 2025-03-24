var categoryItems = [], priorityItems = [], statusItems = [];
var table;
$(document).ready(function () {
    categoryItems = setSelections("#form_query select.select-category", "general/selection", "ref=references&key=ticket_category&status=1");
    priorityItems = setSelections("#form_query select.select-priority", "general/selection", "ref=ticket_priority");
    statusItems = setSelections("#form_query select.select-status", "general/selection", "ref=ticket_status");
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
            url: site_url + 'main/ticket/data'
        },
        columns: [
            {data: 'checkbox', className: 'row-checkbox' + (U_DLT ? '' : 'd-none'), width: '40px', orderable: false}, //checkbox
            {data: 'action', className: 'row-action text-center', orderable: false}, //Control
            {data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false}, //number
            {data: 'number', name: 'number', className: 'text-start'},
            {data: 'user', name: 'user', className: 'text-start'},
            {data: 'subject', name: 'subject', className: 'text-start'},
            {data: 'category', name: 'category', className: 'text-start'},
            {data: 'priority', name: 'priority', className: 'text-center', width: '80px'},
            {data: 'status', name: 'status', className: 'text-center', width: '80px'},
        ],
        order: [[3, 'desc']],
    });
}
