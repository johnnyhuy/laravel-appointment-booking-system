@extends('layouts.master')

@section('content')
	<div class="container">
		<div class="header">
			<h1 class="header__title">Business Name</h1>
			<h3 class="header__subtitle">Booking System</h3>
		</div>
		<div class="block login">
			<form class="login__form" method="post" action="/register">
				<label for="inputFirstName">First Name</label>
				<input type="text" id="inputFirstName" class="form-control login__input" placeholder="First Name" name="firstName" required autofocus>
				<label for="inputLastName">Last Name</label>
				<input type="text" id="inputLastName" class="form-control login__input" placeholder="Last Name" name="lastName" required autofocus>
				<label for="inputUsername">Username</label>
				<input type="text" id="inputUsername" class="form-control login__input" placeholder="Username" name="username" required autofocus>
				<label for="inputPassword">Password</label>
				<input type="password" id="inputPassword" class="form-control login__input" placeholder="Password" name="password" required>
				<label for="inputPhone">Phone</label>
				<input type="text" id="inputPhone" class="form-control login__input" placeholder="Phone" name="phone" required autofocus>
				<label for="inputAddress">Address</label>
				<input type="text" id="inputAddress" class="form-control login__input" placeholder="Address" name="address" required autofocus>
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="submit" class="btn btn-lg btn-primary btn-block" value="Register"></input>
			</form>
		</div>
	</div>
@endsection