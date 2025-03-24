<div class="d-flex align-items-center" id="kt_header_nav">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
        <h1 class="d-flex align-items-center text-white fw-bolder fs-3 my-1"><i class="{{ $m_icon ?? 'fa fa-globe' }} fs-3 me-2"></i>{{ $_title }}</h1>
        @if(isset($m_is_sub) && $m_is_sub)
            <span class="h-20px border-gray-200 border-start mx-4"></span>
            <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                <li class="breadcrumb-item text-white">{{ $m_name }}</li>
            </ul>
        @endif
    </div>
</div>

