$.fn.serializeObject = function () {
    var self = this,
        json = {},
        push_counters = {},
        patterns = {
            validate: /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
            key: /[a-zA-Z0-9_]+|(?=\[\])/g,
            push: /^$/,
            fixed: /^\d+$/,
            named: /^[a-zA-Z0-9_]+$/,
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
            reverse_key = reverse_key.replace(
                new RegExp("\\[" + k + "\\]$"),
                ""
            );
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

$.fn.DataTable.ext.pager.numbers_length = 5;
$.fn.DataTable.ext.pager.full_numbers_no_ellipses = function (page, pages) {
    var numbers = [];
    var buttons = $.fn.DataTable.ext.pager.numbers_length;
    var half = Math.floor(buttons / 2);
    var _range = function (len, start) {
        var end;
        if (typeof start === "undefined") {
            start = 0;
            end = len;
        } else {
            end = start;
            start = len;
        }
        var out = [];
        for (var i = start; i < end; i++) {
            out.push(i);
        }
        return out;
    };
    if (pages <= buttons) {
        numbers = _range(0, pages);
    } else if (page <= half) {
        numbers = _range(0, buttons);
    } else if (page >= pages - 1 - half) {
        numbers = _range(pages - buttons, pages);
    } else {
        numbers = _range(page - half, page + half + 1);
    }
    numbers.DT_el = "span";
    return ["first", "previous", numbers, "next", "last"];
};
$.extend($.fn.DataTable.defaults, {
    responsive: true,
    searching: false,
    autoWidth: false,
    processing: true,
    // RowReorder: true,
    // colReorder: true,
    serverSide: true,
    deferRender: true,
    pagingType: "full_numbers_no_ellipses",
    preDrawCallback: function (settings) {
        //$(this).closest("table").find("tbody").css("cursor","progress").css("opacity",0);
        $(this).removeClass("loaded").addClass("processing");
    },
    drawCallback: function (settings) {
        $(this).removeClass("processing").addClass("loaded");
        $(this)
            .find("td:not(.row-checkbox)")
            .each(function (index, element) {
                $(this).attr(
                    "data-title",
                    $(this)
                        .closest("table")
                        .find("tr:first-child th")
                        .eq($(this).index())
                        .text()
                );
            });
        if (
            $(this).find("input[id=checkAll]").length &&
            $(this).find("input[id=checkAll]").is(":checked")
        ) {
            $(this).find("input[id=checkAll]").val(0).prop("checked", false);
        }
        docBlur();
        $("html, body").animate({ scrollTop: 0 }, 200);
    },
    language: {
        loadingRecords:
            'Loading ... <div class="m-loader m-loader--lg m-loader--primary"></div>',
        processing:
            'Processing ... <div class="m-loader m-loader--lg m-loader--primary"></div>',
        paginate: {
            first: '<i class="fa fa-angle-double-left"></i>',
            previous: '<i class="fa fa-angle-left"></i>',
            next: '<i class="fa fa-angle-right"></i>',
            last: '<i class="fa fa-angle-double-right"></i>',
        },
    },
});

jQuery.validator.setDefaults({
    ignore: ".ignore, .optional, .select2-search__field, :hidden, .note-editor *, [contenteditable='true']:not([name])",
    errorPlacement: function (error, element) {
        if (
            element.hasClass("select2") &&
            element.next(".select2-container").length
        ) {
            error.insertAfter(element.next(".select2-container"));
        } else {
            // error.insertAfter(element.prev());
            error.appendTo(element.parent());
        }
    },
});
$.ajaxSetup({
    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
});
$(document)
    .on("show.bs.modal", ".modal", function (event) {
        var zIndex = 999 + 10 * $(".modal:visible").length;
        $(this).css("z-index", zIndex);
        if ($(this).attr("id")) {
            $("body").addClass($(this).attr("id"));
        }
        setTimeout(function () {
            $(".modal-backdrop")
                .not(".modal-stack")
                .first()
                .css("z-index", zIndex - 1)
                .addClass("modal-stack");
        }, 0);
    })
    .on("hidden.bs.modal", ".modal", function (event) {
        $(".modal:visible").length && $("body").addClass("modal-open");
    });
$(document).on("inserted.bs.tooltip", function (event) {
    var zIndex = 999 + 10 * $(".modal:visible").length;
    var tooltipId = $(event.target).attr("aria-describedby");
    $("#" + tooltipId).css("z-index", zIndex);
});
$(document).on("inserted.bs.popover", function (event) {
    var zIndex = 999 + 10 * $(".modal:visible").length;
    var popoverId = $(event.target).attr("aria-describedby");
    $("#" + popoverId).css("z-index", zIndex);
});

$(document).on(
    "change",
    "table .checkbox-table input[type=checkbox]",
    function () {
        var elRowTable = $(this).closest("tr");
        var elTable = $(this).closest("table").DataTable();
        if ($(this).is(":checked")) {
            elTable.row(elRowTable).select();
        } else {
            elTable.row(elRowTable).deselect();
        }
        tableSelectedInfo(elTable);
    }
);

$(document).on("select2:open", () => {
    setTimeout(() => {
        var searchInput = document.querySelector(
            ".select2-container--open .select2-search__field"
        );
        if (searchInput) {
            searchInput.focus();
        }
    }, 1);
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

function closeModal() {
    if ($("body").hasClass("form_dialog_detail")) {
        $("#form_dialog_detail").html("");
        $("#form_dialog_detail").modal("hide");
        if ($("#form_dialog_detail").attr("data-select2-id")) {
            $("#form_dialog_detail").removeAttr("data-select2-id");
        }
        $("body").removeClass("form_dialog_detail");
    } else {
        $("#form_dialog").html("");
        $("#form_dialog").modal("hide");
        if ($("#form_dialog").attr("data-select2-id")) {
            $("#form_dialog").removeAttr("data-select2-id");
        }
        if ($("body").hasClass("form_dialog")) {
            $("body").removeClass("form_dialog");
        }
    }
    if ($(".note-modal").length) {
        $(".note-modal").remove();
    }
    if ($(".dz-hidden-input").length) {
        $(".dz-hidden-input").remove();
    }
}

function docBlur() {
    window.blur();
    document.activeElement.blur();
}

function setNumber(_el = "#form_data") {
    $(_el + " :input[type=number]").on("mousewheel", function (e) {
        e.preventDefault();
    });
}

function setSelect2(_el) {
    if ($(_el).length > 1) {
        $.each($(_el), function () {
            $(this).select2({
                width: "100%",
                placeholder: $(this).data("placeholder")
                    ? $(this).data("placeholder")
                    : "Select an option",
                dropdownParent: $(document.body).hasClass("modal-open")
                    ? "#" +
                      $(this).parents(".modal").attr("id") +
                      " .modal-body"
                    : null,
                allowClear: !$(this).hasClass("select2-multiple"),
                closeOnSelect: !$(this).hasClass("select2-multiple"),
            });
        });
    } else if ($(_el).length > 0) {
        $(_el).select2({
            width: "100%",
            placeholder: $(_el).data("placeholder")
                ? $(_el).data("placeholder")
                : "Select an option",
            dropdownParent: $(document.body).hasClass("modal-open")
                ? "#" + $(_el).parents(".modal").attr("id") + " .modal-body"
                : null,
            allowClear: !$(_el).hasClass("select2-multiple"),
            closeOnSelect: !$(_el).hasClass("select2-multiple"),
        });
    }
}

function setDatePicker(_el, _start = null, _end = null, _view = null) {
    $(_el).datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        startDate: _start ? _start : "-3m",
        endDate: _end ? _end : "+3m",
        startView: _view ? _view : "days",
    });
}

function setDateRangePicker(_el, _time = false, _start = null, _end = null) {
    if ($(_el).length) {
        $(_el).daterangepicker({
            parentEl: $(document.body).hasClass("modal-open")
                ? "#" + $(_el).parents(".modal").attr("id") + " .modal-body"
                : null,
            autoUpdateInput: false,
            timePicker: _time,
            timePicker24Hour: _time,
            startDate: _start ? _start : moment().startOf("day"),
            endDate: _end ? _end : moment().endOf("day"),
            locale: {
                format: _time ? "YYYY-MM-DD HH:mm" : "YYYY-MM-DD",
            },
            ranges: {
                Today: [moment().startOf("day"), moment().endOf("day")],
                Yesterday: [
                    moment().subtract(1, "days").startOf("day"),
                    moment().subtract(1, "days").endOf("day"),
                ],
                "Last 7 Days": [
                    moment().subtract(6, "days").startOf("day"),
                    moment().endOf("day"),
                ],
                "Last 30 Days": [
                    moment().subtract(29, "days").startOf("day"),
                    moment().endOf("day"),
                ],
                "This Month": [
                    moment().startOf("month"),
                    moment().endOf("month"),
                ],
                "Last Month": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ],
            },
        });
        $(_el).on("apply.daterangepicker", function (ev, picker) {
            $(this).val(
                picker.startDate.format(
                    _time ? "YYYY-MM-DD HH:mm" : "YYYY-MM-DD"
                ) +
                    " - " +
                    picker.endDate.format(
                        _time ? "YYYY-MM-DD HH:mm" : "YYYY-MM-DD"
                    )
            );
            $(this).trigger("change");
        });
        $(_el).on("cancel.daterangepicker", function (ev, picker) {
            $(this).val("");
            $(this).trigger("change");
        });
    }
}

function setPopupImage(_el, _gallery = false) {
    if ($(_el).length > 0) {
        $(_el).magnificPopup({
            type: "image",
            gallery: {
                enabled: _gallery,
            },
            mainClass: "mfp-fade",
        });
    }
}

function setPopupUrl(_el) {
    if ($(_el).length > 0) {
        $(_el).magnificPopup({
            type: "iframe",
            mainClass: "mfp-fade",
            removalDelay: 160,
            preloader: false,
            fixedContentPos: true,
            iframe: {
                markup:
                    '<div class="mfp-iframe-scaler">' +
                    '<div class="mfp-close"></div>' +
                    '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
                    "</div>",
                srcAction: "iframe_src",
            },
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
                template:
                    '<div class="m-tooltip tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>',
            });
        }, _timeout);
    }
}

