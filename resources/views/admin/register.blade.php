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
				<div class="form-group">
					<label for="inputBusinessName">Business Name</label>
					<input name="businessname" type="text" id="inputBusinessName" class="form-control request__input" placeholder="Business Name" value="{{ old('businessname') }}" autofocus>
				</div>
				<div class="form-group">
					<label for="inputFirstName">First Name</label>
					<input name="firstname" type="text" id="inputFirstName" class="form-control request__input" placeholder="First Name" value="{{ old('firstname') }}" autofocus>
				</div>
				<div class="form-group">
					<label for="inputLastName">Last Name</label>
					<input name="lastname" type="text" id="inputLastName" class="form-control request__input" placeholder="Last Name" value="{{ old('lastname') }}" autofocus>
				</div>
				<div class="form-group">
					<label for="inputUsername">Username <span class="request__validate">(alpha-numeric characters only)</span></label>
					<input name="username" type="text" id="inputUsername" class="form-control request__input" placeholder="Username" value="{{ old('username') }}" autofocus>
				</div>
				<div class="form-group">
					<label for="inputPassword">Password <span class="request__validate">(at least 6 characters)</span></label>
					<input name="password" type="password" id="inputPassword" class="form-control request__input" placeholder="Password" value="">
				</div>
				<div class="form-group">
					<label for="inputPasswordConfirmation">Password Confirmation</label>
					<input name="password_confirmation" type="password" id="inputPasswordConfirmation" class="form-control request__input" placeholder="Password" value="">
				</div>
				<div class="form-group">
					<label for="inputPhone">Phone <span class="request__validate">(at least 10 characters)</span></label>
					<input name="phone" type="text" id="inputPhone" class="form-control request__input" placeholder="Phone" value="{{ old('phone') }}" autofocus>
				</div>
				<div class="form-group">
					<label for="inputAddress">Address <span class="request__validate">(at least 6 characters)</span></label>
					<input name="address" type="text" id="inputAddress" class="form-control request__input" placeholder="Address" value="{{ old('address') }}" autofocus>
				</div>
			</div>
			<button class="btn btn-lg btn-primary btn-block" href="/register">Register</a>
		</form>
	</div>
@endsection