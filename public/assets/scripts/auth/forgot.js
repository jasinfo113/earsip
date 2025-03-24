$(document).ready(function(){
    $("#form_data input").keydown(function (e) {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode === 13) {
            e.preventDefault();
            $($("#btn_submit").trigger("click"));
        }
    });
    $("#form_data #btn_submit").click(function (e) {
        e.preventDefault();
        saveFormData("#form_data", "forgot-password", null, beforeRequest, afterRequest);
    });
});

function beforeRequest() {
    var _el = $("#btn_submit");
    _el.attr("data-kt-indicator", "on");
    _el.attr("disabled", 1);
}

function afterRequest() {
    var _el = $("#btn_submit");
    _el.removeAttr("data-kt-indicator");
    _el.removeAttr("disabled");
}