function setSummerNote(_el, _simple = false, _height = 500) {
    if (_simple) {
        $(_el).summernote({
            dialogsInBody: true,
            height: _height,
            toolbar: [
                [
                    "style",
                    [
                        "bold",
                        "italic",
                        "underline",
                        "strikethrough",
                        "superscript",
                        "subscript",
                        "clear",
                    ],
                ],
                ["para", ["ul", "ol", "paragraph"]],
            ],
        });
    } else {
        $(_el).summernote({
            dialogsInBody: true,
            height: _height,
            toolbar: [
                [
                    "style",
                    [
                        "bold",
                        "italic",
                        "underline",
                        "strikethrough",
                        "superscript",
                        "subscript",
                        "clear",
                    ],
                ],
                ["fontsize", ["fontsize"]],
                ["color", ["color"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["height", ["height"]],
                ["table", ["table"]],
                ["insert", ["hr"]], //'image', 'link', 'video',
                ["view", ["codeview"]], //'fullscreen',
            ],
            popover: {
                image: [
                    [
                        "image",
                        [
                            "resizeFull",
                            "resizeHalf",
                            "resizeQuarter",
                            "resizeNone",
                        ],
                    ],
                    ["float", ["floatLeft", "floatRight", "floatNone"]],
                    ["remove", ["removeMedia"]],
                ],
            },
            buttons: {
                image: function () {
                    var ui = $.summernote.ui;
                    var button = ui.button({
                        contents: '<i class="note-icon-picture" />',
                        click: function () {
                            $("#modal_image").remove();
                            $.ajax({
                                url: site_url + "filemanager",
                                dataType: "html",
                                beforeSend: function () {
                                    $("#button-image i").replaceWith(
                                        '<i class="fa fa-circle-o-notch fa-spin"></i>'
                                    );
                                    $("#button-image").prop("disabled", true);
                                },
                                complete: function () {
                                    $("#button-image i").replaceWith(
                                        '<i class="fa fa-upload"></i>'
                                    );
                                    $("#button-image").prop("disabled", false);
                                },
                                success: function (html) {
                                    $("body").append(
                                        '<div id="modal_image" class="modal">' +
                                            html +
                                            "</div>"
                                    );
                                    $("#modal_image").modal("show");
                                    $("#modal_image").delegate(
                                        "a.thumbnail",
                                        "click",
                                        function (e) {
                                            e.preventDefault();
                                            $(_el).summernote(
                                                "insertImage",
                                                $(this).attr("href")
                                            );
                                            $("#modal_image").modal("hide");
                                        }
                                    );
                                },
                            });
                        },
                    });
                    return button.render();
                },
            },
        });
    }
}

function setClipboardPasteImage(_preview, _input) {
    if ($("#" + _preview).length && $("#" + _input).length) {
        document.getElementById(_preview).addEventListener("paste", (e) => {
            if (e.clipboardData.files.length) {
                document.getElementById(_input).files = e.clipboardData.files;
                $("#" + _input).trigger("change");
            }
        });
    }
}

function swalLoading() {
    Swal.fire({
        title: "Processing Request",
        html: '<i class="fa fa-spinner fa-spin me-2"></i> Please wait . . .',
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
    });
}

function swalAlert(_type = "error", _html = "") {
    Swal.fire({
        icon: _type,
        html: _html,
        confirmButtonText: "OK",
        customClass: {
            confirmButton: "btn btn-primary",
        },
    });
}

function openForm(_url, _params = "") {
    if (_url) {
        $.ajax({
            type: "POST",
            url: site_url + _url,
            data: _params,
            cache: false,
            beforeSend: function () {
                loading_open();
            },
            success: function (data) {
                docBlur();
                if ($("body").hasClass("modal-open")) {
                    $("#form_dialog_detail").html(data);
                    setTimeout(() => {
                        console.log("tes");
                        $("#form_dialog_detail").modal("show");
                    }, 100);
                } else {
                    $("#form_dialog").html(data);
                    setTimeout(() => {
                        $("#form_dialog").modal("show");
                        if (typeof loadPDF === "function") {
                            console.log("PDF URL:");
                            loadPDF();
                        }
                    }, 100);
                }
            },
            complete: function () {
                loading_close();
            },
            error: function (xhr, status, error) {
                swalAlert("error", error);
            },
        });
    }
}

function fieldValidate(_form) {
    return $(_form).valid();
}

function listPagination(_page = 1, _url, _form, _el) {
    var string = $(_form).serialize() + "&page=" + _page;
    $.ajax({
        type: "POST",
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
        },
    });
}

function addCommas(nStr) {
    if (nStr) {
        nStr = parseInt(nStr);
        nStr += "";
        x = nStr.split(",");
        x1 = x[0];
        x2 = x.length > 1 ? "," + x[1] : "";
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, "$1" + "," + "$2");
        }
        return x1 + x2;
    } else {
        if (isNaN(parseFloat(nStr))) {
            return "";
        } else {
            return nStr;
        }
    }
}

