@extends('layouts.dashboard')

@section('content')

<div class="dash__block">
	<h1 class="dash__header">Add Employee</h1>
	<h4 class="dash__description">Add a new employee to the system</h4>
	@if ($flash = session('message'))
		<div class="alert alert-success">
			{{ $flash }}	
		</div>
	@endif
	@if (count($errors))
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }}<br>
			@endforeach
		</div>
	@endif
	<form class="request" method="POST" action="/admin/employees">
		{{ csrf_field() }}
		<label for="inputTitle">Title</label>
		<input name="title" type="text" id="inputTitle" class="form-control request__input" placeholder="Title" value="{{old('title')}}" autofocus>
		<label for="inputFirstName">First Name</label>
		<input name="firstname" type="text" id="inputFirstName" class="form-control request__input" placeholder="First Name" value="{{old('firstname')}}" autofocus>
		<label for="inputLastName">Last Name</label>
		<input name="lastname" type="text" id="inputLastName" class="form-control request__input" placeholder="Last Name" value="{{old('lastname')}}" autofocus>
		<label for="inputPhone">Phone <span class="request__validate">(at least 10 characters)</span></label>
		<input name="phone" type="text" id="inputPhone" class="form-control request__input" placeholder="Phone" value="{{old('phone')}}" autofocus>
		<button class="btn btn-lg btn-primary btn-block btn--margin-top" href="/admin/employees">Register</button>
	</form>
</div>
@endsection