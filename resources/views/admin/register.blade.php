@extends('layouts.master')

@section('content')
<div class="dash__block">
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
					<label for="register_business_name">Business Name</label>
					<input name="business_name" type="text" id="register_business_name" class="form-control request__input" placeholder="Business Name" value="{{ old('business_name') }}" autofocus>
				</div>
				<div class="form-group">
					<label for="register_first_name">First Name</label>
					<input name="firstname" type="text" id="register_first_name" class="form-control request__input" placeholder="First Name" value="{{ old('firstname') }}" autofocus>
				</div>
				<div class="form-group">
					<label for="register_last_name">Last Name</label>
					<input name="lastname" type="text" id="register_last_name" class="form-control request__input" placeholder="Last Name" value="{{ old('lastname') }}" autofocus>
				</div>
				<div class="form-group">
					<label for="register_username">Username <span class="request__validate">(alpha-numeric characters only)</span></label>
					<input name="username" type="text" id="register_username" class="form-control request__input" placeholder="Username" value="{{ old('username') }}" autofocus>
				</div>
				<div class="form-group">
					<label for="register_con_password">Password <span class="request__validate">(at least 6 characters)</span></label>
					<input name="password" type="password" id="register_con_password" class="form-control request__input" placeholder="Password" value="">
				</div>
				<div class="form-group">
					<label for="register_password">Password Confirmation</label>
					<input name="password_confirmation" type="password" id="register_password" class="form-control request__input" placeholder="Password" value="">
				</div>
				<div class="form-group">
					<label for="register_phone">Phone <span class="request__validate">(at least 10 characters)</span></label>
					<input name="phone" type="text" id="register_phone" class="form-control request__input" placeholder="Phone" value="{{ old('phone') }}" autofocus>
				</div>
				<div class="form-group">
					<label for="register_address">Address <span class="request__validate">(at least 6 characters)</span></label>
					<input name="address" type="text" id="register_address" class="form-control request__input" placeholder="Address" value="{{ old('address') }}" autofocus>
				</div>
				<div class="form-group">
					<label for="register_temp_password">Temp Password <span class="request__validate">(sent to business owner on purchase of domain)</span></label>
					<input name="temp_password" type="password" id="register_temp_password" class="form-control request__input" placeholder="Password" autofocus>
				</div>
				<button class="btn btn-lg btn-primary btn-block margin-top-two" href="/register">Register</button>
			</div>
		</form>
	</div>
</div>
@endsection