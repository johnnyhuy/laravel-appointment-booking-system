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
		<div class="form-group">
			<label for="inputJobTitle">Job Title <span class="request__validate">(e.g. Crew Member, Manager)</span></label>
			<input name="title" type="text" id="inputJobTitle" class="form-control request__input" placeholder="Title" value="{{ old('title') }}" autofocus>
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
			<label for="inputPhone">Phone <span class="request__validate">(at least 10 characters)</span></label>
			<input name="phone" type="text" id="inputPhone" class="form-control request__input" placeholder="Phone" value="{{ old('phone') }}" autofocus>
		</div>
		<button class="btn btn-lg btn-primary btn-block btn--margin-top">Add Employee</button>
	</form>
</div>
<hr>
<div class="dash__block">
	<h1 class="dash__header dash__header--margin-top">Employees</h1>
	@if ($employees->count())
		<h4 class="main_description">A table of all employees within the business.</h4>
	    <table class="table no-margin">
	        <tr>
				<th class="table__id">ID</th>
				<th class="table__name table__left-solid">First Name</th>
				<th class="table__name">Last Name</th>
				<th class="table__name">Title</th>
				<th class="table__date">Date Created</th>
			</tr>
			@foreach ($employees as $employee)
				<tr>
					<td class="table__id">{{ $employee->id }}</td>
					<td class="table__name table__left-solid">{{ $employee->firstname }}</td>
					<td class="table__name table__left-dotted">{{ $employee->lastname }}</td>
					<td class="table__name table__left-dotted">{{ $employee->title }}</td>
					<td class="table__date table__left-dotted">{{ Time::parse($employee->created_at)->format('d/m/y') }}</td>
				</tr>
			@endforeach
	    </table>
	@else
		@include('shared.error_message_thumbs_down', [
			'message' => 'No employees found.',
			'subMessage' => 'Try add an employee using the form above.'
		])
	@endif
</div>
@endsection