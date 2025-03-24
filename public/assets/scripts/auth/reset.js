$(document).ready(function(){
    var _pass = KTPasswordMeter.getInstance(document.querySelector('[data-kt-password-meter="true"]'));
    var _passMin = 80;
    $("#form_data input[name=password]").keyup(function (e) {
        if (this.value.length > 0) {
            if (_pass.getScore() >= _passMin) {
                $("#btn_submit").removeAttr("disabled");
            } else {
                $("#btn_submit").attr("disabled", 1);
            }
        }
    });
    $("#form_data input").keydown(function (e) {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode === 13) {
            e.preventDefault();
            if (_pass.getScore() >= _passMin) {
                $("#btn_submit").trigger("click");
            }
        }
    });
    $("#form_data #btn_submit").click(function (e) {
        e.preventDefault();
        saveFormData("#form_data", "reset-password", null, beforeRequest, afterRequest);
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