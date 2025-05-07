<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <title><?php echo e(config('app.name', 'Laravel')); ?></title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="keywords" content="Dinas Penanggulangan Kebakaran dan Penyelamatan Provinsi DKI Jakarta" />
		<meta name="description" content="Selamat Datang di Website Resmi Dinas Penanggulangan Kebakaran dan Penyelamatan Provinsi DKI Jakarta" />
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<link rel="shortcut icon" href="<?php echo e(config('app.placeholder.favicon')); ?>" />
		<link rel="stylesheet" href="<?php echo e(asset('_theme/assets/plugins/global/plugins.bundle.css')); ?>" type="text/css" />
		<link rel="stylesheet" href="<?php echo e(asset('_theme/assets/css/style.bundle.css')); ?>" type="text/css" />
		<link rel="stylesheet" href="<?php echo e(asset('assets/styles/custom.css?v=' . time())); ?>" type="text/css" />
		<script src="<?php echo e(asset('_theme/assets/plugins/global/plugins.bundle.js')); ?>"></script>
		<script src="<?php echo e(asset('_theme/assets/js/scripts.bundle.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/scripts/jquery-validation-1.19.5/dist/jquery.validate.min.js')); ?>"></script>
		<script>
            var base_url = "<?php echo e(URL::to('/')); ?>/";
            var site_url = "<?php echo e(URL::to('/')); ?>/";
        </script>
    </head>
    <body>
        <?php echo e($slot); ?>

    </body>
</html>
<?php /**PATH /var/www/earsip/resources/views/layouts/general.blade.php ENDPATH**/ ?>