var sosialisasi, pembinaan;
$(document).ready(function () {
    $("#tab_head li a").click(function () {
        var str = string_replace($(this).attr('href'), '#', '');
        if (str === 'sosialisasi' && sosialisasi === undefined) {
            dataSosialisasi();
        } else if (str === 'pembinaan' && pembinaan === undefined) {
            dataPembinaan();
        }
    });
    $("form input").keydown(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode === 13) {
            event.preventDefault();
            showData($(this.form).attr('data'));
        }
    });
    dataSosialisasi();
});

function showData(str) {
    if (str === 'sosialisasi') {
        if (sosialisasi) {
            sosialisasi.destroy();
        }
        dataSosialisasi();
    } else if (str === 'pembinaan') {
        if (pembinaan) {
            pembinaan.destroy();
        }
        dataPembinaan();
    }
}

function dataSosialisasi() {
    var string = $('#form_sosialisasi').serializeObject();
    sosialisasi = $('#form_sosialisasi #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'apps/sosialisasi/materi/data'
        },
        columns: [
            { data: 'checkbox', className: 'row-checkbox' + (U_DLT ? '' : 'd-none'), width: '40px', orderable: false }, //checkbox
            { data: 'action', className: 'row-action text-center', width: '40px', orderable: false }, //Control
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'name', name: 'name', className: 'text-start' },
        ],
        order: [[3, 'asc']],
    });
}

function dataPembinaan() {
    var string = $('#form_pembinaan').serializeObject();
    pembinaan = $('#form_pembinaan #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'apps/pembinaan/materi/data'
        },
        columns: [
            { data: 'checkbox', className: 'row-checkbox' + (U_DLT ? '' : 'd-none'), width: '40px', orderable: false }, //checkbox
            { data: 'action', className: 'row-action text-center', width: '40px', orderable: false }, //Control
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'name', name: 'name', className: 'text-start' },
        ],
        order: [[3, 'asc']],
    });
}

function completeCallback() {
    var str = string_replace($("#tab_head li a.active").attr("href"), "#", "");
    showData(str);
}
