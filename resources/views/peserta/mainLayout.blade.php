<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Dashboard Peserta | Sistem Uji Kompetensi</title>
	<link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.ico') }}">
	<link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-icons/bootstrap-icons.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
</head>

<body class="peserta-page">
	@include('peserta.header')
	
	<div class="main-wrapper">
		<div class="row g-4">
		  @yield('content')
		</div>
	
		@include('peserta.footer')
	</div>

	@stack('scripts')
</body>

</html>
