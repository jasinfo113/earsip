var clipboard;
var REQUIRED_MESSAGE = "This field is required!";

var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
var shortcut = {
    all_shortcuts: {},
    add: function (a, b, c) {
        var d = {
            type: "keydown", propagate: !1, disable_in_input: !1, target: document, keycode: !1
        };
        if (c) for (var e in d) "undefined" == typeof c[e] && (c[e] = d[e]);
        else c = d;
        d = c.target, "string" == typeof c.target && (d = document.getElementById(c.target)), a = a.toLowerCase(), e = function (d) {
            d = d || window.event;
            if (c.disable_in_input) {
                var e;
                d.target ? e = d.target : d.srcElement && (e = d.srcElement), 3 == e.nodeType && (e = e.parentNode);
                if ("INPUT" == e.tagName || "TEXTAREA" == e.tagName) return
            }
            d.keyCode ? code = d.keyCode : d.which && (code = d.which), e = String.fromCharCode(code).toLowerCase(), 188 == code && (e = ","), 190 == code && (e = ".");
            var f = a.split("+"), g = 0, h = {
                    "`": "~",
                    1: "!",
                    2: "@",
                    3: "#",
                    4: "$",
                    5: "%",
                    6: "^",
                    7: "&",
                    8: "*",
                    9: "(",
                    0: ")",
                    "-": "_",
                    "=": "+",
                    ";": ":",
                    "'": '"',
                    ",": "<",
                    ".": ">",
                    "/": "?",
                    "\\": "|"
                },
                i = {
                    esc: 27,
                    escape: 27,
                    tab: 9,
                    space: 32,
                    "return": 13,
                    enter: 13,
                    backspace: 8,
                    scrolllock: 145,
                    scroll_lock: 145,
                    scroll: 145,
                    capslock: 20,
                    caps_lock: 20,
                    caps: 20,
                    numlock: 144,
                    num_lock: 144,
                    num: 144,
                    pause: 19,
                    "break": 19,
                    insert: 45,
                    home: 36,
                    "delete": 46,
                    end: 35,
                    pageup: 33,
                    page_up: 33,
                    pu: 33,
                    pagedown: 34,
                    page_down: 34,
                    pd: 34,
                    left: 37,
                    up: 38,
                    right: 39,
                    down: 40,
                    f1: 112,
                    f2: 113,
                    f3: 114,
                    f4: 115,
                    f5: 116,
                    f6: 117,
                    f7: 118,
                    f8: 119,
                    f9: 120,
                    f10: 121,
                    f11: 122,
                    f12: 123
                },
                j = !1, l = !1, m = !1, n = !1, o = !1, p = !1, q = !1, r = !1;
            d.ctrlKey && (n = !0), d.shiftKey && (l = !0), d.altKey && (p = !0), d.metaKey && (r = !0);
            for (var s = 0; k = f[s], s < f.length; s++) "ctrl" == k || "control" == k ? (g++, m = !0) : "shift" == k ? (g++, j = !0) : "alt" == k ? (g++, o = !0) : "meta" == k ? (g++, q = !0) : 1 < k.length ? i[k] == code && g++ : c.keycode ? c.keycode == code && g++ : e == k ? g++ : h[e] && d.shiftKey && (e = h[e], e == k && g++);
            if (g == f.length && n == m && l == j && p == o && r == q && (b(d), !c.propagate)) return d.cancelBubble = !0, d.returnValue = !1, d.stopPropagation && (d.stopPropagation(), d.preventDefault()), !1
        },
            this.all_shortcuts[a] = {
                callback: e, target: d, event: c.type
            },
            d.addEventListener ? d.addEventListener(c.type, e, !1) : d.attachEvent ? d.attachEvent("on" + c.type, e) : d["on" + c.type] = e
    },
    remove: function (a) {
        var a = a.toLowerCase(), b = this.all_shortcuts[a];
        delete this.all_shortcuts[a];
        if (b) {
            var a = b.event, c = b.target, b = b.callback;
            c.detachEvent ? c.detachEvent("on" + a, b) : c.removeEventListener ? c.removeEventListener(a, b, !1) : c["on" + a] = !1
        }
    }
};

$.fn.serializeObject = function () {
    var self = this,
        json = {},
        push_counters = {},
        patterns = {
            "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
            "key": /[a-zA-Z0-9_]+|(?=\[\])/g,
            "push": /^$/,
            "fixed": /^\d+$/,
            "named": /^[a-zA-Z0-9_]+$/
        };
    this.build = function (base, key, value) {
        base[key] = value;
        return base;
    };
    this.push_counter = function (key) {
        if (push_counters[key] === undefined) {
            push_counters[key] = 0;
        }
        return push_counters[key]++;
    };
    $.each($(this).serializeArray(), function () {
        if (!patterns.validate.test(this.name)) {
            return;
        }
        var k,
            keys = this.name.match(patterns.key),
            merge = this.value,
            reverse_key = this.name;
        while ((k = keys.pop()) !== undefined) {
            reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');
            if (k.match(patterns.push)) {
                merge = self.build([], self.push_counter(reverse_key), merge);
            } else if (k.match(patterns.fixed)) {
                merge = self.build([], k, merge);
            } else if (k.match(patterns.named)) {
                merge = self.build({}, k, merge);
            }
        }
        json = $.extend(true, json, merge);
    });
    return json;
};

jQuery.validator.setDefaults({
    ignore: ".ignore, .optional, .select2-search__field, :hidden, .note-editor *, [contenteditable='true']:not([name])",
    errorPlacement: function (error, element) {
        if (element.hasClass('select2') && element.next('.select2-container').length) {
            error.insertAfter(element.next('.select2-container'));
        } else {
            // error.insertAfter(element.prev());
            error.appendTo(element.parent());
        }
    }
});
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
});

