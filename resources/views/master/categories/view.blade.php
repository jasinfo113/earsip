<x-app-layout>
    <form id="form_query" data="categori" method="post" autocomplete="off" onsubmit="return false">
        <div class="card card-xl-stretch mb-xl-8">
            <div class="card-header">
                <h3 class="card-title fw-bolder text-dark">
                    Data Kategori
                </h3>
                <div class="card-toolbar">
                    <div class="d-flex gap-2 justify-content-end" data-kt-user-table-toolbar="base">
                        @if (config('app.user_access.delete', 0) == 1)
                        <a href="javascript:void(0)" class="btn btn-sm btn-icon btn-danger" onclick="delTable('master/categories/del', showData)" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right" title="Hapus data">
                            <i class="fa fa-trash p-0"></i>
                        </a>
                        @endif
                        @if (config('app.user_access.create', 0) == 1)
                        <a href="javascript:void(0)" class="btn btn-sm btn-icon btn-primary" onclick="openForm('master/categories/form')" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right" title="Tambah data">
                            <i class="fa fa-plus p-0"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
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
                        <table id="table_data" class="datacategori display mobile-responsive table table-rounded table-row-bordered border border-gray-200 table-row-gray-200 g-2">
                            <thead>
                                <tr class="text-start fw-bolder fs-7 gs-0">
                                    <th class="w-30px mw-30px min-w-30px">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input type="checkbox" class="form-check-input" id="checkAll" value="0" onchange="checkedAll('#form_query', table, this.value)" />
                                    </div>
                                </th>
                                    <th class="w-80px mw-80px min-w-80px">#</th>
                                <th class="text-start w-40px mw-40px min-w-40px">No</th>
                                    <th class="text-start">Kategori</th>
                                    <th class="text-start">Keterangan</th>
                                    <th class="text-start">Gambar</th>
                                    <th class="text-center w-80px">Status</th>
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
