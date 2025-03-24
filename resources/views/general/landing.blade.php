<x-general-layout>   
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-column-fluid">
            <div class="d-flex flex-column flex-column-fluid text-center p-10 py-lg-15">
                <a href="javascript:void(0)" class="mb-5 pt-lg-10">
                    <img src="{{ config('app.placeholder.logo_bundle') }}" class="h-100px mb-5" alt="{{ config('app.name', 'Logo') }}" />
                </a>
                <div class="pt-lg-10 mb-10">
                    <h1 class="fw-bolder fs-2qx text-gray-800 mb-7">Welcome to<br/>SIAGA API Jakarta</h1>
                    <div class="fw-bold fs-3 text-muted mb-15">
                        Single platform for manage all content<br/>of Website and Mobile Apps
                    </div>
                    <div class="text-center">
                        <a href="{{ url('auth/login') }}" class="btn btn-lg btn-dark fw-bolder">Get Started</a>
                    </div>
                </div>
                <div class="d-flex flex-row-auto bgi-no-repeat bgi-position-x-center bgi-size-contain bgi-position-y-bottom min-h-100px min-h-lg-250px" style="background-image: url({{ config('app.placeholder.pos') }})">
                </div>
            </div>
        </div>
    </div>
</x-general-layout>