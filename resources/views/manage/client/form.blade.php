<div id="overlay"></div>
<form id="form_data" method="post" role="form" autocomplete="off" enctype="multipart/form-data" onsubmit="return false;" novalidate>
    @csrf
    @if (isset($row->id))
        <input type="hidden" name="update" value="true" />
        <input type="hidden" name="id" value="{{ $row->id }}" />
        <input type="hidden" name="image_old" value="{{ $row->image }}" />
    @else
        <input type="hidden" name="save" value="true" />
    @endif
    <input type="file" name="image" class="userfile d-none" default="{{ $image }}" onchange="setImagePreview(this, '#form_data img.image-input-wrapper')" accept=".png, .jpg, .jpeg" />
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
                    <div class="image-input image-input-outline mb-6">
                        <div class="d-flex cursor-pointer position-relative w-125px" onclick="$('#form_data .userfile').click()">
                            <img class="image-input-wrapper h-auto cursor-pointer" src="{{ $image }}" />
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow position-absolute right-0 end-0">
                                <i class="bi bi-pencil-fill fs-7"></i>
                            </label>
                        </div>
                        <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2" id="input_name">Name</label>
                        <input type="text" class="form-control form-control-solid mb-3 mb-lg-0" name="name" id="input_name" value="{{ $row->name ?? "" }}" placeholder="name" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2" id="input_url_web">Web URL</label>
                        <input type="text" class="form-control form-control-solid mb-3 mb-lg-0 text-lowercase" name="url_web" id="input_url_web" value="{{ $row->url_web ?? "" }}" onkeyup="removeSpace(this)" placeholder="Web URL" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2" id="input_url_auth">OAuth Callback</label>
                        <input type="text" class="form-control form-control-solid mb-3 mb-lg-0 text-lowercase" name="url_auth" id="input_url_auth" value="{{ $row->url_auth ?? "" }}" onkeyup="removeSpace(this)" placeholder="OAuth Callback" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2" id="input_penugasan">Penugasan</label>
                        <select class="form-select form-select-solid select2-multiple mb-3 mb-lg-0" name="penugasan_ids[]" id="input_penugasan" data-control="select2" data-placeholder="Select an option" multiple>
                        </select>
                        <small class="text-danger">* leave it blank if data not spesific</small>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2" id="input_api">Api Access</label>
                        <select class="form-select form-select-solid mb-3 mb-lg-0" name="api" id="input_api" data-control="select2" data-placeholder="Select an option" required>
                            <option value=""></option>
                            <option value="1">YES</option>
                            <option value="0">NO</option>
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2" id="input_web">Web Access</label>
                        <select class="form-select form-select-solid mb-3 mb-lg-0" name="web" id="input_web" data-control="select2" data-placeholder="Select an option" required>
                            <option value=""></option>
                            <option value="1">YES</option>
                            <option value="0">NO</option>
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2" id="input_status">Status</label>
                        <select class="form-select form-select-solid mb-3 mb-lg-0" name="status" id="input_status" data-control="select2" data-placeholder="Select an option" required>
                            <option value=""></option>
                            <option value="1">ACTIVE</option>
                            <option value="0">NON-ACTIVE</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex-end gap-2">
                <button type="button" class="btn btn-light btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary btn-submit">
                    <span class="indicator-label">Submit</span>
                    <span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function () {
        setSelect2("#form_data select[data-control=select2]");
        $("#form_data").submit(function (e) {
            e.preventDefault();
            submitData(this, "manage/client/save", showData, true, true);
        });
        $("#form_data input").keydown(function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if (keycode === 13) {
                e.preventDefault();
                $("#form_data .btn-submit").trigger("click");
            }
        });
        setSelections("#form_data select[id=input_penugasan]", "general/selection", "ref=penugasan", "{{ $row->penugasan_ids ?? -1 }}".split(","));
        $("#form_data select[name=api]").val("{{ $row->api ?? 1 }}").trigger("change");
        $("#form_data select[name=web]").val("{{ $row->web ?? 1 }}").trigger("change");
        $("#form_data select[name=status]").val("{{ $row->status ?? 1 }}").trigger("change");
    });
</script>