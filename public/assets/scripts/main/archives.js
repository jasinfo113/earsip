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
    dataArchives();
});

function showData() {

    if (table) {
        table.destroy();
    }
    dataArchives();

}

function dataArchives() {
    var string = $('#form_query').serializeObject();
     table = $("#form_query #table_data").DataTable({
         dom: '<"top"i>rt<"bottom"lp><"clear">',
         lengthMenu: [
             [10, 25, 50, 100, -1],
             [10, 25, 50, 100, "All"],
         ],
         ajax: {
             type: "POST",
             data: string,
             cache: false,
             url: site_url + "main/archives/data",
         },
         columns: [
             {
                 data: "checkbox",
                 className: "row-checkbox" + (U_DLT ? "" : "d-none"),
                 width: "40px",
                 orderable: false,
             }, //checkbox
             {
                 data: "action",
                 className: "row-action text-center",
                 orderable: false,
             }, //Control
             {
                 data: "DT_RowIndex",
                 name: "no",
                 className: "text-start hide-hp",
                 width: "40px",
                 orderable: false,
             }, //number
             { data: "number", name: "number", className: "text-start" },
             {
                 data: "ref_number",
                 name: "ref_number",
                 className: "text-start",
             },
             { data: "date", name: "date", className: "text-start" },
             { data: "title", name: "title", className: "text-start" },
             {
                 data: "description",
                 name: "description",
                 className: "text-start",
             },
             { data: "note", name: "note", className: "text-start" },
             { data: "status", name: "status", className: "text-center" },
         ],
         order: [[3, "asc"]],
     });
}


function downloadFile(id) {
    // Gantilah ':id' dengan ID dokumen
    const url = "/main/archives/export/" + id; // URL statis

    // Arahkan browser ke URL untuk mengunduh
    window.location.href = url;
}
function exportData() {
    dialogOpenTab("Export data login pegawai?", "admin/pegawai/export", $("#form_pegawai").serialize());
}
