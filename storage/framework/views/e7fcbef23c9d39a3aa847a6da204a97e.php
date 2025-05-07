<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <title><?php echo $__env->yieldContent('title'); ?></title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="keywords" content="Dinas Penanggulangan Kebakaran dan Penyelamatan Provinsi DKI Jakarta" />
		<meta name="description" content="Selamat Datang di Website Resmi Dinas Penanggulangan Kebakaran dan Penyelamatan Provinsi DKI Jakarta" />
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<link rel="shortcut icon" href="<?php echo e(config('app.placeholder.favicon')); ?>" />
		<link rel="stylesheet" href="<?php echo e(asset('_theme/assets/css/style.bundle.css')); ?>" type="text/css" />
    </head>
    <body class="bg-body">
		<div class="d-flex flex-column flex-root">
			<div class="d-flex flex-column flex-center flex-column-fluid p-10">
				<img src="<?php echo e(asset('_theme/assets/media/illustrations/sketchy-1/18.png')); ?>" class="mw-100 mb-10 h-lg-450px" alt="Error 404" />
				<h1 class="fw-bold mb-10"><?php echo $__env->yieldContent('message'); ?></h1>
				<a href="<?php echo e(url()->previous()); ?>" class="btn btn-primary">Go Back</a>
			</div>
		</div>
    </body>
</html><?php /**PATH /var/www/earsip/resources/views/errors/layout.blade.php ENDPATH**/ ?>