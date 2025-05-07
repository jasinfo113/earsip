<?php if (isset($component)) { $__componentOriginalcd81fe1fc89ee8ed66abd54d35e17714 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcd81fe1fc89ee8ed66abd54d35e17714 = $attributes; } ?>
<?php $component = App\View\Components\GeneralLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('general-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\GeneralLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="d-flex flex-column flex-root">
        
            <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
                <a href="javascript:void(0)" class="mb-5 pt-lg-10">
                    <img src="<?php echo e(config('app.placeholder.logo_bundle')); ?>" class="h-100px mb-5"
                        alt="<?php echo e(config('app.name', 'Logo')); ?>" />
                </a>
                <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                    <form method="post" class="form w-100" novalidate="novalidate" id="form_data" action="#"
                        onsubmit="return false">
                        <?php echo csrf_field(); ?>
                        <div class="text-center mb-10">
                            <h1 class="text-dark mb-3">LOGIN E-ARSIP</h1>
                        </div>
                        <div class="fv-row mb-4">
                            <label class="form-label fs-6 fw-bolder text-dark">Username</label>
                            <input class="form-control form-control-lg form-control-solid" type="text"
                                name="username" autocomplete="off" placeholder="Username" required />
                        </div>
                        <div class="fv-row mb-4">
                            <label class="form-label fw-bolder text-dark fs-6 mb-0">Password</label>
                            <div class="position-relative mb-3 password-visibility">
                                <input class="form-control form-control-lg form-control-solid" type="password"
                                    name="password" autocomplete="off" placeholder="Password" required />
                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2">
                                    <i class="bi bi-eye-slash fs-2"></i>
                                </span>
                            </div>
                        </div>
                        <div class="fv-row mb-10 field-captcha">
                            <label id="captcha" class="form-label fs-6 fw-bolder text-dark">Captcha</label>
                            <div class="d-flex align-items-center">
                                <a href="javascript:void(0)" class="btn btn-sm btn-secondary d-flex align-items-center"
                                    onclick="captchaRefresh(true)">
                                    <i class="fa fa-history"></i>
                                </a>
                                <div id="captcha_img"><?php echo captcha_img(); ?></div>
                                <input class="form-control form-control-lg form-control-solid" type="text"
                                    name="captcha" id="captcha" autocomplete="off" placeholder="Captcha" required />
                            </div>
                            <?php $__errorArgs = ['captcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-10">
                            <div class="fv-row fv-plugins-icon-container">
                                <label class="form-check form-check-custom form-check-solid form-check-inline"
                                    for="remember">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        value="1" />
                                    <span class="form-check-label fw-bold text-gray-700 fs-6"> Remember me</span>
                                </label>
                                <div class="fv-plugins-message-container invalid-feedback"></div>
                            </div>
                            <div>
                                <a href="https://pemadam.jakarta.go.id/sso/forgot-password/?client_id=sidamk-TXSTcc1goJWtSy0"
                                    class="link-primary fs-6 fw-bolder">Forgot Password
                                    ?</a>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="button" id="btn_submit" class="btn btn-lg btn-primary w-100 mb-5">
                                <span class="indicator-label">LOGIN</span>
                                <span class="indicator-progress">
                                    Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                            <div class="text-center text-muted text-uppercase fw-bolder mb-5">or</div>
                            <a href="https://pemadam.jakarta.go.id/sso/?client_id=sidamk-TXSTcc1goJWtSy0"
                                class="btn btn-flex flex-center btn-light btn-lg w-100 mb-5">
                                <i class="fa fa-key"></i> Continue with SSO
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        
    </div>

    <script src="<?php echo e(asset('assets/scripts/general.js?v=' . time())); ?>"></script>
    <script src="<?php echo e(asset('assets/scripts/auth/login.js?v=' . time())); ?>"></script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcd81fe1fc89ee8ed66abd54d35e17714)): ?>
<?php $attributes = $__attributesOriginalcd81fe1fc89ee8ed66abd54d35e17714; ?>
<?php unset($__attributesOriginalcd81fe1fc89ee8ed66abd54d35e17714); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcd81fe1fc89ee8ed66abd54d35e17714)): ?>
<?php $component = $__componentOriginalcd81fe1fc89ee8ed66abd54d35e17714; ?>
<?php unset($__componentOriginalcd81fe1fc89ee8ed66abd54d35e17714); ?>
<?php endif; ?>
<?php /**PATH /var/www/earsip/resources/views/auth/login.blade.php ENDPATH**/ ?>