$(document).on("show.bs.modal", '.modal', function (event) {
    var zIndex = 999 + (10 * $(".modal:visible").length);
    $(this).css("z-index", zIndex);
    setTimeout(function () {
        $(".modal-backdrop").not(".modal-stack").first().css("z-index", zIndex - 1).addClass("modal-stack");
    }, 0);
}).on("hidden.bs.modal", '.modal', function (event) {
    $(".modal:visible").length && $("body").addClass("modal-open");
});
$(document).on('inserted.bs.tooltip', function (event) {
    var zIndex = 999 + (10 * $(".modal:visible").length);
    var tooltipId = $(event.target).attr("aria-describedby");
    $("#" + tooltipId).css("z-index", zIndex);
});
$(document).on('inserted.bs.popover', function (event) {
    var zIndex = 999 + (10 * $(".modal:visible").length);
    var popoverId = $(event.target).attr("aria-describedby");
    $("#" + popoverId).css("z-index", zIndex);
});
$(document).ready(function() {
    if ($(".password-visibility").length) {
        $(".password-visibility .btn-icon").click(function(e){
            passwordVisibility(".password-visibility");
        });
    }
});

function toastSuccess(str) {
    if (str) {
        //toastr.clear();
        toastr.success(str);
    }
}

function toastError(str) {
    if (str) {
        //toastr.clear();
        toastr.error(str);
    }
}

function loading_open() {
    $("#loading").show();
}

function loading_close() {
    $("#loading").hide();
}

function loaderOpen(_el) {
    mApp.block(_el, {
        overlayColor: "#000000",
        type: "loader",
        state: "primary",
        message: "Please wait..."
    });
}

function loaderClose(_el) {
    setTimeout(function () {
        mApp.unblock(_el)
    }, 500);
}

function closeModal() {
    $('body').removeClass('modal-open');
    $('#form_dialog').html('').removeClass('show').attr("aria-hidden", "true").hide();
    $('.modal-backdrop').remove();
}

function closeModalDetail() {
    $('#form_dialog_detail').html('').removeClass('show').attr("aria-hidden", "true").hide();
}

function docBlur() {
    window.blur();
    document.activeElement.blur();
}

function setNumber(_el) {
    $(_el + ' :input[type=number]').on('mousewheel', function (e) {
        e.preventDefault();
    });
}

function setSelect2(_el) {
    if ($(_el).length > 1) {
        $.each($(_el), function () {
            $(this).select2({
                width: "100%",
                placeholder: ($(this).data("placeholder") ? $(this).data("placeholder") : "Choose an option"),
                allowClear: true
            });
        });
    } else if ($(_el).length > 0) {
        $(_el).select2({
            width: "100%",
            placeholder: ($(_el).data("placeholder") ? $(_el).data("placeholder") : "Choose an option"),
            allowClear: true
        });
    }
}

function setSelect2Multiple(_el) {
    if ($(_el).length > 1) {
        $.each($(_el), function () {
            $(this).select2({
                width: "100%",
                placeholder: ($(this).data("placeholder") ? $(this).data("placeholder") : "Choose multiple option"),
                closeOnSelect: false
            });
        });
    } else if ($(_el).length > 0) {
        $(_el).select2({
            width: "100%",
            placeholder: ($(_el).data("placeholder") ? $(_el).data("placeholder") : "Choose multiple option"),
            closeOnSelect: false
        });
    }
}

function setDatePicker(_el, _start = null, _end = null, _view = null) {
    $(_el).datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        startDate: (_start ? _start : '-3m'),
        endDate: (_end ? _end : '+3m'),
        startView: (_view ? _view : 'days')
    });
}

function setDateRangePicker(_el, _time = false, _start = null, _end = null) {
    if ($(_el).length) {
        $(_el).daterangepicker({
            autoUpdateInput: false,
            timePicker: _time,
            timePicker24Hour: _time,
            startDate: (_start ? _start : moment().startOf('day')),
            endDate: (_end ? _end : moment().endOf('day')),
            locale:
                {
                    format: (_time ? 'YYYY-MM-DD HH:mm' : 'YYYY-MM-DD')
                },
            ranges:
                {
                    'Today': [moment().startOf('day'), moment().endOf('day')],
                    'Yesterday': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
                    'Last 7 Days': [moment().subtract(6, 'days').startOf('day'), moment().endOf('day')],
                    'Last 30 Days': [moment().subtract(29, 'days').startOf('day'), moment().endOf('day')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
        });
        $(_el).on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format((_time ? 'YYYY-MM-DD HH:mm' : 'YYYY-MM-DD')) + ' - ' + picker.endDate.format((_time ? 'YYYY-MM-DD HH:mm' : 'YYYY-MM-DD')));
            $(this).trigger('change');
        });
        $(_el).on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
            $(this).trigger('change');
        });
    }
}

function setDatetimeRangePicker(_el) {
    $(_el).daterangepicker({
        autoUpdateInput: false,
        locale:
            {
                format: "YYYY-MM-DD HH:mm"
            },
        timePicker: true,
        timePicker24Hour: true,
    });
    $(_el).on("apply.daterangepicker", function (ev, picker) {
        $(this).val(picker.startDate.format("YYYY-MM-DD HH:mm") + " - " + picker.endDate.format("YYYY-MM-DD HH:mm"));
        $(this).trigger("change");
    });
    $(_el).on("cancel.daterangepicker", function (ev, picker) {
        $(this).val("");
        $(this).trigger("change");
    });
}

function setPopupImage(_el, _gallery = false) {
    if ($(_el).length > 0) {
        $(_el).magnificPopup({
            type: "image",
            gallery: {
                enabled: _gallery
            },
            mainClass: "mfp-fade"
        });
    }
}

function setPopupVideo(_el) {
    if ($(_el).length > 0) {
        $(_el).magnificPopup({
            type: "iframe",
            mainClass: "mfp-fade",
            removalDelay: 160,
            preloader: false,
            fixedContentPos: true,
            iframe: {
                markup: '<div class="mfp-iframe-scaler">' +
                    '<div class="mfp-close"></div>' +
                    '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
                    '</div>',
                srcAction: "iframe_src",
            }
        });
    }
}

