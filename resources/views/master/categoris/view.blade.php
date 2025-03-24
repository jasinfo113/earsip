<x-app-layout>
    <form id="form_categori" data="categori" method="post" autocomplete="off" onsubmit="return false">
        <div class="card card-xl-stretch mb-xl-8">
            <div class="card-header">
                <h3 class="card-title fw-bolder text-dark">
                    Data Kategori
                    <div class="d-flex flex-column w-80px ms-4">
                        {{-- <div class="d-flex flex-stack mb-2">
                            <span class="text-muted me-2 fs-7 fw-bold">{{ _numdec($value) }}%</span>
                        </div>
                        <div class="progress h-6px w-100">
                            <div class="progress-bar bg-{{ $label }}" role="progressbar"
                                style="width:{{ $value }}%" aria-valuenow="{{ $value }}" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div> --}}
                    </div>
                </h3>
                {{-- <div class="card-toolbar">
                    <div class="d-flex gap-2 justify-content-end" data-kt-user-table-toolbar="base">
                        @if (config('app.user_access.export', 0) == 1)
                            <a href="javascript:void(0)" class="btn btn-sm btn-icon btn-success" onclick="exportData()"
                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click"
                                data-bs-placement="right" title="Export data">
                                <i class="fa fa-file-excel p-0"></i>
                            </a>
                        @endif
                    </div>
                </div> --}}
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- <div class="col-md-3">
                        <div class="form-group">
                            <label class="text-muted" id="filter_jenis">Jenis Pegawai</label>
                            <select class="form-select form-select-solid" name="jenis" id="filter_jenis"
                                data-control="select2" data-allow-clear="true" data-placeholder="Choose an Option"
                                onchange="showData('pegawai')">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="text-muted" id="filter_unit_kerja">Unit Kerja</label>
                            <select class="form-select form-select-solid" name="unit_kerja" id="filter_unit_kerja"
                                data-control="select2" data-allow-clear="true" data-placeholder="Choose an Option"
                                onchange="showData('pegawai')">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="text-muted" id="filter_sub_unit_kerja">Sub Unit Kerja</label>
                            <select class="form-select form-select-solid" name="sub_unit_kerja"
                                id="filter_sub_unit_kerja" data-control="select2" data-allow-clear="true"
                                data-placeholder="Choose an Option" onchange="showData('pegawai')">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="text-muted" id="filter_status">Status</label>
                            <select class="form-select form-select-solid" name="status" id="filter_status"
                                data-control="select2" data-allow-clear="true" data-placeholder="Choose an Option"
                                onchange="showData('pegawai')">
                                <option value=""></option>
                                <option value="1">Sudah Login</option>
                                <option value="0" selected>Belum Login</option>
                            </select>
                        </div>
                    </div> --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="d-flex align-items-center position-relative">
                                <input type="text" class="form-control form-control-solid pe-14" name="search"
                                    id="filter_search" placeholder="Search . . ." />
                                <a href="javascript:void(0)" class="svg-icon svg-icon-1 position-absolute end-0 me-6"
                                    onclick="showData('categori')">
                                    <i class="fa fa-search"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="table_data" data="categori"
                            class="datatable display mobile-responsive table table-rounded table-row-bordered border border-gray-200 table-row-gray-200 g-2">
                            <thead>
                                <tr class="text-start fw-bolder fs-7 gs-0">
                                    <th class="text-start w-40px mw-40px min-w-40px">No</th>
                                    <th class="text-start">Kategori</th>
                                    <th class="text-start">Keterangan</th>
                                    <th class="text-start">Gambar</th>
                                    <th class="text-start">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <script src="{{ asset('assets/scripts/master/categories.js?v=' . time()) }}"></script>
</x-app-layout>
