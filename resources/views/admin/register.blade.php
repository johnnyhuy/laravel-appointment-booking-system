@extends('layouts.master')

@section('content')
	<div class="container">
		@if (count($errors))
			<div class="alert alert-danger">
				@foreach ($errors->all() as $error)
					{{ $error }}<br>
				@endforeach
			</div>
		@endif
		<form method="POST" action="/admin/register">
			<div class="block request">
				{{ csrf_field() }}
				<label for="inputBusinessName">Business Name</label>
				<input name="businessname" type="text" id="inputBusinessName" class="form-control request__input" placeholder="Business Name"  value="{{old('businessname')}}" autofocus>
				<label for="inputFullName">Full Name</label>
				<input name="fullname" type="text" id="inputFullName" class="form-control request__input" placeholder="Full Name"  value="{{old('fullname')}}" autofocus>
				<label for="inputUsername">Username</label>
				<input name="username" type="text" id="inputUsername" class="form-control request__input" placeholder="Username"  value="{{old('username')}}" autofocus>
				<label for="inputPassword">Password</label>
				<input name="password" type="password" id="inputPassword" class="form-control request__input" placeholder="Password"  value="{{old('password')}}">
				<label for="inputPasswordConfirmation">Password Confirmation</label>
				<input name="password_confirmation" type="password" id="inputPasswordConfirmation" class="form-control request__input" placeholder="Password"  value="{{old('password_confirmation')}}">
				<label for="inputPhone">Phone</label>
				<input name="phone" type="text" id="inputPhone" class="form-control request__input" placeholder="Phone"  value="{{old('phone')}}" autofocus>
				<label for="inputAddress">Address</label>
				<input name="address" type="text" id="inputAddress" class="form-control request__input" placeholder="Address"  value="{{old('address')}}" autofocus>
			</div>
			<button class="btn btn-lg btn-primary btn-block" href="/register">Register</a>
		</form>
	</div>
@endsection