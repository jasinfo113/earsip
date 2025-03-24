var roleItems = [], moduleItems = [], refItems = [], penugasanItems = [];
var permissions, roles, references, rules;
$(document).ready(function () {
    $("#tab_head li a").click(function () {
        var str = string_replace($(this).attr('href'), '#', '');
        if (str === 'permissions' && permissions === undefined) {
            dataPermissions();
        } else if (str === 'roles' && roles === undefined) {
            dataRoles();
        } else if (str === 'references' && references === undefined) {
            dataReferences();
        } else if (str === 'rules' && rules === undefined) {
            dataRules();
        }
    });
    $("form input").keydown(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode === 13) {
            event.preventDefault();
            showData($(this.form).attr('data'));
        }
    });
    roleItems = setSelections("#form_permissions select.select-role", "general/selection", "ref=apps_role&status=1");
    refItems = setSelections("#form_references select.select-ref", "general/selection", "ref=references&status=1");
    dataPermissions();
});

function showData(str) {
    if (str === 'permissions') {
        if (permissions) {
            permissions.destroy();
        }
        dataPermissions();
    } else if (str === 'roles') {
        if (roles) {
            roles.destroy();
        }
        dataRoles();
    } else if (str === 'references') {
        if (references) {
            references.destroy();
        }
        dataReferences();
    } else if (str === 'rules') {
        if (rules) {
            rules.destroy();
        }
        dataRules();
    }
}

function dataPermissions() {
    var string = $('#form_permissions').serializeObject();
    permissions = $('#form_permissions #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'apps/rules/permission/data'
        },
        ordering: false,
        columns: [
            { data: 'checkbox', className: 'row-checkbox h-auto' + (U_DLT ? '' : 'd-none'), width: '30px', orderable: false }, //checkbox
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'app', name: 'app', className: 'text-start' },
            { data: 'page', name: 'page', className: 'text-start' },
            { data: 'name', name: 'name', className: 'text-start' },
            { data: 'roles', name: 'roles', className: 'text-start' },
        ],
        order: [[2, 'asc']],
    });
}

function dataRoles() {
    var string = $('#form_roles').serializeObject();
    roles = $('#form_roles #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'apps/rules/role/data'
        },
        ordering: false,
        columns: [
            { data: 'action', className: 'row-action text-center', orderable: false }, //Control
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'name', name: 'name', className: 'text-start' },
            { data: 'description', name: 'description', className: 'text-start' },
            { data: 'penugasan', name: 'penugasan', className: 'text-start' },
            { data: 'status', name: 'status', className: 'text-center', width: '80px' },
        ],
        order: [[2, 'asc']],
    });
}

function dataReferences() {
    var string = $('#form_references').serializeObject();
    references = $('#form_references #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'apps/rules/reference/data'
        },
        columns: [
            { data: 'action', className: 'row-action text-center', orderable: false }, //Control
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'ref', name: 'ref', className: 'text-start' },
            { data: 'name', name: 'name', className: 'text-start' },
            { data: 'description', name: 'description', className: 'text-start' },
            { data: 'label', name: 'label', className: 'text-start' },
            { data: 'status', name: 'status', className: 'text-center', width: '80px' },
        ],
        order: [[2, 'asc']],
    });
}

function dataRules() {
    var string = $('#form_rules').serializeObject();
    rules = $('#form_rules #table_data').DataTable({
        dom: '<"top"i>rt<"bottom"lp><"clear">',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            type: 'POST',
            data: string,
            cache: false,
            url: site_url + 'apps/rules/data'
        },
        ordering: false,
        columns: [
            { data: 'DT_RowIndex', name: 'no', className: 'text-start hide-hp', width: '40px', orderable: false }, //number
            { data: 'name', name: 'name', className: 'text-start' },
            { data: 'description', name: 'description', className: 'text-start' },
            { data: 'value', name: 'value', className: 'text-start', width: '160px' },
            { data: 'status', name: 'status', className: 'text-center', width: '80px' },
        ],
        order: [[2, 'asc']],
    });
}

function updateRulesValue(e) {
    var _id = $(e).attr("data-id");
    var _value = "";
    if ($("select.rules-" + _id).length) {
        _value = $("select.rules-" + _id).val();
    } else {
        _value = $("input.rules-" + _id).val();
    }
    if (_id > 0 && _value !== "") {
        var string = "id=" + _id + "&value=" + _value;
        saveData("apps/rules/save", string);
    }
}

function delPermission() {
    var values = getCheckAll("#form_permissions #table_data");
    if (values.length) {
        var string = values.join(",");
        if ($('#form_permissions select[id=filter_role]').val()) {
            string += "&role_ids=" + $('#form_permissions select[id=filter_role]').val();
        }
        dialogConfirm(string, "Hapus data terpilih?", "apps/rules/permission/del", delCallback);
    } else {
        swalAlert("warning", "Silahkan pilih minimal 1 data!");
    }
}

function delCallback() {
    showData('permissions');
}
