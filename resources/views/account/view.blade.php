<x-app-layout>
    <div class="d-flex flex-column flex-xl-row">
        <div class="flex-column flex-lg-row-auto w-100 w-xl-350px mb-10">
            <div class="card mb-5 mb-xl-8">
                <div class="card-body">
                    <div class="d-flex flex-center flex-column py-5">
                        <div class="symbol symbol-100px symbol-circle mb-7">
                            <img src="{{ Auth::user()->photo }}" alt="Photo Profile" />
                        </div>
                        <a href="javascript:void(0)"
                            class="fs-3 text-gray-800 text-hover-primary fw-bolder mb-3">{{ Auth::user()->name }}</a>
                        <div class="mb-9">
                            <div class="badge badge-lg badge-light-primary d-inline">{{ Auth::user()->role }}</div>
                        </div>
                    </div>
                    <div class="d-flex flex-stack fs-4 py-3">
                        <div class="fw-bolder rotate collapsible" data-bs-toggle="collapse" href="#kt_user_view_details"
                            role="button" aria-expanded="false" aria-controls="kt_user_view_details">
                            Details
                            <span class="ms-2 rotate-180">
                                <span class="svg-icon svg-icon-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z"
                                            fill="black" />
                                    </svg>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="separator"></div>
                    <div id="kt_user_view_details" class="collapse show">
                        <div class="pb-5 fs-6">
                            <div class="fw-bolder mt-5">Username</div>
                            <div class="text-gray-600">{{ Auth::user()->username }}</div>
                            <div class="fw-bolder mt-5">Email</div>
                            <div class="text-gray-600">
                                <a href="javascript:void(0)"
                                    class="text-gray-600 text-hover-primary">{{ Auth::user()->email }}</a>
                                @if (Auth::user()->email_verified_at)
                                    <span class="badge badge-success">Verified</span>
                                @endif
                            </div>
                            <div class="fw-bolder mt-5">Phone</div>
                            <div class="text-gray-600">
                                {{ Auth::user()->phone }}
                                @if (Auth::user()->phone_verified_at)
                                    <span class="badge badge-success">Verified</span>
                                @endif
                            </div>
                            <div class="fw-bolder mt-5">Status</div>
                            <div class="text-gray-600"><?php echo Auth::user()->status; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-lg-row-fluid ms-lg-15">
            <form id="form_profile" class="m-form m-form--label-align-left- m-form--state-" method="post"
                role="form" autocomplete="off" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="file" name="photo" class="userfile d-none" default="{{ Auth::user()->photo }}"
                    onchange="setImagePreview(this, '#form_profile img.image-input-wrapper')"
                    accept=".png, .jpg, .jpeg" />
                <div class="card mb-5 mb-xl-10">
                    <div class="card-header border-0">
                        <div class="card-title m-0">
                            <h3 class="fw-bolder m-0">Profile Details</h3>
                        </div>
                    </div>
                    <div class="card-body border-top p-9">
                        <div class="image-input image-input-outline mb-6">
                            <div class="d-flex cursor-pointer position-relative w-125px"
                                onclick="$('#form_profile .userfile').click()">
                                <img class="image-input-wrapper h-auto cursor-pointer"
                                    src="{{ Auth::user()->photo }}" />
                                <label
                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow position-absolute right-0 end-0">
                                    <i class="bi bi-pencil-fill fs-7"></i>
                                </label>
                            </div>
                            <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-bold fs-6" id="input_name">Name</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" class="form-control form-control-lg form-control-solid"
                                    name="name" id="input_name" placeholder="Name" value="{{ Auth::user()->name }}"
                                    required />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-bold fs-6"
                                id="input_username">Username</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text"
                                    class="form-control form-control-lg form-control-solid text-lowercase"
                                    name="username" id="input_username" onkeyup="removeSpace(this)"
                                    placeholder="Username" value="{{ Auth::user()->username }}" required />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-bold fs-6" id="input_email">Email</label>
                            <div class="col-lg-8 fv-row">
                                <input type="email" class="form-control form-control-lg form-control-solid"
                                    name="email" id="input_email" placeholder="Email"
                                    value="{{ Auth::user()->email }}" required />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-bold fs-6"
                                id="input_phone">Phone</label>
                            <div class="col-lg-8 fv-row">
                                <input type="number" class="form-control form-control-lg form-control-solid"
                                    name="phone" id="input_phone" placeholder="Phone"
                                    value="{{ Auth::user()->phone }}" required />
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="submit" class="btn btn-primary btn-submit">Save Changes</button>
                    </div>
                </div>
            </form>

            <form id="form_password" method="post" role="form" autocomplete="off" onsubmit="return false;">
                @csrf
                <div class="card mb-5 mb-xl-10">
                    <div class="card-header border-0">
                        <div class="card-title m-0">
                            <h3 class="fw-bolder m-0">Change Password</h3>
                        </div>
                    </div>
                    <div class="card-body border-top p-9">
                        <div class="fv-row mb-10">
                            <label class="form-label fw-bolder text-dark fs-6" id="password_current">Current
                                Password</label>
                            <input type="password" class="form-control form-control-lg form-control-solid"
                                name="password_current" id="password_current" placeholder="Password Saat Ini"
                                autocomplete="off" required />
                        </div>
                        <div class="mb-10 fv-row" data-kt-password-meter="true">
                            <div class="mb-1">
                                <label class="form-label fw-bolder text-dark fs-6" id="password">Password</label>
                                <div class="position-relative mb-3">
                                    <input class="form-control form-control-lg form-control-solid" type="password"
                                        name="password" id="password" placeholder="Password Baru"
                                        autocomplete="off" required />
                                    <span
                                        class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                        data-kt-password-meter-control="visibility">
                                        <i class="bi bi-eye-slash fs-2"></i>
                                        <i class="bi bi-eye fs-2 d-none"></i>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center mb-3"
                                    data-kt-password-meter-control="highlight">
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                </div>
                            </div>
                            <div class="text-muted">Use 8 or more characters with a mix of letters, numbers &amp;
                                symbols.</div>
                        </div>
                        <div class="fv-row mb-10">
                            <label class="form-label fw-bolder text-dark fs-6" id="password_confirmation">Confirm
                                Password</label>
                            <input class="form-control form-control-lg form-control-solid" type="password"
                                name="password_confirmation" id="password_confirmation"
                                placeholder="Konfirmasi Password Baru" autocomplete="off" required />
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="reset"
                            class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancel</button>
                        <button type="button" class="btn btn-primary btn-submit">Update Password</button>
                    </div>
                </div>
            </form>

            <!--
            <form class="d-none" id="form_deactivate" method="post" role="form" autocomplete="off" onsubmit="return false;">
                @csrf
                <div class="card mb-5 mb-xl-10">
                    <div class="card-header border-0">
                        <div class="card-title m-0">
                            <h3 class="fw-bolder m-0">Deactivate Account</h3>
                        </div>
                    </div>
                    <div class="card-body border-top p-9">
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
                            <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                                    <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black" />
                                    <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black" />
                                </svg>
                            </span>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-bold">
                                    <h4 class="text-gray-900 fw-bolder">You Are Deactivating Your Account</h4>
                                    <div class="fs-6 text-gray-700">
                                        Your account will delete permanently and this process cannot be undone.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-check form-check-solid fv-row">
                            <input type="checkbox" class="form-check-input" name="deactivate" id="deactivate" value="1" required />
                            <label class="form-check-label fw-bold ps-2 fs-6" for="deactivate">I confirm my account deactivation</label>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="button" class="btn btn-danger fw-bold btn-submit">Deactivate Account</button>
                    </div>
                </div>
            </form>
            -->
        </div>
    </div>

    <script src="{{ asset('assets/scripts/account/profile.js?v=' . time()) }}"></script>
</x-app-layout>
