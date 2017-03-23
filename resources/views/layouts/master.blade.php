<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="../../favicon.ico">
	<title>Appointment Booking System</title>
	<link href="{{ asset('css/app.css') }}"" rel="stylesheet">
	<script>
		window.myToken =  <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
	</script>
</head>

<body>
	<div class="container">
		<ul class="nav nav-pills pull-left">
			<li role="presentation" class="{{ Request::is('/') ? 'active' : null }}""><a href="/">Home</a></li>
			<li role="presentation" class="{{ Request::is('bookings') ? 'active' : null }}"><a href="/bookings">Bookings</a></li>
		</ul>
		@if (Auth::check())
			<div class="pull-right user">
				Logged in as {{ Auth::user()->firstname }}
				<a href="/logout">Logout</a>
			</div>
		@endif
		<div class="clearfix"></div>
		@if ($flash = session('message'))
			<div class="alert alert-success">
				{{ $flash }}	
			</div>
		@endif
		@if ($flash = session('error'))
			<div class="alert alert-danger">
				{{ $flash }}	
			</div>
		@endif
		<div class="header">
			<a class="header__title" href="/">
				<h1>
				@if (\App\BusinessOwner::first())
					{{ \App\BusinessOwner::first()->business_name }}
				@else
					Business Placeholder
				@endif
				</h1>
			</a>
			<h3 class="header__subtitle">Booking System</h3>
		</div>
	</div>
	@yield('content')
	<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
</body>

</html>