function removeSpace(e) {
    if (e.value) {
        return $(e).val(string_replace(e.value, " ", ""));
    }
}

function removeExceptNumber(e) {
    if (e.value) {
        var str = e.value.replace(/\D+/g, "");
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
    result = result >= 0 ? parseInt(result) : "";
    $(e).val(addCommas(result));
}

function number(str) {
    if (str) {
        return Number.parseFloat(str).toLocaleString("en-US", {
            maximumFractionDigits: 2,
        });
    } else {
        if (isNaN(parseFloat(str))) {
            return "";
        } else {
            return str;
        }
    }
}

function inArray(str, array) {
    if (array.length) {
        for (var i = 0; i < array.length; i++) {
            if (array[i] == str) return true;
        }
    }
    return false;
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

function setForm(item, _url, _params = false) {
    var _return = "";
    if (item && _url) {
        var string = "item=" + item;
        if (_params) {
            string += "&" + _params;
        }
        $.ajax({
            type: "POST",
            url: site_url + _url,
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
            },
        });
    }
    return _return;
}

function removeDetail(e, _value, _url, _callback = false) {
    if (_value && _url) {
        Swal.fire({
            icon: "question",
            title: "Konfirmasi",
            text: "Hapus data?",
            cancelButtonText: "Tidak, batalkan!",
            confirmButtonText: "Ya, lanjutkan!",
            showCancelButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-danger",
            },
        }).then(function (results) {
            if (results.isConfirmed) {
                var proceed = saveData(_url, "id=" + _value, _callback);
                if (proceed) {
                    removeFieldItem(e);
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
        setPopupUrl(_form + " .popup-url");
        if (_table && $(_form + " input[id=checkAll]").length) {
            $(_form + " input[id=checkAll]")
                .prop("checked", false)
                .val(0);
            _table.rows().deselect();
            tableSelectedInfo(_table);
        }
    }
}

function checkedAll(_form, _table, str = 0) {
    if (_form && _table) {
        if (str > 0 && $(_form + " input[id=checkAll]").length) {
            $(_form + " input[id=checkAll]").val(0);
            $(_form + " .checkbox-table input[type=checkbox]").prop(
                "checked",
                false
            );
            _table.rows().deselect();
        } else if ($(_form + " input[id=checkAll]").length) {
            $(_form + " input[id=checkAll]").val(1);
            $(_form + " .checkbox-table input[type=checkbox]").prop(
                "checked",
                true
            );
            _table.rows().select();
        }
        tableSelectedInfo(_table);
    }
}

function getCheckAll(_el, _index = 0) {
    var selecteds = [];
    if (
        $(_el + " tr.selected .checkbox-table input[type=checkbox]:checked")
            .length
    ) {
        $.each(
            $(
                _el +
                    " tr.selected .checkbox-table input[type=checkbox]:checked"
            ),
            function () {
                selecteds.push($(this).val());
            }
        );
    }
    return selecteds;
}

function tableSelectedInfo(_table) {
    var totalSelected = _table.rows({ selected: true }).count();
    var elInfo = $(_table.table().node())
        .closest("#table_data_wrapper")
        .find(".dataTables_info");
    var elInfoText =
        totalSelected > 0
            ? '<span class="select-item">' +
              addCommas(totalSelected) +
              " row" +
              (totalSelected > 1 ? "s" : "") +
              " selected</span>"
            : "";
    if (elInfo.find(".select-info").length) {
        elInfo.find(".select-info").html(elInfoText);
    } else {
        elInfo.append('<span class="select-info">' + elInfoText + "</span>");
    }
}

function changeCheckboxOption(e, _el, _required = true, _reverse = false) {
    var show;
    if (_reverse) {
        show = $(e).is(":checked") ? false : true;
    } else {
        show = $(e).is(":checked") ? true : false;
    }
    if (show) {
        if ($(_el).hasClass("d-none")) {
            $(_el).removeClass("d-none");
        }
        if (_required) {
            if ($(_el + " input:not(.optional)").length) {
                $(_el + " input:not(.optional)").attr("required", true);
            }
            if ($(_el + " select:not(.optional)").length) {
                $(_el + " select:not(.optional)").attr("required", true);
            }
            if ($(_el + " textarea:not(.optional)").length) {
                $(_el + " textarea:not(.optional)").attr("required", true);
            }
        }
    } else {
        if (!$(_el).hasClass("d-none")) {
            $(_el).addClass("d-none");
        }
        if (_required) {
            if ($(_el + " input:not(.optional)").length) {
                $(_el + " input:not(.optional)").removeAttr("required");
            }
            if ($(_el + " select:not(.optional)").length) {
                $(_el + " select:not(.optional)").removeAttr("required");
            }
            if ($(_el + " textarea:not(.optional)").length) {
                $(_el + " textarea:not(.optional)").removeAttr("required");
            }
        }
    }
}

function setItems(_el, _items, _selected = false) {
    if ($(_el).length) {
        var _label, _option;
        $(_items).each(function (index, row) {
            if (
                typeof row.subs != "undefined" &&
                Array.isArray(row.subs) &&
                row.subs.length
            ) {
                _label = $("<optgroup/>").attr("label", row.name);
                $(row.subs).each(function (i, r) {
                    if (
                        typeof r.subs != "undefined" &&
                        Array.isArray(r.subs) &&
                        r.subs.length
                    ) {
                        _option = $("<option/>")
                            .attr("value", r.id)
                            .attr("disabled", 1)
                            .text(r.name);
                        _label.append(_option);
                        $(r.subs).each(function (is, rs) {
                            _option = $("<option/>")
                                .attr("value", rs.id)
                                .html("&nbsp;&nbsp;&nbsp;" + rs.name);
                            _label.append(_option);
                        });
                    } else {
                        _option = $("<option/>")
                            .attr("value", r.id)
                            .text(r.name);
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

function dialogConfirm(_value, _text, _url, _callback = false) {
    if (_value && _text && _url) {
        Swal.fire({
            icon: "question",
            title: "Konfirmasi",
            text: _text,
            cancelButtonText: "Tidak, batalkan!",
            confirmButtonText: "Ya, lanjutkan!",
            showCancelButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-danger",
            },
        }).then(function (e) {
            if (e.isConfirmed) {
                saveData(_url, "id=" + _value, _callback);
            } else if (_callback) {
                _callback(false);
            }
        });
    } else {
        swalAlert("warning", "Invalid request!");
    }
}

function downloadConfirm(_path, _name) {
    if (_path && _name) {
        Swal.fire({
            icon: "question",
            title: "Konfirmasi",
            text: "Download file?",
            cancelButtonText: "Tidak, batalkan!",
            confirmButtonText: "Ya, lanjutkan!",
            showCancelButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-danger",
            },
        }).then(function (e) {
            if (e.isConfirmed) {
                window.open(
                    site_url + "general/download/?k=" + _path + "&f=" + _name
                );
            }
        });
    } else {
        swalAlert("warning", "Invalid request!");
    }
}

async function dialogNote(
    _value,
    _title,
    _url,
    _callback = false,
    _validate = true
) {
    if (_value && _title && _url) {
        const { value: text } = await Swal.fire({
            title: _title,
            input: "textarea",
            inputLabel: "Note",
            inputPlaceholder: "Write your note here...",
            inputAttributes: {
                "aria-label": "Write your note here",
            },
            inputValidator: (value) => {
                if (_validate && !value) {
                    return "Note is required!";
                }
            },
            cancelButtonText: "Tidak, batalkan!",
            confirmButtonText: "Ya, lanjutkan!",
            showCancelButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            focusConfirm: false,
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-danger",
            },
        });
        if (text) {
            saveData(_url, "id=" + _value + "&note=" + text, _callback);
        }
    } else {
        swalAlert("warning", "Invalid request!");
    }
}

async function dialogPassword(_url, _callback = false) {
    if (_url) {
        const { value: text } = await Swal.fire({
            input: "password",
            inputLabel: "Silahkan masukan password anda",
            inputPlaceholder: "Password",
            inputAttributes: {
                autocapitalize: "off",
                autocorrect: "off",
            },
            inputValidator: (value) => {
                if (!value) {
                    return "Silahkan masukan password anda!";
                }
            },
            cancelButtonText: "Tidak, batalkan!",
            confirmButtonText: "Ya, lanjutkan!",
            showCancelButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            focusConfirm: false,
            showLoaderOnConfirm: true,
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-danger",
            },
        });
        if (text) {
            saveData(_url, "password=" + text, _callback);
        }
    } else {
        swalAlert("warning", "Invalid request!");
    }
}

function dialogPopup(_text, _url, _params = "") {
    if (_text && _url) {
        Swal.fire({
            icon: "question",
            title: "Konfirmasi",
            text: _text,
            cancelButtonText: "Tidak, batalkan!",
            confirmButtonText: "Ya, lanjutkan!",
            showCancelButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-danger",
            },
        }).then(function (e) {
            if (e.isConfirmed) {
                openForm(_url, _params);
            }
        });
    } else {
        swalAlert("warning", "Invalid request!");
    }
}

function dialogOpenTab(_text, _url, _params = false) {
    if (_text && _url) {
        Swal.fire({
            icon: "question",
            title: "Konfirmasi",
            text: _text,
            cancelButtonText: "Tidak, batalkan!",
            confirmButtonText: "Ya, lanjutkan!",
            showCancelButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-danger",
            },
        }).then(function (e) {
            if (e.isConfirmed) {
                var string = _params ? "?" + _params : "";
                window.open(site_url + _url + string);
            }
        });
    } else {
        swalAlert("warning", "Invalid request!");
    }
}

function setImagePreview(_input, _preview) {
    if (_input.files && _input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(_preview).attr("src", e.target.result);
        };
        reader.readAsDataURL(_input.files[0]);
    } else {
        $(_preview).attr(src, $(_input).attr("default"));
    }
}

function changeStatus(e, _id, _url, _field = "status") {
    if (e && _id && _url) {
        if (e.checked) {
            $(e).val(1);
            setStatus(_id, _url, _field, "1");
        } else {
            $(e).val(0);
            setStatus(_id, _url, _field, "0");
        }
    }
}

function setStatus(_id, _url, _field, _status) {
    if (_id && _url && _field && _status) {
        var string = "id=" + _id + "&field=" + _field + "&status=" + _status;
        var proceed = saveData(_url, string);
        if (proceed && !proceed.status) {
            $("input." + _field + "-" + _id).prop("checked", _status == 0);
        }
    }
}

function setSelections(_el, _url, _params = null, _selected = false) {
    var _items = [];
    if (_el && $(_el).length) {
        var _select = $(_el);
        var _value = _select.val();
        var isMultiple =
            _select.hasClass("select2-multiple") ||
            _select.hasClass("multi-select");
        if (isMultiple && _value.length > 0) {
            _select.val("").trigger("change");
        } else if (_value != "") {
            _select.val("").trigger("change");
        }
        _select.find("option").remove().end();
        if (!isMultiple) {
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
                    swalAlert("error", error);
                },
            });
        }
    }
    return _items;
}

function setAjaxSelections(_el, _url, _params = null, _selected = false) {
    var _select = $(_el);
    var _value = _select.val();
    var isMultiple =
        _select.hasClass("select2-multiple") ||
        _select.hasClass("multi-select");
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
            placeholder: _select.data("placeholder")
                ? _select.data("placeholder")
                : "Select an option",
            allowClear: !isMultiple,
            dropdownParent: $(document.body).hasClass("modal-open")
                ? "#" + $(_el).parents(".modal").attr("id")
                : null,
            // dropdownParent: ($(_el).closest('.modal').length ? $(_el).closest('.modal') : $(document.body)),

            closeOnSelect: !isMultiple,
            minimumInputLength: 0,
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
                            };
                        }),
                        pagination: {
                            more: page * 10 <= data.total,
                        },
                    };
                },
            },
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
                        $.each($(json.items), function (index, row) {
                            var option = $("<option/>")
                                .attr("value", row.id)
                                .text(row.name);
                            _select.append(option);
                        });
                        if (_selected) {
                            _select.val(_selected).trigger("change");
                        }
                    }
                },
                complete: function () {
                    loading_close();
                },
                error: function (xhr, status, error) {
                    swalAlert("error", error);
                },
            });
        }
    } else {
        _select.select2({
            width: "100%",
            placeholder: _select.data("placeholder")
                ? _select.data("placeholder")
                : "Select an option",
            closeOnSelect: !isMultiple,
            allowClear: !isMultiple,
        });
    }
}