function setSwitch(_el, _timeout = 500) {
    if ($(_el).length > 0) {
        setTimeout(function () {
            $(_el).bootstrapSwitch();
        }, _timeout);
    }
}

function setToolTip(_el, _timeout = 500) {
    if ($(_el).length > 0) {
        setTimeout(function () {
            $(_el).tooltip({
                template:'<div class="m-tooltip tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>',
            });
        }, _timeout);
    }
}

function copyToBoard(str) {
    if ($(str).length) {
        if (clipboard) {
            clipboard.destroy();
        }
        clipboard = new ClipboardJS(str);
        clipboard.on('success', function (e) {
            console.log(e);
            toastSuccess('Text copied!');
            e.clearSelection();
        });
        clipboard.on('error', function (e) {
            toastError('Copy error');
        });
    }
}

function setSummerNote(_el, _height) {
    $(_el).summernote({
        dialogsInBody: true,
        height: _height,
        toolbar:
            [
                ['style', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['image', 'link', 'video', 'hr']],
                ['view', ['fullscreen', 'codeview']]
            ],
        popover: {
            image: [
                ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                ['float', ['floatLeft', 'floatRight', 'floatNone']],
                ['remove', ['removeMedia']]
            ],
        },
        buttons: {
            image: function () {
                var ui = $.summernote.ui;

                // create button
                var button = ui.button({
                    contents: '<i class="note-icon-picture" />',
                    click: function () {
                        $('#modal-image').remove();

                        $.ajax({
                            url: site_url + 'common/filemanager',
                            dataType: 'html',
                            beforeSend: function () {
                                $('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
                                $('#button-image').prop('disabled', true);
                            },
                            complete: function () {
                                $('#button-image i').replaceWith('<i class="fa fa-upload"></i>');
                                $('#button-image').prop('disabled', false);
                            },
                            success: function (html) {
                                $('body').append('<div id="modal-image" class="modal">' + html + '</div>');

                                $('#modal-image').modal('show');

                                $('#modal-image').delegate('a.thumbnail', 'click', function (e) {
                                    e.preventDefault();

                                    $(_el).summernote('insertImage', $(this).attr('href'));

                                    $('#modal-image').modal('hide');
                                });
                            }
                        });
                    }
                });

                return button.render();
            }
        }
    });
}

function open_form(str, _params = false) {
    var string = '';
    if (_params) {
        string = _params;
    }
    $.ajax({
        type: 'POST',
        url: site_url + str,
        data: string,
        cache: false,
        beforeSend: function () {
            loading_open();
        },
        success: function (data) {
            docBlur();
            $('body').addClass('modal-open');
            $("#form_dialog").html(data).show();
        },
        complete: function () {
            loading_close();
        }
    });
}

function form_update(str, id) {
    var string = 'id=' + id;
    $.ajax({
        type: 'POST',
        url: site_url + str,
        data: string,
        cache: false,
        beforeSend: function () {
            loading_open();
        },
        success: function (data) {
            docBlur();
            $('body').addClass('modal-open');
            $("#form_dialog").html(data).show();
        },
        complete: function () {
            loading_close();
        }
    });
}

function form_dialog_detail(str, id) {
    var string = 'id=' + id;
    $.ajax({
        type: 'POST',
        url: site_url + str,
        data: string,
        cache: false,
        beforeSend: function () {
            loading_open();
        },
        success: function (data) {
            docBlur();
            $("#form_dialog_detail").html(data).show();
        },
        complete: function () {
            loading_close();
        }
    });
}

function open_dialog(_get, _store, _form, _title, id) {
    console.log(_get, _store, _form, _title, id);
    var string = "";
    if (id) {
        string = 'id=' + id;
    }
    $.ajax({
        type: "POST",
        url: site_url + _get,
        data: string,
        cache: false,
        beforeSend: function () {
            loading_open();
        },
        success: function (data) {
            swal({
                    title: _title,
                    text: data,
                    html: true,
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    cancelButtonText: "No",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                },
                function () {
                    var proceed = saveFormData(_store, _form);
                    if (proceed) {
                        if (proceed.status == 'success') {
                            swal.close();
                            closeModal();
                            toastSuccess(proceed.message);
                        } else {
                            console.log(proceed);
                            toastError(proceed.message);
                        }
                    }
                });
        },
        complete: function () {
            loading_close();
        }
    });
}

function fieldValidate(_form) {
    return $(_form).valid();
}

function setSelectJson(t, k, v, b, c, s, r) {
    if (t != '' && s != '') {
        var string = 'table=' + t;
        if (k != '' && v != '' && k != null && v != null) {
            string += '&key=' + k + '&value=' + v;
        }
        if (b != '' && c != '' && b != null && c != null) {
            string += '&by=' + b + '&case=' + c;
        }
        $.ajax({
            type: 'POST',
            url: site_url + "common/getDataJson",
            data: string,
            cache: false,
            dataType: 'json',
            beforeSend: function () {
                loading_open();
            },
            success: function (json) {
                $(s).val('').trigger('change');
                $(s).find('option').remove().end().append('<option value=""></option>');
                $(json).each(function (index, i) {
                    var option = $("<option/>").attr("value", i.id).text(i.name);
                    $(s).append(option);
                });
                if (r != undefined) {
                    $(s).val(r).trigger('change');
                }
            },
            complete: function () {
                loading_close();
            }
        });
    } else {
        $(s).val('').trigger('change');
        $(s).find('option').remove().end().append('<option value=""></option>');
    }
}

function setSelectRequiredJson(t, k, v, b, c, s, r) {
    if (t != '' && k != '' && v != '' && s != '') {
        var string = 'table=' + t + '&key=' + k + '&value=' + v;
        if (b != '' && c != '' && b != null && c != null) {
            string += '&by=' + b + '&case=' + c;
        }
        $.ajax({
            type: 'POST',
            url: site_url + "common/getDataJson",
            data: string,
            cache: false,
            dataType: 'json',
            beforeSend: function () {
                loading_open();
            },
            success: function (json) {
                $(s).val('').trigger('change');
                $(s).find('option').remove().end().append('<option value=""></option>');
                $(json).each(function (index, i) {
                    var option = $("<option/>").attr("value", i.id).text(i.name);
                    $(s).append(option);
                });
                if (r != undefined) {
                    $(s).val(r).trigger('change');
                }
            },
            complete: function () {
                loading_close();
            }
        });
    } else {
        $(s).val('').trigger('change');
        $(s).find('option').remove().end().append('<option value=""></option>');
    }
}

function listPagination(_start = 0, _url, _form, _el) {
    var string = $(_form).serialize() + "&start=" + _start;
    $.ajax({
        type: 'POST',
        url: site_url + _url,
        data: string,
        cache: false,
        beforeSend: function () {
            loading_open();
        },
        success: function (data) {
            $(_el).html(data);
        },
        complete: function () {
            loading_close();
        }
    });
}

function addCommas(nStr) {
    if (nStr) {
        nStr = parseInt(nStr);
        nStr += '';
        x = nStr.split(',');
        x1 = x[0];
        x2 = x.length > 1 ? ',' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    } else {
        if (isNaN(parseFloat(nStr))) {
            return '';
        } else {
            return nStr;
        }
    }
}

function convertDate(str) {
    var date = new Date(str);
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear();
    if (day < 10) {
        day = '0' + day;
    }
    if (month < 10) {
        month = '0' + month;
    }
    return year + '-' + month + '-' + day;
}

function convertDatetime(str) {
    var date = new Date(str);
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear();
    var hours = date.getHours();
    var minute = date.getMinutes();
    var second = date.getSeconds();
    if (day < 10) {
        day = '0' + day;
    }
    if (month < 10) {
        month = '0' + month;
    }
    if (hours < 10) {
        hours = '0' + hours;
    }
    if (minute < 10) {
        minute = '0' + minute;
    }
    if (second < 10) {
        second = '0' + second;
    }
    return year + '-' + month + '-' + day + ' ' + hours + ':' + minute + ':' + second;
}

function dateFormat(str) {
    var date = new Date(str);
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear();
    if (day < 10) {
        day = '0' + day;
    }
    if (month < 10) {
        month = '0' + month;
    }
    return day + '/' + month + '/' + year;
}

function datetimeFormat(str) {
    var date = new Date(str);
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear();
    var hours = date.getHours();
    var minute = date.getMinutes();
    var second = date.getSeconds();
    if (day < 10) {
        day = '0' + day;
    }
    if (month < 10) {
        month = '0' + month;
    }
    if (hours < 10) {
        hours = '0' + hours;
    }
    if (minute < 10) {
        minute = '0' + minute;
    }
    if (second < 10) {
        second = '0' + second;
    }
    return day + '/' + month + '/' + year + ' ' + hours + ':' + minute + ':' + second;
}

function addDay(str, day) {
    var date = new Date(str);
    var newdate = new Date(date);
    newdate.setDate(newdate.getDate() + parseInt(day));
    var dd = newdate.getDate();
    var mm = newdate.getMonth() + 1;
    var yyyy = newdate.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    return yyyy + '-' + mm + '-' + dd;
}

function subDay(str, day) {
    var date = new Date(str);
    var newdate = new Date(date);
    newdate.setDate(newdate.getDate() - parseInt(day));
    var dd = newdate.getDate();
    var mm = newdate.getMonth() + 1;
    var yyyy = newdate.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    return yyyy + '-' + mm + '-' + dd;
}

function addMonth(str, month) {
    var date = new Date(str);
    var newdate = new Date(date.setMonth(date.getMonth() + month));
    var dd = newdate.getDate();
    var mm = newdate.getMonth() + 1;
    var yyyy = newdate.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    return yyyy + '-' + mm + '-' + dd;
}

function subMonth(str, month) {
    var date = new Date(str);
    var newdate = new Date(date.setMonth(date.getMonth() - month));
    var dd = newdate.getDate();
    var mm = newdate.getMonth() + 1;
    var yyyy = newdate.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    return yyyy + '-' + mm + '-' + dd;
}

function convert_currency(e) {
    var _form = $(e.form).attr('id');
    var str = e.value;
    var minus = '';
    if (str && str.substr(0, 1) === '-') {
        minus = '-';
    }
    var re = /\D/g; /* /,/g */
    var result = str.replace(re, "");
    result = parseInt(result);
    if (result >= 0) {
        result = parseInt(result);
    } else {
        result = '';
    }
    $('#' + _form + ' input[name=' + e.id + ']').val(minus + '' + result);
    $(e).val(minus + '' + addCommas(parseInt(result)));
}

function cek_length(str, val) {
    var string = $('textarea[name=' + str + ']').val();
    var jml = string.length;
    if (jml > parseInt(val)) {
        $('textarea[name=' + str + ']').val(string.substr(0, parseInt(val)));
    }
}

function removeSpace(e) {
    if (e.value) {
        return $(e).val(string_replace(e.value, ' ', ''));
    }
}

function removeExceptNumber(e) {
    if (e.value) {
        var str = e.value.replace(/\D+/g, '');
        return $(e).val(str);
    }
}

function string_replace(str, r, j) {
    if (str.split(r).join(j)) {
        return str.split(r).join(j);
    } else {
        return str;
    }
}

function capitalize(str) {
    return str.replace(/\w\S*/g, function (txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}

function uniqid() {
    return new Date().valueOf();
}

function currency(e) {
    var str = e.value;
    var re = /\D/g; /* /,/g */
    var result = str.replace(re, "");
    result = (result >= 0 ? parseInt(result) : '');
    $(e).val(addCommas(result));
}

function discount(e) {
    if (e.value) {
        var _min = parseFloat($(this).attr("min"));
        var _max = parseFloat($(this).attr("max"));
        if (e.value < _min) {
            $(e).val(_min);
        } else if (e.value > _max) {
            $(e).val(_max);
        }
    }
}

function number(str) {
    if (str) {
        return Number.parseFloat(str).toLocaleString("en-US", {maximumFractionDigits: 2});
    } else {
        if (isNaN(parseFloat(str))) {
            return '';
        } else {
            return str;
        }
    }
}

function generateCode() {
    var firstPart = (Math.random() * 46656) | 0;
    var secondPart = (Math.random() * 46656) | 0;
    firstPart = ("000" + firstPart.toString(36)).slice(-3);
    secondPart = ("000" + secondPart.toString(36)).slice(-3);
    return firstPart + secondPart;
}

function makeId(length) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

function inArray(str, array) {
    if (array.length) {
        for (var i = 0; i < array.length; i++) {
            if (array[i] == str) return true;
        }
    }
    return false;
}

function setForm(item, linkUrl, _params = false) {
    var _return = "";
    if (item && linkUrl) {
        var string = "item=" + item;
        if (_params) {
            string += "&" + _params;
        }
        $.ajax({
            type: "POST",
            url: site_url + linkUrl,
            data: string,
            cache: false,
            async: false,
            beforeSend: function () {
                loading_open();
            },
            success: function (data) {
                _return = data;
            },
            complete: function () {
                loading_close();
            }
        });
    }
    return _return;
}

function addFieldEmail(_name = "", _email = "") {
    var tbl = document.getElementById("table_email");
    var item = 'email_' + uniqid();
    var row = tbl.insertRow(tbl.rows.length);
    row.id = item;
    row.className = 'detail-email';
    var inputData = setForm(item, 'common/form_email');
    var cell = row.insertCell(0);
    cell.className = 'left';
    cell.innerHTML = inputData;
    cell = row.insertCell(1);
    cell.style.verticalAlign = 'top';
    cell.innerHTML = '<button type="button" class="btn btn-danger" data="' + item + '" onclick="removeFieldItem(this)" title="Remove Item">X</button>';

    $('tr#' + item + ' .email-name').val(_name);
    $('tr#' + item + ' .email-email').val(_email);
}

function addFieldPhone(_name = "", _phone = "") {
    var tbl = document.getElementById("table_phone");
    var item = 'phone_' + uniqid();
    var row = tbl.insertRow(tbl.rows.length);
    row.id = item;
    row.className = 'detail-phone';
    var inputData = setForm(item, 'common/form_phone');
    var cell = row.insertCell(0);
    cell.className = 'left';
    cell.innerHTML = inputData;
    cell = row.insertCell(1);
    cell.style.verticalAlign = 'top';
    cell.innerHTML = '<button type="button" class="btn btn-danger" data="' + item + '" onclick="removeFieldItem(this)" title="Remove Item">X</button>';

    setNumber('tr#' + item);
    $('tr#' + item + ' .phone-name').val(_name);
    $('tr#' + item + ' .phone-phone').val(_phone);
}

function addFieldFile() {
    var tbl = document.getElementById("table_file");
    var item = "file_" + uniqid();
    var row = tbl.insertRow(tbl.rows.length);
    row.id = item;
    row.className = "detail-file";
    var inputData = setForm(item, 'common/form_file');
    var cell = row.insertCell(0);
    cell.className = "left";
    cell.innerHTML = inputData;
    cell = row.insertCell(1);
    cell.style.verticalAlign = "top";
    cell.innerHTML = '<button type="button" class="btn btn-danger" data="' + item + '" onclick="removeFieldItem(this)" title="Remove Item">X</button>';
}

function addFieldImage() {
    var tbl = document.getElementById("table_image");
    var item = "image_" + uniqid();
    var row = tbl.insertRow(tbl.rows.length);
    row.id = item;
    row.className = "detail-image";
    var inputData = setForm(item, 'common/form_image');
    var cell = row.insertCell(0);
    cell.className = "left";
    cell.innerHTML = inputData;
    cell = row.insertCell(1);
    cell.style.verticalAlign = "top";
    cell.innerHTML = '<button type="button" class="btn btn-danger" data="' + item + '" onclick="removeFieldItem(this)" title="Remove Item">X</button>';
    setNumber("tr#" + item);
    $("tr#" + item + " input.image-sort").val(tbl.rows.length - 1);
}

function removeDetail(e, str, linkUrl) {
    if (str && linkUrl) {
        swal({
                title: "Confirmation",
                text: "Delete Data ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                cancelButtonText: "No",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            },
            function () {
                var string = "id=" + str;
                var proceed = saveData(linkUrl, string);
                if (proceed) {
                    swal.close();
                    if (proceed.status === "success") {
                        toastSuccess(proceed.message);
                        removeFieldItem(e);
                    } else {
                        toastError(proceed.message);
                    }
                }
            });
    }
}

function removeFieldItem(e) {
    $("tr#" + $(e).attr("data")).remove();
}

function callbackData(_form, _table = false) {
    if (_form) {
        setPopupImage(_form + " .popup-image");
        setPopupImage(_form + " .popup-image-gallery", true);
        setPopupVideo(_form + " .popup-video");
        setSwitch(_form + " .js-switch");
        setToolTip(_form + ' [data-toggle="m-tooltip"]');
        copyToBoard(_form + " .copy-to-clipboard");
        if (_table && $(_form + " input[name=checkAll]").length) {
            $(_form + " input[name=checkAll]").prop("checked", false).val(0);
            _table.rows().deselect();
        }
    }
}

function checkedAll(_form, _table, str = 0) {
    if (_form && _table) {
        if (str > 0 && $(_form + " input[name=checkAll]").length) {
            $(_form + " input[name=checkAll]").val(0);
            _table.rows().deselect();
        } else if ($(_form + " input[name=checkAll]").length) {
            $(_form + " input[name=checkAll]").val(1);
            _table.rows().select();
        }
    }
}

function getCheckAll(_el, _index = 1) {
    var row = [];
    if ($(_el + " tr.selected").length) {
        $.each($(_el + " tr.selected"), function () {
            row.push($(this).find('td').eq(_index).text());
        });
    }
    return row;
}

function changeCheckboxOption(e, _el, _required = true, _reverse = false) {
    var show;
    if (_reverse) {
        show = ($(e).is(":checked") ? false : true);
    } else {
        show = ($(e).is(":checked") ? true : false);
    }
    if (show) {
        if ($(_el).hasClass('m--hide')) {
            $(_el).removeClass('m--hide');
        }
        if (_required) {
            if ($(_el + ' input:not(.optional)').length) {
                $(_el + ' input:not(.optional)').attr('required', true);
            }
            if ($(_el + ' select:not(.optional)').length) {
                $(_el + ' select:not(.optional)').attr('required', true);
            }
            if ($(_el + ' textarea:not(.optional)').length) {
                $(_el + ' textarea:not(.optional)').attr('required', true);
            }
        }
    } else {
        if (!$(_el).hasClass('m--hide')) {
            $(_el).addClass('m--hide');
        }
        if (_required) {
            if ($(_el + ' input:not(.optional)').length) {
                $(_el + ' input:not(.optional)').removeAttr('required');
            }
            if ($(_el + ' select:not(.optional)').length) {
                $(_el + ' select:not(.optional)').removeAttr('required');
            }
            if ($(_el + ' textarea:not(.optional)').length) {
                $(_el + ' textarea:not(.optional)').removeAttr('required');
            }
        }
    }
}

function setItems(_el, _items, _selected = false) {
    if ($(_el).length) {
        var _label, _option;
        $(_items).each(function (index, row) {
            if (typeof row.subs != 'undefined' && Array.isArray(row.subs) && row.subs.length) {
                _label = $("<optgroup/>").attr("label", row.name);
                $(row.subs).each(function (i, r) {
                    if (typeof r.subs != 'undefined' && Array.isArray(r.subs) && r.subs.length) {
                        _option = $("<option/>").attr("value", r.id).attr("disabled", 1).text(r.name);
                        _label.append(_option);
                        $(r.subs).each(function (is, rs) {
                            _option = $("<option/>").attr("value", rs.id).html('&nbsp;&nbsp;&nbsp;' + rs.name);
                            _label.append(_option);
                        });
                    } else {
                        _option = $("<option/>").attr("value", r.id).text(r.name);
                        _label.append(_option);
                    }
                });
                $(_el).append(_label);
            } else {
                _option = $("<option/>").attr("value", row.id);
                _option.text(row.name);
                $(_el).append(_option);
            }
        });
        if (_selected) {
            $(_el).val(_selected).trigger("change");
        }
    }
}

function dialogConfirm(_value, _title, _url, _callback = false) {
    if (_value && _title && _url) {
        swal({
                title: "Confirmation",
                text: _title,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                cancelButtonText: "No",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    var string = 'id=' + _value;
                    $.ajax({
                        type: 'POST',
                        url: site_url + _url,
                        data: string,
                        cache: false,
                        dataType: 'json',
                        beforeSend: function () {
                            swal({
                                title: "Processing Request",
                                text: "Please Wait . . .",
                                type: "info",
                                showCancelButton: false,
                                showConfirmButton: false
                            });
                        },
                        success: function (json) {
                            if (json.status === 'success') {
                                swal(
                                    'Success',
                                    json.message,
                                    'success'
                                );
                                if (_callback) {
                                    _callback();
                                }
                            } else {
                                swal(
                                    'Error',
                                    json.message,
                                    'error'
                                );
                            }
                        },
                        error: function (xhr, status, error) {
                            swal(
                                'Error',
                                error,
                                'error'
                            );
                        }
                    });
                }
            });
    } else {
        swal(
            'Warning',
            'Invalid request!',
            'warning',
        );
    }
}

function dialogNote(_value, _title, _url, _callback = false, _validate = true) {
    if (_value && _title && _url) {
        var _html = '<div class="form-group"><textarea class="form-control" id="swal_txt" rows="6" placeholder="Note" style="display:block;"></textarea><div class="text-left help-block text-danger" id="span_swal_txt"></div></div>';
        swal({
                title: _title,
                text: _html,
                html: true,
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                cancelButtonText: "No",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    var txt = $('#swal_txt').val();
                    if (_validate && txt == "") {
                        $("#span_swal_txt").html("This field is required").show();
                        return false;
                    }
                    var string = 'id=' + _value + '&note=' + txt;
                    $.ajax({
                        type: 'POST',
                        url: site_url + _url,
                        data: string,
                        cache: false,
                        dataType: 'json',
                        beforeSend: function () {
                            swal({
                                title: "Processing Request",
                                text: "Please Wait . . .",
                                type: "info",
                                showCancelButton: false,
                                showConfirmButton: false
                            });
                        },
                        success: function (json) {
                            if (json.status === 'success') {
                                swal(
                                    'Success',
                                    json.message,
                                    'success'
                                );
                                if (_callback) {
                                    _callback();
                                }
                            } else {
                                swal(
                                    'Error',
                                    json.message,
                                    'error'
                                );
                            }
                        },
                        error: function (xhr, status, error) {
                            swal(
                                'Error',
                                error,
                                'error'
                            );
                        }
                    });
                }
            });
    } else {
        swal(
            'Warning',
            'Invalid request!',
            'warning',
        );
    }
}

function dialogPopup(_title, _url, _params = "") {
    if (_title && _url) {
        swal({
                title: "Confirmation",
                text: _title,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                cancelButtonText: "No",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        type: 'POST',
                        url: site_url + _url,
                        data: _params,
                        cache: false,
                        dataType: 'json',
                        beforeSend: function () {
                            swal({
                                title: "Processing Request",
                                text: "Please Wait . . .",
                                type: "info",
                                showCancelButton: false,
                                showConfirmButton: false
                            });
                        },
                        success: function (json) {
                            if (json.status === 'success') {
                                swal.close();
                                docBlur();
                                $("body").addClass("modal-open");
                                $("#form_dialog").html(json.results).show();
                            } else {
                                swal(
                                    'Error',
                                    json.message,
                                    'error'
                                );
                            }
                        },
                        error: function (xhr, status, error) {
                            swal(
                                'Error',
                                error,
                                'error'
                            );
                        }
                    });
                }
            });
    } else {
        swal(
            'Warning',
            'Invalid request!',
            'warning',
        );
    }
}

