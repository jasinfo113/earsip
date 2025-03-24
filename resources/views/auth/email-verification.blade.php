<x-general-layout>
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed"
            style="background-image: url({{ config('app.placeholder.bg_bottom') }})">
            <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
                <a href="javascript:void(0)" class="mb-5 pt-lg-10">
                    <img src="{{ config('app.placeholder.logo_bundle') }}" class="h-100px mb-5"
                        alt="{{ config('app.name', 'Logo') }}" />
                </a>
                <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                    <div class="text-center mb-10">
                        <h1 class="text-dark mb-3">Email Terverifikasi</h1>
                        <div class="text-gray-400 fw-bold fs-4">
                            Terima kasih!<br />
                            Alamat email Anda telah berhasil terverifikasi.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-general-layout>
