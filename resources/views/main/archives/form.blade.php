<div id="overlay"></div>
<form id="form_data" method="post" role="form" enctype="multipart/form-data" autocomplete="off" onsubmit="return false;">
    @csrf
    @if (isset($row->id))
        <input type="hidden" name="update" value="true" />
        <input type="hidden" name="id" value="{{ $row->id }}" />
    @else
        <input type="hidden" name="save" value="true" />
    @endif

    <div class="modal-dialog modal-dialog-centered mw-750px">
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
                        <div class="row g-9 mb-8">
                            <div class="col-md-6 fv-row">
                                <label class="required fw-bold fs-6 mb-2" id="input_name">Referensi Nomor Arsip</label>
                                <input type="number" class="form-control form-control-solid mb-3 mb-lg-0"
                                    name="ref_nomor" id="ref_nomor" value="{{ $row->ref_number ?? '' }}"
                                    placeholder="Referensi Nomer Arsip" />

                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="required fw-bold fs-6 mb-2" id="input_name">Tanggal Arsip</label>
                                <input type="date" class="form-control form-control-solid mb-3 mb-lg-0"
                                    name="date" id="date"
                                    value="{{ isset($row) && $row->date ? date('Y-m-d', strtotime($row->date)) : '' }}"
                                    placeholder="Tanggal Arsip" required />
                            </div>
                        </div>
                        <div class="row g-9 mb-8">
                            <div class="col-md-6 fv-row">
                                <label class="required fw-bold fs-6 mb-2" id="input_name">Nama Arsip</label>
                                <input type="text" class="form-control form-control-solid mb-3 mb-lg-0"
                                    name="title" id="title" value="{{ $row->title ?? '' }}"
                                    placeholder="Nama Arsip" required />
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="required fw-bold fs-6 mb-2" id="input_penugasan">Kategori</label>
                                <select class="form-select form-select-solid mb-3 mb-lg-0" name="category_id"
                                    id="category_id" data-control="select2" data-placeholder="Select an option"
                                    required>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-9 mb-8">
                            <div class="col-md-6 fv-row">
                                <label class="required fw-bold fs-6 mb-2" id="input_penugasan">Tags</label>
                                <select class="form-select form-select-solid mb-3 mb-lg-0" name="tag_ids[]"
                                    id="tag_ids" data-control="select2" data-placeholder="Select an option" multiple
                                    required>
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="required fw-bold fs-6 mb-2" id="input_penugasan">Lokasi</label>
                                <select class="form-select form-select-solid mb-3 mb-lg-0" name="location_id"
                                    id="location_id" data-control="select2" data-placeholder="Select an option"
                                    required>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-9 mb-8">
                            <div class="col-md-6 fv-row">
                                <label class="required fw-bold fs-6 mb-2" id="input_username">Keterangan Arsip</label>
                                <textarea class="form-control form-control-solid mb-3 mb-lg-0" name="keterangan" id="input_keterangan"
                                    placeholder="keterangan" required>{{ $row->description ?? '' }}</textarea>
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="required fw-bold fs-6 mb-2" id="input_username">Catatan Arsip</label>
                                <textarea class="form-control form-control-solid mb-3 mb-lg-0" name="note" id="input_note"
                                    placeholder="Catatan Arsip" required>{{ $row->note ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2" for="file">File Arsip <span
                                    class="text-danger">*</span></label>
                            @if (!empty($row->document_files[0]->name))
                                <div class="mb-2">
                                    <a href="{{ asset('storage/main/arsip/' . $row->document_files[0]->name) }}"
                                        target="_blank" class="text-primary">
                                        Lihat File
                                    </a>
                                </div>
                            @endif

                            <input type="file" class="form-control form-control-solid mb-3 mb-lg-0" name="file"
                                id="file" accept="application/pdf" {{ empty($row->id) ? 'required' : '' }} />
                        </div>


                        @isset($status)
                            <div class="fv-row mb-7">
                                <label class="required fw-bold fs-6 mb-2" id="input_status">Status</label>
                                <select class="form-select form-select-solid mb-3 mb-lg-0" name="status_id"
                                    id="input_status" data-control="select2" data-placeholder="Select an option"
                                    required>
                                    <option value=""></option>
                                    @foreach ($status as $r)
                                        <option value="{{ $r->id }}"
                                            {{ isset($row) && $row->status_id == $r->id ? 'selected' : '' }}>
                                            {{ $r->name }}
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
        setSelect2("#form_data select[data-control=select2]");
        $("#form_data").submit(function(e) {
            e.preventDefault();
            submitDataarsip(this, "main/archives/save", showData, true, true);
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


        @if (isset($row->id))
            setAjaxSelections(
                "#form_data select[id=category_id]",
                "general/selection",
                "ref=category", "{{ $row->category_id ?? '' }}");
        @else
            setAjaxSelections("#form_data select[id=category_id]", "general/selection", "ref=category");
        @endif

        @if (isset($row->id))
            setAjaxSelections(
                "#form_data select[id=tag_ids]",
                "general/selection",
                "ref=tags",
                {!! json_encode(explode(',', $row->tag_ids ?? '')) !!}
            );
        @else
            setAjaxSelections("#form_data select[id=tag_ids]", "general/selection", "ref=tags");
        @endif

        @if (isset($row->id))
            setAjaxSelections(
                "#form_data select[id=location_id]",
                "general/selection",
                "ref=lokasi",
                "{{ $row->location_id ?? '' }}"
            );
        @else
            setAjaxSelections("#form_data select[id=location_id]", "general/selection", "ref=lokasi");
        @endif



    });
</script>




