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
        if (fieldValidate("#form_data")) {
            var string = $("#form_data").serialize();
            $.ajax({
                type: "POST",
                url: site_url + "auth/login",
                data: string,
                cache: false,
                dataType: "json",
                beforeSend: function () {
                    var _el = $("#btn_submit");
                    _el.attr("data-kt-indicator", "on");
                    _el.attr("disabled", 1);
                },
                success: function (json) {
                    if (json.status) {
                        Swal.fire({
                            icon: "success",
                            html: json.message,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then((function (e) {
                            if (e.isConfirmed) {
                                if (json.url) {
                                    location.replace(json.url);
                                }
                            }
                        }));
                    } else {
                        Swal.fire({
                            icon: "error",
                            html: json.message,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                        captchaRefresh();
                    }
                },
                complete: function () {
                    var _el = $("#btn_submit");
                    _el.removeAttr("data-kt-indicator");
                    _el.removeAttr("disabled");
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        html: error,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                    captchaRefresh();
                }
            });
        }
        saveFormData("#form_data", "auth/login", null, beforeRequest, afterRequest);
    });
});