function dialogOpenTab(_title, _url, _params = false) {
    if (_title && _url) {
        swal({
                title: "Confirmation",
                text: _title,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                cancelButtonText: "No",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    var string = (_params ? '?' + _params : '');
                    window.open(site_url + _url + string);
                }
                swal.close();
            });
    } else {
        swal(
            'Warning',
            'Invalid request!',
            'warning',
        );
    }
}

function setImagePreview(_el, _input) {
    if (_input.files && _input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(_el).attr('src', e.target.result);
        };
        reader.readAsDataURL(_input.files[0]);
    } else {
        $(_el).attr('src', 'https://pemadam.jakarta.go.id/central_assets/placeholder/noimage.png');
    }
}

function setPhotoPreview(_el, _input) {
    if (_input.files && _input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(_el).attr('src', e.target.result);
        };
        reader.readAsDataURL(_input.files[0]);
    } else {
        $(_el).attr('src', 'https://pemadam.jakarta.go.id/central_assets/placeholder/nophoto.png');
    }
}

function changeStatus(e, _id, _url, _field = 'status') {
    if (e && _id && _url) {
        if (e.checked) {
            $(e).val(1);
            setStatus(_id, _url, _field, '1');
        } else {
            $(e).val(0);
            setStatus(_id, _url, _field, '0');
        }
    }
}

