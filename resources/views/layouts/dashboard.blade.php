<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="../../favicon.ico">
	<title>{{ $business->business_name }}: Dashboard</title>
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body class="dashboard">
	<nav class="navbar navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				@php
					// Dynamic page titles
					$title = ": ";

					// Check url for title
					if (Request::is('admin')) {
						$title .= "Business Information";
					}
					elseif (Request::is('admin/summary')) {
						$title .= "Summary of Bookings";
					}
					elseif (Request::is('admin/history')) {
						$title .= "History";
					}
					elseif (Request::is('admin/roster')) {
						$title .= "Roster";
					}
					elseif (Request::is('admin/employees')) {
						$title .= "Employees";
					}
					else {
						// Else default
						$title = "";
					}
				@endphp
				<a class="navbar-brand" href="#">{{ $business->business_name . $title }}</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="#">Logged in as {{ $business->username }}</a></li>
					<li><a href="/logout">Logout</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-3 col-md-2 sidebar">
				<ul class="nav nav-sidebar">
					<li class="{{ Request::is('admin') ? 'active' : null }}"><a href="/admin">Information<span class="sr-only">(current)</span></a></li>
					<li class="{{ Request::is('admin/summary') ? 'active' : null }}"><a href="/admin/summary">Summary<span class="sr-only">(current)</span></a></li>
					<li class="{{ Request::is('admin/history') ? 'active' : null }}"><a href="/admin/history">History</a></li>
					<li class="{{ Request::is('admin/roster') ? 'active' : null }}"><a href="/admin/roster">Roster</a></li>
					<li class="{{ Request::is('admin/employees') ? 'active' : null }}"><a href="/admin/employees">Employees</a></li>
				</ul>
				<footer class="dashboard">LCJJ Development Team</footer>
			</div>
			<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 dash">
				@yield('content')
			</div>
		</div>
	</div>
	<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
</body>

</html>
