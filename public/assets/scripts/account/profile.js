$(document).ready(function () {
    var _pass = KTPasswordMeter.getInstance(document.querySelector('[data-kt-password-meter="true"]'));
    var _passMin = 80;

    $("#form_profile input").keydown(function (e) {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode === 13) {
            e.preventDefault();
            $("#form_profile .btn-submit").trigger("click");
        }
    });
    $("#form_profile").submit(function(e) {
        e.preventDefault();
        submitData(this, "account/update", locationReload);
    });

    $("#form_password .btn-submit").click(function (e) {
        e.preventDefault();
        saveFormData("#form_password", "account/password", locationReload);
    });
    $("#form_password input").keydown(function (e) {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode === 13) {
            e.preventDefault();
            if (_pass.getScore() >= _passMin) {
                $("#form_password .btn-submit").trigger("click");
            }
        }
    });

    $("#form_deactivate .btn-submit").click(function (e) {
        e.preventDefault();
        if (fieldValidate("#form_deactivate")) {
            dialogPassword("account/deactivate");
        }
    });
    $("#form_deactivate input").keydown(function (e) {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode === 13) {
            e.preventDefault();
            $("#form_deactivate .btn-submit").trigger("click");
        }
    });
});