function setStatus(_id, _url, _field, _status) {
    if (_id && _url && _field && _status) {
        var string = 'id=' + _id + '&field=' + _field + '&status=' + _status;
        var proceed = saveData(_url, string);
        if (proceed) {
            if (proceed.status === 'success') {
                toastSuccess(proceed.message);
            } else {
                toastError(proceed.message);
            }
        }
    }
}

function setSelections(_el, _url, _params = null, _selected = false) {
    var _items = [];
    if (_el && $(_el).length) {
        var _select = $(_el);
		var _value = _select.val();
		var isMultiple = _select.hasClass("select-multiple");
		if (isMultiple && _value.length > 0) {
			_select.val("").trigger("change");
		} else if (_value != "") {
			_select.val("").trigger("change");
		}
        _select.find("option").remove().end();
        if (!_select.hasClass("select-multiple")) {
            _select.append('<option value=""></option>');
        }
        if (_url && _params) {
            var string = _params;
            $.ajax({
                type: "POST",
                url: site_url + _url,
                data: string,
                cache: false,
                dataType: "json",
                async: false,
                beforeSend: function () {
                    loading_open();
                },
                success: function (json) {
                    _items = json.items;
                    setItems(_el, _items, _selected);
                },
                complete: function () {
                    loading_close();
                },
                error: function (xhr, status, error) {
                    swal(
                        'Error',
                        error,
                        'error'
                    );
                }
            });
        }
    }
    return _items;
}

