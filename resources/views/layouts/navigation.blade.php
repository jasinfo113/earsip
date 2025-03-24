<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
    <div class="aside-logo flex-column-auto" id="kt_aside_logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ config('app.placeholder.logo') }}" class="h-45px logo" alt="{{ config('app.name', 'Logo') }}" />
        </a>
        <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
            <span class="svg-icon svg-icon-1 rotate-180">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="black" />
                    <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="black" />
                </svg>
            </span>
        </div>
    </div>
    <div class="aside-menu flex-column-fluid">
        <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">
            <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true">
                @foreach (Auth::user()->privileges as $privilege)
                    <div class="menu-item">
                        <div class="menu-content pb-2">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">{{ $privilege->group }}</span>
                        </div>
                    </div>
                    @foreach ($privilege->menus as $menu)
                        @if (COUNT($menu->subs))
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion menu-parent-{{ $menu->id }}">
                                <span class="menu-link">
                                    <span class="menu-icon">
                                        <i class="{{ $menu->icon }}"></i>
                                    </span>
                                    <span class="menu-title">{{ $menu->name }}</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <div class="menu-sub menu-sub-accordion menu-active-bg">
                                    @foreach ($menu->subs as $sub)
                                        <div class="menu-item menu-sub-{{ $sub->id }}">
                                            <a class="menu-link" href="{{ url($sub->url) }}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">{{ $sub->name }}</span>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="menu-item menu-parent-{{ $menu->id }}">
                                <a class="menu-link" href="{{ url($menu->url) }}">
                                    <span class="menu-icon">
                                        <i class="{{ $menu->icon }}"></i>
                                    </span>
                                    <span class="menu-title">{{ $menu->name }}</span>
                                </a>
                            </div>
                        @endif
                    @endforeach
                @endforeach
                <div class="menu-item">
                    <div class="menu-content pb-2">
                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">Account</span>
                    </div>
                </div>
                <div class="menu-item menu-parent--1">
                    <a class="menu-link" href="{{ route('account.profile') }}">
                        <span class="menu-icon">
                            <i class="fa fa-user-circle"></i>
                        </span>
                        <span class="menu-title">My Profile</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('logout') }}">
                        <span class="menu-icon">
                            <i class="fa fa-sign-out-alt"></i>
                        </span>
                        <span class="menu-title">Logout</span>
                    </a>
                </div>
                <div class="menu-item d-none">
                    <div class="menu-content">
                        <div class="separator mx-1 my-4"></div>
                    </div>
                </div>
                <div class="menu-item d-none">
                    <a class="menu-link" href="{{ url('docs') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M16.95 18.9688C16.75 18.9688 16.55 18.8688 16.35 18.7688C15.85 18.4688 15.75 17.8688 16.05 17.3688L19.65 11.9688L16.05 6.56876C15.75 6.06876 15.85 5.46873 16.35 5.16873C16.85 4.86873 17.45 4.96878 17.75 5.46878L21.75 11.4688C21.95 11.7688 21.95 12.2688 21.75 12.5688L17.75 18.5688C17.55 18.7688 17.25 18.9688 16.95 18.9688ZM7.55001 18.7688C8.05001 18.4688 8.15 17.8688 7.85 17.3688L4.25001 11.9688L7.85 6.56876C8.15 6.06876 8.05001 5.46873 7.55001 5.16873C7.05001 4.86873 6.45 4.96878 6.15 5.46878L2.15 11.4688C1.95 11.7688 1.95 12.2688 2.15 12.5688L6.15 18.5688C6.35 18.8688 6.65 18.9688 6.95 18.9688C7.15 18.9688 7.35001 18.8688 7.55001 18.7688Z" fill="black" />
                                    <path opacity="0.3" d="M10.45 18.9687C10.35 18.9687 10.25 18.9687 10.25 18.9687C9.75 18.8687 9.35 18.2688 9.55 17.7688L12.55 5.76878C12.65 5.26878 13.25 4.8687 13.75 5.0687C14.25 5.1687 14.65 5.76878 14.45 6.26878L11.45 18.2688C11.35 18.6688 10.85 18.9687 10.45 18.9687Z" fill="black" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Changelog v0.0.1</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@isset($m_id)
<script type="text/javascript">
    $(document).ready(function() {
        @if (isset($m_is_sub) && $m_is_sub)
            if ($("#kt_aside .menu-sub-{{ $m_id }} a").length) {
                $("#kt_aside .menu-sub-{{ $m_id }} a").addClass("active");
                $("#kt_aside .menu-sub-{{ $m_id }} a").closest(".menu-sub-accordion").addClass("show");
                $("#kt_aside .menu-sub-{{ $m_id }} a").closest(".menu-accordion").addClass("show");
            }
        @else
            if ($("#kt_aside .menu-parent-{{ $m_id }} a").length) {
                $("#kt_aside .menu-parent-{{ $m_id }} a").addClass("active");
            }
        @endif
    });
</script>
@endisset