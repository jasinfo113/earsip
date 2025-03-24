var categori;
$(document).ready(function () {
    //setSelect2("form select[data-control=select2]");
    // setSelections("#form_pegawai select[name=jenis]", "general/selection", "ref=jenis_pegawai");
    // setSelections("#form_pegawai select[name=unit_kerja]", "general/selection", "ref=unit_kerja");
    // $("#form_pegawai select[name=unit_kerja]").change(function (event) {
    //     var _value = $(this).val();
    //     penempatanItems = setSelections("#form_pegawai select[name=sub_unit_kerja]", "general/selection", (_value ? "ref=unit_kerja_sub&id_unit_kerja=" + _value : null));
    // });
    // $("form input").keydown(function (event) {
    //     var keycode = (event.keyCode ? event.keyCode : event.which);
    //     if (keycode === 13) {
    //         event.preventDefault();
    //         showData($(this.form).attr('data'));
    //     }
    // });
    dataCategori();
    // if ($("#form_fcm").length) {
    //     dataFcm();
    // }
});

function showData(str) {
    if (str === 'categori') {
        if (categori) {
            categori.destroy();
        }
        dataCategori();
    }
}

function dataCategori() {
    var string = $('#form_categori').serializeObject();
    categori = $('#form_categori #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'master/categori/data'
        },
        columns: [
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'name', name: 'name', className: 'text-start' },
            { data: 'description', name: 'description', className: 'text-start' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'status', name: 'status', className: 'text-center' },
        ],
        order: [[1, 'asc']],
    });
}



function exportData() {
    dialogOpenTab("Export data login pegawai?", "admin/pegawai/export", $("#form_pegawai").serialize());
}
