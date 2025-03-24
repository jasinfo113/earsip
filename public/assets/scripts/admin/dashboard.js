var pegawai, fcm;
$(document).ready(function () {
    setSelect2("form select[data-control=select2]");
    setSelections("#form_pegawai select[name=jenis]", "general/selection", "ref=jenis_pegawai");
    setSelections("#form_pegawai select[name=unit_kerja]", "general/selection", "ref=unit_kerja");
    $("#form_pegawai select[name=unit_kerja]").change(function (event) {
        var _value = $(this).val();
        penempatanItems = setSelections("#form_pegawai select[name=sub_unit_kerja]", "general/selection", (_value ? "ref=unit_kerja_sub&id_unit_kerja=" + _value : null));
    });
    $("form input").keydown(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode === 13) {
            event.preventDefault();
            showData($(this.form).attr('data'));
        }
    });
    dataPegawai();
    if ($("#form_fcm").length) {
        dataFcm();
    }
});

function showData(str) {
    if (str === 'pegawai') {
        if (pegawai) {
            pegawai.destroy();
        }
        dataPegawai();
    } else if (str === 'fcm') {
        if (fcm) {
            fcm.destroy();
        }
        dataFcm();
    }
}

function dataPegawai() {
    var string = $('#form_pegawai').serializeObject();
    pegawai = $('#form_pegawai #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'admin/pegawai/data'
        },
        columns: [
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'nrk', name: 'nrk', className: 'text-start' },
            { data: 'nama', name: 'nama', className: 'text-start' },
            { data: 'jenis', name: 'jenis', className: 'text-start' },
            { data: 'unit_kerja', name: 'unit_kerja', className: 'text-start' },
            { data: 'sub_unit_kerja', name: 'sub_unit_kerja', className: 'text-start' },
            { data: 'status', name: 'status', className: 'text-center' },
        ],
        order: [[1, 'asc']],
    });
}

function dataFcm() {
    var string = $('#form_fcm').serializeObject();
    fcm = $('#form_fcm #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'admin/notif/data'
        },
        columns: [
            { data: 'checkbox', className: 'row-checkbox' + (U_DLT ? '' : 'd-none'), width: '40px', orderable: false }, //checkbox
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'user', name: 'user', className: 'text-start' },
            { data: 'device', name: 'device', className: 'text-start' },
        ],
        order: [[2, 'asc']],
    });
}

function pushNotif() {
    var values = getCheckAll('#form_fcm #table_data');
    if (values.length) {
        dialogConfirm(values, "Send push notif to selected data?", "admin/notif/send", showData);
    } else {
        swalAlert("warning", "Silahkan pilih minimal 1 data!");
    }
}

function exportData() {
    dialogOpenTab("Export data login pegawai?", "admin/pegawai/export", $("#form_pegawai").serialize());
}
