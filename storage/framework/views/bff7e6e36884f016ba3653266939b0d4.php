<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <title><?php echo e($_title ?? config('app.name', 'Laravel')); ?> | E-ARSIP</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="keywords" content="Dinas Penanggulangan Kebakaran dan Penyelamatan Provinsi DKI Jakarta" />
		<meta name="description" content="Selamat Datang di Website Resmi Dinas Penanggulangan Kebakaran dan Penyelamatan Provinsi DKI Jakarta" />
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<link rel="shortcut icon" href="<?php echo e(config('app.placeholder.favicon')); ?>" />
		<link rel="stylesheet" href="<?php echo e(asset('assets/plugins/magnific-popup/dist/magnific-popup.css')); ?>" type="text/css" />
		<link rel="stylesheet" href="<?php echo e(asset('_theme/assets/plugins/custom/datatables/datatables.bundle.css')); ?>" type="text/css" />
		<link rel="stylesheet" href="<?php echo e(asset('_theme/assets/plugins/global/plugins.bundle.css')); ?>" type="text/css" />
		<link rel="stylesheet" href="<?php echo e(asset('_theme/assets/css/style.bundle.css')); ?>" type="text/css" />
		<link rel="stylesheet" href="<?php echo e(asset('assets/styles/custom.css?v=' . time())); ?>" type="text/css" />
		<script src="<?php echo e(asset('_theme/assets/plugins/global/plugins.bundle.js')); ?>"></script>
		<script src="<?php echo e(asset('_theme/assets/js/scripts.bundle.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/plugins/magnific-popup/dist/jquery.magnific-popup.min.js')); ?>"></script>
        <script src="<?php echo e(asset('_theme/assets/plugins/custom/datatables/datatables.bundle.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/scripts/jquery-validation-1.19.5/dist/jquery.validate.min.js')); ?>"></script>
		<script>
            var base_url = "<?php echo e(URL::to('/')); ?>/";
            var site_url = "<?php echo e(URL::to('/')); ?>/";
            var hostUrl = "<?php echo e(URL::to('/_theme/assets/')); ?>";
            var U_CRT = <?php echo e(config('app.user_access.create', 0)); ?>;
            var U_UPT = <?php echo e(config('app.user_access.update', 0)); ?>;
            var U_DLT = <?php echo e(config('app.user_access.delete', 0)); ?>;
            var U_EXP = <?php echo e(config('app.user_access.export', 0)); ?>;
            var U_APR = <?php echo e(config('app.user_access.approve', 0)); ?>;
        </script>
        <script src="<?php echo e(asset('assets/scripts/app.js?v=' . time())); ?>"></script>
    </head>
    <body class="header-fixed header-tablet-and-mobile-fixed aside-enabled aside-fixed">
		<div class="d-flex flex-column flex-root">
			<div class="page d-flex flex-row flex-column-fluid">
                <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
				<div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                    <?php echo $__env->make('layouts.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
					<div class="content bg-light d-flex flex-column flex-column-fluid" id="kt_content">
						<div class="post d-flex flex-column-fluid" id="kt_post">
							<div id="kt_content_container" class="container-xxl mw-100">
                        		<?php echo e($slot); ?>

							</div>
						</div>
					</div>
                    <?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
				</div>
			</div>
		</div>
		<div class="modal fade" id="form_dialog" tabindex="-1" aria-hidden="true" role="dialog"></div>
		<div class="modal fade" id="form_dialog_detail" tabindex="-1" aria-hidden="true" role="dialog"></div>
		<div id="loading" style="display:none;">
			<div class="icon-loading"><i class='fa fa-spinner fa-spin'></i></div>
			<h2 class="text-loading"> Loading . . . </h2>
		</div>
		<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
			<span class="svg-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="black" />
					<path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="black" />
				</svg>
			</span>
		</div>
    </body>
</html>
<?php /**PATH C:\laragon\www\pekerjaan\damkar\earsip\resources\views/layouts/app.blade.php ENDPATH**/ ?>