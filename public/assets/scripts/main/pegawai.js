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
            url: site_url + 'main/pegawai/data'
        },
        columns: [
            { data: 'action', className: 'row-action text-center', orderable: false }, //Control
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'nip', name: 'nip_nik', className: 'text-start' },
            { data: 'nrk', name: 'nrk_id_pjlp', className: 'text-start' },
            { data: 'nama', name: 'nama_pegawai', className: 'text-start' },
            { data: 'unit_kerja', name: 'id_sub_unit_kerja', className: 'text-start' },
            { data: 'jabatan', name: 'id_jabatan', className: 'text-start' },
            { data: 'penugasan', name: 'id_penugasan', className: 'text-start' },
            { data: 'role', name: 'role', className: 'text-start', orderable: false },
        ],
        order: [[2, 'asc']],
    });
}
