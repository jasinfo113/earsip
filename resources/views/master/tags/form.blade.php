<div id="overlay"></div>
<form id="form_data" method="post" role="form" enctype="multipart/form-data" autocomplete="off" onsubmit="return false;">
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
                    <div class="field-user ">
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2" id="input_name">Tag</label>
                            <input type="text" class="form-control form-control-solid mb-3 mb-lg-0" name="tag"
                                id="input_name" value="{{ $row->name ?? '' }}" placeholder="Tag" required />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2" for="input_label">Label</label>
                            <select class="form-select form-select-solid mb-3 mb-lg-0" name="label" id="input_label"
                                data-control="select2" data-placeholder="Select an option" required>
                                <option value="">Select an option</option>
                                <option value="primary" class="text-primary"
                                    {{ ($row->label ?? '') == 'primary' ? 'selected' : '' }}>Biru</option>
                                <option value="success" class="text-success"
                                    {{ ($row->label ?? '') == 'success' ? 'selected' : '' }}>Hijau</option>
                                <option value="danger" class="text-danger"
                                    {{ ($row->label ?? '') == 'danger' ? 'selected' : '' }}>Merah</option>
                                <option value="warning" class="text-warning"
                                    {{ ($row->label ?? '') == 'warning' ? 'selected' : '' }}>Kuning</option>
                                <option value="info" class="text-info"
                                    {{ ($row->label ?? '') == 'info' ? 'selected' : '' }}>Biru Muda</option>
                                <option value="secondary" class="text-secondary"
                                    {{ ($row->label ?? '') == 'secondary' ? 'selected' : '' }}>Abu-abu</option>
                                <option value="dark" class="text-dark"
                                    {{ ($row->label ?? '') == 'dark' ? 'selected' : '' }}>Hitam</option>
                            </select>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2" id="input_username">keterangan</label>
                            <textarea class="form-control form-control-solid mb-3 mb-lg-0" name="keterangan" id="input_keterangan"
                            placeholder="keterangan" required>{{ $row->description ?? '' }}</textarea>
                        </div>

                        @isset($status)
                            <div class="fv-row mb-7">
                                <label class="required fw-bold fs-6 mb-2" id="input_status">Status</label>
                                <select class="form-select form-select-solid mb-3 mb-lg-0" name="status_id"
                                    id="input_status" data-control="select2" data-placeholder="Select an option" required>
                                    <option value=""></option>
                                    @foreach ($status as $r)
                                        <option value="{{ $r['id'] }}"
                                            {{ $row->status == $r['id'] ? 'selected' : '' }}>
                                            {{ $r['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endisset

                    </div>
                </div>
                <div class="modal-footer flex-end gap-2">
                    <button type="button" class="btn btn-light btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-submit">
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

        $("#form_data").submit(function(e) {
            e.preventDefault();
            submitData(this, "master/tags/save", showData, true, true);
        });

        $("#form_data input").keydown(function(e) {
            if ($(this).attr("type") === "file") return;
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if (keycode === 13) {
                e.preventDefault();
                if (_pass.getScore() >= _passMin) {
                    $("#form_data .btn-submit").trigger("click");
                }
            }
        });
        @isset($status)
            $("#form_data select[name=status]").val("{{ $row->id ?? 1 }}").trigger("change");
        @endisset

    });
</script>
