@extends('layouts.dashboard')

@section('content')
<body class="dashboard">
	<div class="dash__block">
		<h1 class="dash__header">Edit Business Information</h1>
		<h4 class="dash__description">Update the Business Information</h4>
		@include('shared.session_message')
		@include('shared.error_message')
		<form class="request" method="POST" action="/admin/{{ $business->id }}" enctype="multipart/form-data">
			{{ method_field('PUT') }}
			{{ csrf_field() }}
			<div class="form-group">
				@if ($business->logo)
					<section class="logo-display">
						<img class="logo logo--small" alt="" src="{{ asset('storage/' . $business->logo) }}">
						<img class="logo" alt="" src="{{ asset('storage/' . $business->logo) }}">
						<img class="logo logo--large" alt="" src="{{ asset('storage/' . $business->logo) }}">
					</section>
				@endif
				<label for="business_logo">Business Logo<span class="request__validate"> (minimum 240x120 px)</span></label>
				<input name="logo" type="file" id="business_logo">
			</div>
			<div class="checkbox">
				<label>
					<input name="remove_logo" type="checkbox"> Remove Logo
				</label>
			</div>
			<div class="form-group">
				<label for="business_name">Business Name</label>
				<input name="business_name" type="text" id="business_name" class="form-control request__input" value="{{ $business->business_name }}">
			</div>
			<div class="form-group">
				<label for="business_first_name">First Name</label>
				<input name="firstname" type="text" id="business_first_name" class="form-control request__input" placeholder="First Name" value="{{ $business->firstname }}">
			</div>
			<div class="form-group">
				<label for="business_last_name">Last Name</label>
				<input name="lastname" type="text" id="business_last_name" class="form-control request__input" placeholder="Last Name" value="{{ $business->lastname }}">
			</div>
			<div class="form-group">
				<label for="inputPhone">Phone <span class="request__validate">(at least 10 characters)</span></label>
				<input name="phone" type="text" id="inputPhone" class="form-control request__input" placeholder="Phone" value="{{ $business->phone }}">
			</div>
			<div class="form-group">
				<label for="inputAddress">Address <span class="request__validate">(at least 6 characters)</span></label>
				<input name="address" type="text" id="inputAddress" class="form-control request__input" placeholder="Address" value="{{ $business->address }}">
			</div>
			<button class="btn btn-lg btn-primary btn-block btn--margin-top">Update</button>
		</form>
	</div>
</body>
@endsection