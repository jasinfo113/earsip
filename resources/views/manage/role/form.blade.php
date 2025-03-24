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
                        <label class="required fw-bold fs-6 mb-2" id="input_name">Name</label>
                        <input type="text" class="form-control form-control-solid mb-3 mb-lg-0" name="name" id="input_name" value="{{ $row->name ?? "" }}" placeholder="Name" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2" id="input_description">Description</label>
                        <textarea class="form-control form-control-solid mb-3 mb-lg-0 " name="description" id="input_description" rows="3" placeholder="Description" required>{{ $row->description ?? "" }}</textarea>
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
                <button type="button" class="btn btn-primary btn-submit">
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
        $("#form_data select[name=status]").val("{{ $row->status ?? 1 }}").trigger("change");
        $("#form_data .btn-submit").click(function (e) {
            e.preventDefault();
            saveFormData("#form_data", "manage/user/role/save", showData, true, true);
        });
        $("#form_data input").keydown(function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if (keycode === 13) {
                e.preventDefault();
                if (_pass.getScore() >= _passMin) {
                    $("#form_data .btn-submit").trigger("click");
                }
            }
        });
    });
</script>