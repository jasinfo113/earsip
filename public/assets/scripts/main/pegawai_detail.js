var pegawai_activity;

function showDataPegawai(str) {
    if (str === 'activity') {
        if (pegawai_activity) {
            pegawai_activity.destroy();
        }
        dataPegawaiActivity();
    }
}

function dataPegawaiActivity() {
    var string = $('#form_pegawai_activity').serializeObject();
    pegawai_activity = $('#form_pegawai_activity #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'main/pegawai/activity'
        },
        columns: [
            {data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false}, //number
            {data: 'date', name: 'date', className: 'text-start', width: '120px'},
            {data: 'description', name: 'description', className: 'text-start'},
            {data: 'user_agent', name: 'user_agent', className: 'text-start'},
            {data: 'ip_address', name: 'ip_address', className: 'text-start'},
        ],
        order: [[1, 'asc']]
    });
}