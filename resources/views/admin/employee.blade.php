@extends('layouts.dashboard')

@section('content')
	<div class="main__block">
		<h1 class="main__header">Add Employee</h1>
		<h4 class="main_description">Add a new employee to the system.</h4>
			<div class="container">
		@if (count($errors))
			<div class="alert alert-danger">
				@foreach ($errors->all() as $error)
					{{ $error }}<br>
				@endforeach
			</div>
		@endif
		<form method="POST" action="/employee">
			<div class="block request">
				{{ csrf_field() }}
				<label for="inputTitle"> Title</label>
				<select name="title" id="inputTitle" class="form-control request__input">
			    <option value="Mr" selected>Mr</option>
			    <option value="Mrs">Mrs</option>
			    <option value="Miss">Miss</option>
			    <option value="Ms">Ms</option>
			  	</select>
				<label for="inputFirstName">First Name</label>
				<input name="firstName" type="text" id="inputFirstName" class="form-control request__input" placeholder="First Name"  value="{{old('firstname')}}" autofocus>
				<label for="inputLastName">Last Name</label>
				<input name="lastName" type="text" id="inputLastName" class="form-control request__input" placeholder="Last Name"  value="{{old('lastname')}}" autofocus>
				<label for="inputPhone">Phone</label>
				<input name="phone" type="text" id="inputPhone" class="form-control request__input" placeholder="Phone"  value="{{old('phone')}}" autofocus>
			</div>
			<button class="btn btn-lg btn-primary btn-block" href="/register">Add Employee</a>
		</form>
	</div>
	</div>
@endsection