<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>{{ config('app.name', 'Laravel') }}</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="keywords" content="Dinas Penanggulangan Kebakaran dan Penyelamatan Provinsi DKI Jakarta" />
		<meta name="description" content="Selamat Datang di Website Resmi Dinas Penanggulangan Kebakaran dan Penyelamatan Provinsi DKI Jakarta" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<link rel="shortcut icon" href="{{ config('app.placeholder.favicon') }}" />
		<link rel="stylesheet" href="{{ asset('_theme/assets/plugins/global/plugins.bundle.css') }}" type="text/css" />
		<link rel="stylesheet" href="{{ asset('_theme/assets/css/style.bundle.css') }}" type="text/css" />
		<link rel="stylesheet" href="{{ asset('assets/styles/custom.css?v=' . time()) }}" type="text/css" />
		<script src="{{ asset('_theme/assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('_theme/assets/js/scripts.bundle.js') }}"></script>
        <script src="{{ asset('assets/scripts/jquery-validation-1.19.5/dist/jquery.validate.min.js') }}"></script>
		<script>
            var base_url = "{{ URL::to('/') }}/";
            var site_url = "{{ URL::to('/') }}/";
        </script>
    </head>
    <body>
        {{ $slot }}
    </body>
</html>
