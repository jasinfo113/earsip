<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>Forbidden Access</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="keywords" content="Dinas Penanggulangan Kebakaran dan Penyelamatan Provinsi DKI Jakarta" />
		<meta name="description" content="Selamat Datang di Website Resmi Dinas Penanggulangan Kebakaran dan Penyelamatan Provinsi DKI Jakarta" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<link rel="shortcut icon" href="{{ config('app.placeholder.favicon') }}" />
		<link rel="stylesheet" href="{{ asset('_theme/assets/css/style.bundle.css') }}" type="text/css" />
    </head>
    <body class="bg-body">
		<div class="d-flex flex-column flex-root">
			<div class="d-flex flex-column flex-center flex-column-fluid p-10">
				<img src="{{ asset('_theme/assets/media/illustrations/sketchy-1/5.png') }}" class="mw-100 mb-10 h-lg-350px" alt="Error 403" />
				<h1 class="fw-bold mb-10">{{ $message }}</h1>
				<a href="{{ url()->previous() }}" class="btn btn-primary">Go Back</a>
			</div>
		</div>
    </body>
</html>