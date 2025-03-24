<x-app-layout>
    <form id="form_query" method="post" autocomplete="off" onsubmit="return false">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title fw-bolder text-dark">Data {{ $_title }}</h3>
                <div class="card-toolbar">
                    <div class="d-flex gap-2 justify-content-end" data-kt-user-table-toolbar="base">
                        @if (config('app.user_access.create', 0) == 1)
                        <a href="javascript:void(0)" class="btn btn-sm btn-icon btn-primary" onclick="openForm('manage/user/role/form')" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right" title="Tambah data">
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
                                <input type="text" class="form-control form-control-solid pe-14" name="search" placeholder="Search . . ." />
                                <a href="javascript:void(0)" class="svg-icon svg-icon-1 position-absolute end-0 me-6" onclick="showData()">
                                    <i class="fa fa-search"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="table_data" class="datatable display mobile-responsive table table-rounded table-row-bordered border border-gray-200 table-row-gray-200 g-2">
                            <thead>
                            <tr class="text-start fw-bolder fs-7 gs-0">
                                <th class="w-40px">#</th>
                                <th class="text-start w-40px">No</th>
                                <th class="text-start">Name</th>
                                <th class="text-start">Description</th>
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

    <script src="{{ asset('assets/scripts/manage/role.js?v=' . time()) }}"></script>
</x-app-layout>


