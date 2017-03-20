@extends('layouts.master')

@section('content')
	<div class="container">
		<div class="header">
			<h1 class="header__title">Business Name</h1>
			<h3 class="header__subtitle">Booking System</h3>
		</div>
		@if ($errors)
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		<form class="login__form" method="POST" action="/register">
			<div class="block login">
				{{ csrf_field() }}
				<label for="inputFirstName">First Name</label>
				<input name="firstname" type="text" id="inputFirstName" class="form-control login__input" placeholder="First Name" required autofocus>
				<label for="inputLastName">Last Name</label>
				<input name="lastname" type="text" id="inputLastName" class="form-control login__input" placeholder="Last Name" required autofocus>
				<label for="inputUsername">Username</label>
				<input name="username" type="text" id="inputUsername" class="form-control login__input" placeholder="Username" required autofocus>
				<label for="inputPassword">Password</label>
				<input name="password" type="password" id="inputPassword" class="form-control login__input" placeholder="Password" required>
				<label for="inputPasswordConfirmation">Password Confirmation</label>
				<input name="password_confirmation" type="password" id="inputPasswordConfirmation" class="form-control login__input" placeholder="Password" required>
				<label for="inputPhone">Phone</label>
				<input name="phone" type="text" id="inputPhone" class="form-control login__input" placeholder="Phone" required autofocus>
				<label for="inputAddress">Address</label>
				<input name="address" type="text" id="inputAddress" class="form-control login__input" placeholder="Address" required autofocus>
			</div>
			<button class="btn btn-lg btn-primary btn-block" href="/register">Register</a>
		</form>
	</div>
@endsection