function setAjaxSelections(_el, _url, _params = null, _selected = false) {
	var _select = $(_el);
	var _value = _select.val();
	var isMultiple = _select.hasClass("select-multiple");
	if (isMultiple && _value.length > 0) {
		_select.val("").trigger("change");
	} else if (_value != "") {
		_select.val("").trigger("change");
	}
	_select.find("option").remove().end();
	if (!isMultiple) {
		_select.append('<option value=""></option>');
	}
    if (_select.hasClass("select2-hidden-accessible")) {
        _select.select2("destroy");
    }
	if (_url && _params) {
		var _obj = stringToObject(_params);
		_select.select2({
			width: "100%",
			placeholder: "Silahkan pilih opsi",
			minimumInputLength: 0,
			allowClear: !isMultiple,
			closeOnSelect: !isMultiple,
			ajax: {
				type: "POST",
                url: site_url + _url,
				delay: 250,
				cache: false,
				dataType: "json",
				data: function (params) {
					_obj.search = params.term || "";
					_obj.page = params.page || 1;
					return _obj;
				},
				processResults: function (data, params) {
					var page = params.page || 1;
					return {
						results: $.map(data.items, function (item) {
							return {
								id: item.id,
								text: item.name,
							}
						}),
						pagination: {
							more: (page * 10) <= data.total
						}
					};
				},
			}
		});

		if (_selected) {
			var string = _params + "&selected=" + _selected;
			$.ajax({
				type: "POST",
                url: site_url + _url,
				data: string,
				cache: false,
				dataType: "json",
				beforeSend: function () {
					loading_open();
				},
				success: function (json) {
					if (json.items.length) {
						var row = json.items[0];
						var option = $("<option/>").attr("value", row.id).text(row.name);
						_select.append(option);
						if (_selected) {
							_select.val(_selected).trigger("change");
						}
					}
				},
				complete: function () {
					loading_close();
				}
			});
		}
	} else {
        _select.select2({
            width: "100%",
            placeholder: _select.data("placeholder"),
            closeOnSelect: !isMultiple,
            allowClear: !isMultiple
        });
    }
}

