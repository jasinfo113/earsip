var platformItems = [], categoryItems = [], refActionItems = [];
var table;
$(document).ready(function () {
    setDateRangePicker("#form_query .date-range-picker");
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
    platformItems = setSelections("#form_query select.select-platform", "general/selection", "ref=references&key=platform");
    categoryItems = setSelections("#form_query select.select-category", "general/selection", "ref=references&key=news");
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
            url: site_url + 'cms/news/data'
        },
        columns: [
            {data: 'checkbox', className: 'row-checkbox' + (U_DLT ? '' : 'd-none'), width: '40px', orderable: false}, //checkbox
            {data: 'action', className: 'row-action text-center', orderable: false}, //Control
            {data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false}, //number
            {data: 'image', name: 'image', className: 'text-center', width: '60px'},
            {data: 'date', name: 'date', className: 'text-start'},
            {data: 'category', name: 'category', className: 'text-start'},
            {data: 'title', name: 'title', className: 'text-start'},
            {data: 'viewed', name: 'viewed', className: 'text-end'},
            {data: 'shared', name: 'shared', className: 'text-end'},
            {data: 'status', name: 'status', className: 'text-center', width: '80px'},
        ],
        order: [[3, 'desc']],
    });
}
