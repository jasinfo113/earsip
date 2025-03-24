var news_pegawai, news_view, news_share;

function showDataNews(str) {
    if (str === 'pegawai') {
        if (news_pegawai) {
            news_pegawai.destroy();
        }
        dataNewsPegawai();
    } else if (str === 'view') {
        if (news_view) {
            news_view.destroy();
        }
        dataNewsView();
    } else if (str === 'share') {
        if (news_share) {
            news_share.destroy();
        }
        dataNewsShare();
    }
}

function dataNewsPegawai() {
    var string = $('#form_news_pegawai').serializeObject();
    news_pegawai = $('#form_news_pegawai #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'cms/news/pegawai'
        },
        columns: [
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'nama', name: 'nama', className: 'text-start', width: '120px' },
            { data: 'jenis', name: 'jenis', className: 'text-start' },
            { data: 'penugasan', name: 'penugasan', className: 'text-start' },
            { data: 'penempatan', name: 'penempatan', className: 'text-start' },
            { data: 'is_view', name: 'is_view', className: 'text-center' },
            { data: 'is_share', name: 'is_share', className: 'text-center' },
        ],
        order: [[5, 'asc']]
    });
}

function dataNewsView() {
    var string = $('#form_news_view').serializeObject();
    news_view = $('#form_news_view #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'cms/news/view'
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

function dataNewsShare() {
    var string = $('#form_news_share').serializeObject();
    news_share = $('#form_news_share #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'cms/news/share'
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
    dialogOpenTab("Export data?", "cms/news/" + str + "/export", $("#form_news_pegawai").serialize());
}