@include('head.html')

<head>
	<title>Appointment Booking System</title>
	@include('head.meta')
	@include('head.css')
	@include('head.js')
	@include('head.other')
</head>

<body>
	<div class="container">
		@if (Auth::check())
		<ul class="nav nav-pills pull-left">
			<li role="presentation" class="{{ Request::is('/') ? 'active' : null }}"><a href="/">Home</a></li>
			@if (Auth::check())
				<li role="presentation" class="{{ Request::is('bookings') ? 'active' : null }}"><a href="/bookings">Bookings</a></li>
				<li role="presentation" class="{{ Request::is('bookings/new') ? 'active' : null }}"><a href="/bookings/new">Create Booking</a></li>
			@endif
		</ul>

			<div class="pull-right user">
				Logged in as {{ Auth::user()->username }}
				<a href="/logout">Logout</a>
			</div>
		@endif
		<div class="clearfix"></div>
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
			@php
				// Dynamic page titles
				$title = ": ";

				// Check url for title
				if (Request::is('bookings')) {
					$title .= "Customer Bookings";
				}
				elseif (Request::is('login')) {
					$title .= "Login";
				}
				elseif (Request::is('register')) {
					$title .= "Customer Registration";
				}
				elseif (Request::is('admin')) {
					$title .= "Admin";
				}
				elseif (Request::is('create_booking')) {
					$title .= "Create a Booking";
				}
				else {
					// Else default
					$title = "";
				}
			@endphp
			<h3 class="header__subtitle">Booking System{{ $title }}</h3>
		</div>
	</div>
	<div class="container">
		@if ($flash = session('message'))
			<div class="alert alert-success">
				{{ $flash }}
			</div>
		@endif
		@yield('content')
	</div>
	<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
	<footer>
		LCJJ Development Team
	</footer>
</body>

</html>