function stringToObject(str){
	var data = str.split("&");
	var obj = {};
	for (var key in data) {
		obj[data[key].split("=")[0]] = data[key].split("=")[1];
	}
	return obj;
}

function saveData(_url, _params) {
    var _return = false;
    if (_url && _params) {
        $.ajax({
            type: 'POST',
            url: site_url + _url,
            data: _params,
            cache: false,
            async: false,
            dataType: 'json',
            beforeSend: function () {
                loading_open();
            },
            success: function (json) {
                _return = json;
            },
            complete: function () {
                loading_close();
            },
            error: function (xhr, status, error) {
                swal(
                    'Error',
                    error,
                    'error'
                );
            }
        });
    }
    return _return;
}

function saveFormData(_form, _url, _callback = false, _before = false, _after = false) {
    if (_form && _url && fieldValidate(_form)) {
        var string = $(_form).serialize();
        $.ajax({
            type: "POST",
            url: site_url + _url,
            data: string,
            cache: false,
            dataType: "json",
            beforeSend: function () {
                if (_before) {
                    _before();
                } else {
                    Swal.fire({
                        title: "Processing Request",
                        html: '<i class="fa fa-spinner fa-spin me-2"></i> Please wait . . .',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    });
                }
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
                            if (_callback) {
                                _callback(json);
                            } else if (json.url) {
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
                }
            },
            complete: function () {
                if (_after) {
                    _after();
                }
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
            }
        });
    }
}

