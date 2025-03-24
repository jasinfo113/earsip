var law_pegawai, law_view, law_download;

function showDataLaw(str) {
    if (str === 'pegawai') {
        if (law_pegawai) {
            law_pegawai.destroy();
        }
        dataLawPegawai();
    } else if (str === 'view') {
        if (law_view) {
            law_view.destroy();
        }
        dataLawView();
    } else if (str === 'download') {
        if (law_download) {
            law_download.destroy();
        }
        dataLawDownload();
    }
}

function dataLawPegawai() {
    var string = $('#form_law_pegawai').serializeObject();
    law_pegawai = $('#form_law_pegawai #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'cms/info/law/pegawai'
        },
        columns: [
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'nama', name: 'nama', className: 'text-start', width: '120px' },
            { data: 'jenis', name: 'jenis', className: 'text-start' },
            { data: 'penugasan', name: 'penugasan', className: 'text-start' },
            { data: 'penempatan', name: 'penempatan', className: 'text-start' },
            { data: 'is_view', name: 'is_view', className: 'text-center' },
            { data: 'is_download', name: 'is_download', className: 'text-center' },
        ],
        order: [[5, 'asc']]
    });
}

function dataLawView() {
    var string = $('#form_law_view').serializeObject();
    law_view = $('#form_law_view #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'cms/info/law/view'
        },
        columns: [
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'date', name: 'date', className: 'text-start', width: '120px' },
            { data: 'user', name: 'user', className: 'text-start' },
            { data: 'ip_address', name: 'ip_address', className: 'text-start' },
            { data: 'user_agent', name: 'user_agent', className: 'text-start' },
        ],
        order: [[1, 'asc']]
    });
}

function dataLawDownload() {
    var string = $('#form_law_download').serializeObject();
    law_download = $('#form_law_download #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'cms/info/law/download'
        },
        columns: [
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'date', name: 'date', className: 'text-start', width: '120px' },
            { data: 'user', name: 'user', className: 'text-start' },
            { data: 'ip_address', name: 'ip_address', className: 'text-start' },
            { data: 'user_agent', name: 'user_agent', className: 'text-start' },
        ],
        order: [[1, 'asc']]
    });
}

function exportData(str) {
    dialogOpenTab("Export data?", "cms/info/law/" + str + "/export", $("#form_law_pegawai").serialize());
}