function stringToObject(str) {
    var data = str.split("&");
    var obj = {};
    for (var key in data) {
        obj[data[key].split("=")[0]] = data[key].split("=")[1];
    }
    return obj;
}

function locationReload() {
    location.reload();
}

function saveData(_url, _params, _callback = false, _modal = false) {
    var _return = false;
    if (_url && _params) {
        $.ajax({
            type: "POST",
            url: site_url + _url,
            data: _params,
            cache: false,
            async: false,
            dataType: "json",
            beforeSend: function () {
                swalLoading();
            },
            success: function (json) {
                _return = json;
                if (json.status) {
                    Swal.fire({
                        icon: "success",
                        html: json.message,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    }).then(function (e) {
                        if (e.isConfirmed) {
                            if (_modal) {
                                closeModal();
                            }
                            if (_callback) {
                                _callback(json);
                            } else if (json.url) {
                                location.replace(json.url);
                            }
                        }
                    });
                } else {
                    swalAlert("error", json.message);
                    if (_callback) {
                        _callback(json);
                    }
                }
            },
            error: function (xhr, status, error) {
                swalAlert("error", error);
                if (_callback) {
                    _callback(false);
                }
            },
        });
    }
    return _return;
}

function saveFormData(
    _form,
    _url,
    _callback = false,
    _loader = false,
    _modal = false
) {
    if (_form && _url && fieldValidate(_form)) {
        var string = $(_form).serialize();
        //var string = new FormData(_form);
        $.ajax({
            type: "POST",
            url: site_url + _url,
            data: string,
            cache: false,
            //contentType: false,
            // processData: false,
            dataType: "json",
            beforeSend: function () {
                if (_loader) {
                    loaderBefore($(_form).find(".btn-submit"));
                } else {
                    swalLoading();
                }
            },
            success: function (json) {
                if (json.status) {
                    Swal.fire({
                        icon: "success",
                        html: json.message,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    }).then(function (e) {
                        if (e.isConfirmed) {
                            if (_modal) {
                                closeModal();
                            }
                            if (_callback) {
                                _callback(json);
                            } else if (json.url) {
                                location.replace(json.url);
                            }
                        }
                    });
                } else {
                    swalAlert("error", json.message);
                }
            },
            complete: function () {
                if (_loader) {
                    loaderAfter($(_form).find(".btn-submit"));
                }
            },
            error: function (xhr, status, error) {
                swalAlert("error", error);
            },
        });
    }
}