function submitData(_form = '#form_data') {
    if (fieldValidate(_form)) {
        $(_form + " input[type=submit]").click();
    }
}

function sendEmail(_form = '#form_data', _url = '', _detail = false) {
    if (_form && _url && fieldValidate(_form)) {
        var string = $(_form).serialize();
        $.ajax({
            type: 'POST',
            url: site_url + _url,
            data: string,
            cache: false,
            dataType: 'json',
            beforeSend: function () {
                swal({
                    title: "Processing Request",
                    text: "Please Wait . . .",
                    type: "info",
                    showCancelButton: false,
                    showConfirmButton: false
                });
            },
            success: function (json) {
                if (json.status === 'success') {
                    swal(
                        'Success',
                        json.message,
                        'success'
                    );
                    if (_detail) {
                        closeModalDetail();
                    } else {
                        closeModal();
                    }
                } else {
                    swal(
                        'Error',
                        json.message,
                        'error'
                    );
                }
            },
            error: function (xhr, status, error) {
                swal(
                    'Error',
                    error,
                    'error'
                );
            }
        });
    }
}

function passwordVisibility(_el) {
    var _input = $(_el).find("input");
    var _icon = $(_el).find(".bi");
    if (_input.attr("type") === "password") {
        _input.attr("type", "text");
        _icon.removeClass("bi-eye-slash");
        _icon.addClass("bi-eye");
    } else {
        _input.attr("type", "password");
        _icon.removeClass("bi-eye");
        _icon.addClass("bi-eye-slash");
    }
}

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(locationSuccess, locationError);
    } else {
        swal(
            'Error',
            'Geolocation is not supported by this browser',
            'error'
        );
    }
}

function locationSuccess(position) {
    $('form input[name=latitude]').val(position.coords.latitude);
    $('form input[name=longitude]').val(position.coords.longitude);
    placeAutoComplete(position.coords.latitude, position.coords.longitude, 17);
    // console.log("Latitude: " + position.coords.latitude + "<br>Longitude: " + position.coords.longitude);
}

function locationError(error) {
    swal(
        'Error',
        error.message,
        'error'
    );
    console.log('Error Location: ' + error.code + ' - ' + error.message);
}

function placeAutoComplete(lat, lng, z) {
    if (typeof google !== 'undefined') {
        var latLng = new google.maps.LatLng(lat, lng);
        var map = new google.maps.Map(document.getElementById('map_canvas'), {
            zoom: z,
            center: latLng,
            mapTypeId: google.maps.MapTypeId.TERRAIN
        });
        var marker = new google.maps.Marker({
            position: latLng,
            title: 'Location',
            map: map,
            draggable: true
        });
        updateMarkerPosition(latLng);
        google.maps.event.addListener(marker, 'drag', function () {
            updateMarkerPosition(marker.getPosition());
        });
        var input = document.getElementById('search_place');
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);
        autocomplete.setFields(['geometry']);
        autocomplete.addListener('place_changed', function () {
            marker.setVisible(false);
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                toastError("No details available for input: '" + place.name + "'");
                return;
            }
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);
            updateMarkerPosition(marker.getPosition());
        });
    }
}

function updateMarkerPosition(latLng) {
    $('#form_data input[name=latitude]').val([latLng.lat()]);
    $('#form_data input[name=longitude]').val([latLng.lng()]);
}

function setGmap(lat, lng, z, drag) {
    if (typeof google !== 'undefined') {
        var latLng = new google.maps.LatLng(lat, lng);
        var map = new google.maps.Map(document.getElementById('map_canvas'), {
            //scrollwheel	: false,
            zoom: z,
            center: latLng,
            mapTypeId: google.maps.MapTypeId.TERRAIN
        });
        new google.maps.Marker({
            position: latLng,
            title: 'Location',
            map: map,
            draggable: drag
        });
    }
}

function captchaRefresh(_alert = false, _img = "#captcha_img", _input = "input[name=captcha]") {
    if (_img) {
        $.ajax({
            type: "POST",
            url: site_url + "captcha-refresh",
            cache: false,
            dataType: "json",
            beforeSend: function () {
                loading_open();
            },
            success: function (json) {
                $(_img).html(json.results);
                $(_input).val("");
                if (_alert) {
                    toastSuccess(json.message);
                }
            },
            complete: function () {
                loading_close();
            },
            error: function (xhr, status, error) {
                swal(
                    'Error',
                    error,
                    'error'
                );
            }
        });
    }
}
