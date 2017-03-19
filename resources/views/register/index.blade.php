@extends('layouts.master')

@section('content')
	<div class="container">
		<div class="header">
			<h1 class="header__title">Business Name</h1>
			<h3 class="header__subtitle">Booking System</h3>
		</div>
		<div class="block login">
			<form class="login__form" method="POST" action="/login">
				<label for="inputFirstName">First Name</label>
				<input type="text" id="inputFirstName" class="form-control login__input" placeholder="First Name" required autofocus>
				<label for="inputLastName">Last Name</label>
				<input type="text" id="inputLastName" class="form-control login__input" placeholder="Last Name" required autofocus>
				<label for="inputUsername">Username</label>
				<input type="text" id="inputUsername" class="form-control login__input" placeholder="Username" required autofocus>
				<label for="inputPassword">Password</label>
				<input type="password" id="inputPassword" class="form-control login__input" placeholder="Password" required>
				<label for="inputPhone">Phone</label>
				<input type="text" id="inputPhone" class="form-control login__input" placeholder="Phone" required autofocus>
				<label for="inputEmail">Email Address</label>
				<input type="email" id="inputEmail" class="form-control login__input" placeholder="Email Address" required autofocus>
			</form>
		</div>
		<a class="btn btn-lg btn-primary btn-block" href="/register">Register</a>
	</div>
@endsection