function submitData(
    _event,
    _url,
    _callback = false,
    _loader = false,
    _modal = false
) {
    var _form = "#" + $(_event).attr("id");
    if (fieldValidate(_form)) {
        let formData = new FormData(_event);
        $.ajax({
            type: "POST",
            url: site_url + _url,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            beforeSend: function () {
                if (_loader) {
                    loaderBefore($(_form).find(".btn-submit"));
                } else {
                    swalLoading();
                }
            },
            success: function (json) {
                if (json.status) {
                    Swal.fire({
                        icon: "success",
                        html: json.message,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    }).then(function (e) {
                        if (e.isConfirmed) {
                            if (_modal) {
                                closeModal();
                            }
                            if (_callback) {
                                _callback(json);
                            } else if (json.url) {
                                location.replace(json.url);
                            }
                        }
                    });
                } else {
                    swalAlert("error", json.message);
                }
            },
            complete: function () {
                if (_loader) {
                    loaderAfter($(_form).find(".btn-submit"));
                }
            },
            error: function (xhr, status, error) {
                swalAlert("error", error);
            },
        });
    }
}

function submitDataarsip(
    _event,
    _url,
    _callback = false,
    _loader = false,
    _modal = false
) {
    var _form = "#" + $(_event).attr("id");
    if (fieldValidate(_form)) {
        let formData = new FormData(_event);
        $.ajax({
            type: "POST",
            url: site_url + _url,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            beforeSend: function () {
                if (_loader) {
                    loaderBefore($(_form).find(".btn-submit"));
                } else {
                    swalLoading();
                }
            },
            success: function (json) {
                if (json.status) {
                    Swal.fire({
                        icon: "success",
                        html: json.message,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    }).then(function (e) {
                        if (e.isConfirmed) {
                            if (_modal) {
                                closeModal();
                            }
                            if (_callback) {
                                _callback(json);
                            } else if (json.url) {
                                location.replace(json.url);
                            }
                            if (json.modal) {
                                console.log(json.data);
                                openForm("main/archives/pembubuhan", {
                                    nama_file: json.data.nama_file,
                                    code: json.data.code,
                                    _token: $('meta[name="csrf-token"]').attr(
                                        "content"
                                    ),
                                });
                            }
                        }
                    });
                } else {
                    swalAlert("error", json.message);
                }
            },
            complete: function () {
                if (_loader) {
                    loaderAfter($(_form).find(".btn-submit"));
                }
            },
            error: function (xhr, status, error) {
                swalAlert("error", error);
            },
        });
    }
}

