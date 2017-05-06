@extends('layouts.dashboard')

@section('content')
<div class="dash__block">
	<h1 class="dash__header">Add Working Times</h1>
	<h4 class="dash__description">Add Business Hours for the month.</h4>
	<form class="request" method="POST" action="/admin/roster/{{ $dateString }}">
		{{ csrf_field() }}
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
		@if (!Employee::first())
			@include('shared.error_message_custom', [
				'title' => 'Employees do not exist.',
				'message' => 'Create an employee <a href="/admin/employees">here</a>.',
				'type' => 'danger'
			])
		@endif
		@include('shared.loading_message')
		<div class="form-group">
			<label for="input_employee">Employee <span class="request__validate">(Title - Full Name - ID)</span></label>
			<select name="employee_id" id="input_employee" class="form-control request__input" onchange="showRedirect('.loading', '/admin/roster/{{ $dateString }}/' + this.value)">
				@foreach (Employee::all()->sortBy('lastname')->sortBy('firstname')->sortBy('title') as $e)
					<option value="{{ $e->id }}" {{ old('employee_id') == $e->id || $employeeID == $e->id ? 'selected' : null }}>{{ $e->title . ' - ' . $e->firstname . ' ' . $e->lastname . ' - ' . $e->id }}</option>
				@endforeach
				<option value="" {{ old('employee_id') || $employeeID ? null : 'selected' }}>-- None --</option>
			</select>
		</div>
		<div class="form-group request__flex-container">
			<div class="request__flex request__flex--left">
				<label for="input_start_time">Start Time <span class="request__validate">(24 hour format)</span></label>
				<input name="start_time" type="time" id="input_start_time" class="form-control request__input" value="{{ old('start_time') ? old('start_time') : '09:00' }}" autofocus>
			</div>
			<div class="request__flex request__flex--right">
				<label for="input_end_time">End Time <span class="request__validate">(24 hour format)</span></label>
				<input name="end_time" type="time" id="input_end_time" class="form-control request__input" value="{{ old('end_time') ? old('end_time') : '17:00' }}" autofocus>
			</div>
		</div>
		<div class="form-group request__flex-container">
			<div class="request__flex request__flex--left">
				<label for="input_month_year">Month & Year <span class="request__validate">(Select to go to month)</span></label>
			    <select name="month_year" id="input_month_year" class="form-control request__input" onchange="showRedirect('.loading', '/admin/roster/' + this.value + '{{ $employeeID ? '/' . $employeeID : null }}')">
			        @foreach ($months as $month)
			            <option value="{{ $month->format('m-Y') }}" {{ $date->format('m-Y') == $month->format('m-Y') ? 'selected' : null }}>{{ $month->format('F Y') }}</option>
			        @endforeach
			    </select>
			</div>
			<div class="request__flex request__flex--right">
				<label for="inputDay">Day <span class="request__validate"></span></label>
			    <select name="day" id="inputMonthYear" class="form-control request__input">
			        @for ($day = 1; $day <= $date->endOfMonth()->day; $day++)
			            <option value="{{ $day }}" {{ old('day') == $day ? 'selected' : null }}>{{ $day }}</option>
			        @endfor
			    </select>
			</div>
		</div>
		<button class="btn btn-lg btn-primary btn-block btn--margin-top" href="/admin/employees">Add Working Time</button>
	</form>
</div>
<hr>
<div class="dash__block">
	<h1 class="dash__header dash__header--margin-top">Roster {{ $employee ? ' for ' . $employee->firstname . ' ' . $employee->lastname : null }}</h1>
	<h4 class="dash__description">Show the roster of a given month.</h4>
	<h1>{{ $date->format('F Y') }}</h1>
	@include('shared.calendar', [
		'pDate' => $date,
		'items' => $roster,
		'type' => 'admin'
	])
</div>
@endsection