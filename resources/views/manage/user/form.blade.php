<div id="overlay"></div>
<form id="form_data" method="post" role="form" autocomplete="off" onsubmit="return false;">
    @csrf
    @if (isset($row->id))
        <input type="hidden" name="update" value="true" />
        <input type="hidden" name="id" value="{{ $row->id }}" />
    @else
        <input type="hidden" name="save" value="true" />
    @endif
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">{{ $title }}</h2>
                <button type="button" class="btn btn-icon btn-sm btn-active-icon-primary" onclick="closeModal()">
                    <i class="fa fa-times fs-3"></i>
                </button>
            </div>
            <div class="modal-body m-4">
                <div class="d-flex flex-column m-3">
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2" id="input_ref">Reference</label>
                        <select class="form-select form-select-solid mb-3 mb-lg-0" name="ref" id="input_ref"
                            data-control="select2" data-placeholder="Select an option" required>
                            <option value=""></option>
                            <option value="pegawai">Pegawai</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div class="field-pegawai d-none">
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2" id="input_pegawai">Pegawai</label>
                            <select class="form-select form-select-solid mb-3 mb-lg-0" name="pegawai_id"
                                id="input_pegawai" data-control="select2" data-placeholder="Select an option" required>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="field-user d-none">
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2" id="input_name">Full Name</label>
                            <input type="text" class="form-control form-control-solid mb-3 mb-lg-0" name="name"
                                id="input_name" value="{{ $row->name ?? '' }}" placeholder="Full name" required />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2" id="input_username">Username</label>
                            <input type="text" class="form-control form-control-solid mb-3 mb-lg-0 text-lowercase"
                                name="username" id="input_username" value="{{ $row->username ?? '' }}"
                                onkeyup="removeSpace(this)" placeholder="Username" required />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2" id="input_email">Email</label>
                            <input type="email" class="form-control form-control-solid mb-3 mb-lg-0 text-lowercase"
                                name="email" id="input_email"value="{{ $row->email ?? '' }}"
                                placeholder="Email Address" required />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2" id="input_phone">Phone</label>
                            <input type="number" class="form-control form-control-solid mb-3 mb-lg-0" name="phone"
                                id="input_phone" value="{{ $row->phone ?? '' }}" placeholder="Phone Number" required />
                        </div>
                        <div class="fv-row mb-7" data-kt-password-meter="true">
                            <div class="mb-1">
                                <label class="{{ empty($row->id) ? 'required' : '' }} fw-bold fs-6 mb-2"
                                    id="input_password">{{ isset($row->id) ? 'Change ' : '' }}Password</label>
                                <div class="position-relative mb-3 password-visibility">
                                    <input class="form-control form-control-lg form-control-solid {{ isset($row->id) ? 'optional ' : '' }}" type="password"
                                        name="password" id="input_password" autocomplete="off" placeholder="Password"
                                        {{ empty($row->id) ? 'required ' : '' }} />
                                    <span
                                        class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                        data-kt-password-meter-control="visibility">
                                        <i class="bi bi-eye-slash fs-2"></i>
                                        <i class="bi bi-eye fs-2 d-none"></i>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                </div>
                            </div>
                            <div class="text-muted">Use 8 or more characters with a mix of letters, numbers &amp;
                                symbols.</div>
                        </div>
                    </div>
                    @isset($status)
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2" id="input_status">Status</label>
                            <select class="form-select form-select-solid mb-3 mb-lg-0" name="status_id" id="input_status"
                                data-control="select2" data-placeholder="Select an option" required>
                                <option value=""></option>
                                @foreach ($status as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endisset
                    <div class="mb-7">
                        <label class="required fw-bold fs-6 mb-5">Role</label>
                        @foreach ($roles as $role)
                            <div class="d-flex fv-row">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input type="radio" class="form-check-input me-3" name="role_id"
                                        value="{{ $role->id }}" id="role_{{ $role->id }}" required />
                                    <label class="form-check-label" for="role_{{ $role->id }}"
                                        id="role_{{ $role->id }}">
                                        <div class="fw-bolder text-gray-800">{{ $role->name }}</div>
                                        <div class="text-gray-600">{{ $role->description }}</div>
                                    </label>
                                </div>
                            </div>
                            <div class="separator separator-dashed my-5"></div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer flex-end gap-2">
                <button type="button" class="btn btn-light btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="button" class="btn btn-primary btn-submit">
                    <span class="indicator-label">Submit</span>
                    <span class="indicator-progress">Please wait...<span
                            class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        setSelect2("#form_data select[data-control=select2]");
        KTPasswordMeter.init();
        var _pass = KTPasswordMeter.getInstance(document.querySelector('[data-kt-password-meter="true"]'));
        var _passMin = 40;
        $("#form_data input[name=password]").keyup(function(e) {
            if (this.value.length > 0) {
                if (_pass.getScore() >= _passMin) {
                    $("#form_data .btn-submit").removeAttr("disabled");
                } else {
                    $("#form_data .btn-submit").attr("disabled", 1);
                }
            } else {
                if ($("#form_data .btn-submit").attr("disabled")) {
                    $("#form_data .btn-submit").removeAttr("disabled");
                }
            }
        });
        $("#form_data select[id=input_ref]").change(function(e) {
            if (!$("#form_data .field-pegawai").hasClass("d-none")) {
                $("#form_data .field-pegawai").addClass("d-none");
                $("#form_data .field-pegawai select").removeAttr("required");
            }
            if (!$("#form_data .field-user").hasClass("d-none")) {
                $("#form_data .field-user").addClass("d-none");
                $("#form_data .field-user input:not(.optional)").removeAttr("required");
            }
            if (this.value) {
                if (this.value == "pegawai" && $("#form_data .field-pegawai").hasClass("d-none")) {
                    $("#form_data .field-pegawai").removeClass("d-none");
                    $("#form_data .field-pegawai select").attr("required", true);
                } else if (this.value == "user" && $("#form_data .field-user").hasClass("d-none")) {
                    $("#form_data .field-user").removeClass("d-none");
                    $("#form_data .field-user input:not(.optional)").attr("required", true);
                }
            }
        });
        $("#form_data .btn-submit").click(function(e) {
            e.preventDefault();
            saveFormData("#form_data", "manage/user/save", showData, true, true);
        });
        $("#form_data input").keydown(function(e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if (keycode === 13) {
                e.preventDefault();
                if (_pass.getScore() >= _passMin) {
                    $("#form_data .btn-submit").trigger("click");
                }
            }
        });
        @isset($row->ref)
            $("#form_data select[name=ref]").val("{{ $row->ref ?? '' }}").trigger("change");
        @endisset
        @isset($row->role_id)
            $("#form_data input[name=role_id][value={{ $row->role_id }}]").prop("checked", true);
        @endisset
        @isset($status)
            $("#form_data select[name=status_id]").val("{{ $row->status_id ?? 1 }}").trigger("change");
        @endisset
        @if (isset($row->ref) and $row->ref == 'pegawai')
            setAjaxSelections("#form_data select[id=input_pegawai]", "general/selection", "ref=pegawai",
                "{{ $row->username ?? '' }}");
        @else
            setAjaxSelections("#form_data select[id=input_pegawai]", "general/selection", "ref=pegawai");
        @endif
    });
</script> 