// function submitData(_event, _url, _callback = false, _loader = false, _modal = false) {
//     _event.preventDefault(); // Mencegah reload form saat submit

//     let _form = $(_event.target);
//     //var _form = "#" + $(_event).attr('id');
//     if (fieldValidate(_form)) {
//         //let formData = new FormData(_event);
//         let formData = new FormData(_form[0]);
//         $.ajax({
//             type: "POST",
//             url: site_url + _url,
//             data: formData,
//             cache: false,
//             contentType: false,
//             processData: false,
//             dataType: "json",
//             beforeSend: function () {
//                 if (_loader) {
//                     loaderBefore($(_form).find(".btn-submit"));
//                 } else {
//                     swalLoading();
//                 }
//             },
//             success: function (json) {
//                 if (json.status) {
//                     Swal.fire({
//                         icon: "success",
//                         html: json.message,
//                         confirmButtonText: "OK",
//                         customClass: {
//                             confirmButton: "btn btn-primary"
//                         }
//                     }).then((function (e) {
//                         if (e.isConfirmed) {
//                             if (_modal) {
//                                 closeModal();
//                             }
//                             if (typeof _callback === "function") {
//                     _callback(json);

//                             } else if (json.url) {
//                                 location.replace(json.url);
//                             }
//                         }
//                     }));
//                 } else {
//                     swalAlert("error", json.message);
//                 }
//             },
//             complete: function () {
//                 if (_loader) {
//                     loaderAfter($(_form).find(".btn-submit"));
//                 }
//             },
//             error: function (xhr, status, error) {
//                 swalAlert("error", error);
//             }
//         });
//     }
// }

function delTable(
    _url,
    _callback,
    _el = "#form_query #table_data",
    _text = "Hapus data terpilih?"
) {
    if (_url && _callback) {
        var values = getCheckAll(_el);
        if (values.length) {
            dialogConfirm(values, _text, _url, _callback);
        } else {
            swalAlert("warning", "Silahkan pilih minimal 1 data!");
        }
    }
}

function delData(_id, _url, _callback = false, _text = "Hapus data?") {
    if (_id && _url) {
        dialogConfirm(_id, _text, _url, _callback);
    }
}

function loaderBefore(_el) {
    if (_el) {
        _el.attr("data-kt-indicator", "on");
        _el.attr("disabled", 1);
    }
}

function loaderAfter(_el) {
    if (_el) {
        _el.removeAttr("data-kt-indicator");
        _el.removeAttr("disabled");
    }
}

function exportDatatableAction(e, dt, button, config) {
    var self = this;
    var oldStart = dt.settings()[0]._iDisplayStart;
    dt.one("preXhr", function (e, s, data) {
        data.start = 0;
        data.length = 2147483647;
        dt.one("preDraw", function (e, settings) {
            if (button[0].className.indexOf("buttons-copy") >= 0) {
                $.fn.dataTable.ext.buttons.copyHtml5.action.call(
                    self,
                    e,
                    dt,
                    button,
                    config
                );
            } else if (button[0].className.indexOf("buttons-excel") >= 0) {
                $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)
                    ? $.fn.dataTable.ext.buttons.excelHtml5.action.call(
                          self,
                          e,
                          dt,
                          button,
                          config
                      )
                    : $.fn.dataTable.ext.buttons.excelFlash.action.call(
                          self,
                          e,
                          dt,
                          button,
                          config
                      );
            } else if (button[0].className.indexOf("buttons-csv") >= 0) {
                $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config)
                    ? $.fn.dataTable.ext.buttons.csvHtml5.action.call(
                          self,
                          e,
                          dt,
                          button,
                          config
                      )
                    : $.fn.dataTable.ext.buttons.csvFlash.action.call(
                          self,
                          e,
                          dt,
                          button,
                          config
                      );
            } else if (button[0].className.indexOf("buttons-pdf") >= 0) {
                $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)
                    ? $.fn.dataTable.ext.buttons.pdfHtml5.action.call(
                          self,
                          e,
                          dt,
                          button,
                          config
                      )
                    : $.fn.dataTable.ext.buttons.pdfFlash.action.call(
                          self,
                          e,
                          dt,
                          button,
                          config
                      );
            } else if (button[0].className.indexOf("buttons-print") >= 0) {
                $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
            }
            dt.one("preXhr", function (e, s, data) {
                settings._iDisplayStart = oldStart;
                data.start = oldStart;
            });
            setTimeout(dt.ajax.reload, 0);
            return false;
        });
    });
    dt.ajax.reload();
}
