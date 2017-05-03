@extends('layouts.dashboard')

@section('content')

<div class="dash__block">
	<h1 class="dash__header">Assign Employee</h1>
	<h4 class="dash__description">Assign an employee to a particular booking. All available bookings are display for the next 30 days.</h4>
	@if ($flash = session('message'))
		<div class="alert alert-success">
			{{ $flash }}
		</div>
	@endif
	@if (!App\Employee::first())
		@include('shared.error_message_custom', [
			'title' => 'Employees do not exist.',
			'message' => 'Create an employee <a href="/admin/employees">here</a>.',
			'type' => 'danger'
		])
	@endif
	@if (count($errors))
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }}<br>
			@endforeach
		</div>
	@endif
	<form class="request" method="POST" action="/admin/employees/assign">
		{{ csrf_field() }}
		<div class="form-group">
			<label for="inputEmployee">Employee <span class="request__validate">(Title - Full Name - ID)</span></label>
			<select name="employee_id" id="inputEmployee" class="form-control request__input" onchange="location = '/admin/employees/assign/' + this.value" value="{{ $selectedEmployee->id }}">
				@foreach ($employees as $employee)
					<option value="{{ $employee->id }}" {{ $employee->id == $selectedEmployee->id ? 'selected' : null }}> {{ $employee->title . ' - ' . $employee->firstname . ' ' . $employee->lastname . ' - ' . $employee->id }}</option>
				@endforeach
			</select>
		</div>
		@if ($bookings)
			<label>Select which bookings to assign the employee to <span class="request__validate">(only bookings which the employee is available to work are shown)</span></label>
			<div class="table-responsive dash__table-wrapper">
			    <table class="table table--no-margin dash__table">
			        <tr>
						<th class="table--id">Select</th>
						<th class="table--left-solid">Customer</th>
						<th class="table--time">Start</th>
						<th class="table--time">End</th>
						<th class="table--date">Date</th>
						<th class="table--time">Duration</th>
						<th class="table--name">Employee Assigned</th>
					</tr>
					@foreach ($bookings as $booking)
						<tr>
							<td class="table--id">
								@if (!$booking->employee)
									<input name="bookings[]" id="input_bookings" value="{{ $booking->id }}" type="checkbox"></input>
								@endif
							</td>
							<td class="table--name">{{ $booking->customer->firstname . ' ' . $booking->customer->lastname }}</td>
							<td class="table--time table--left-dotted">{{ Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</td>
							<td class="table--time table--left-dotted">{{ Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
							<td class="table--date table--left-dotted">{{ Carbon\Carbon::parse($booking->date)->format('d/m/y') }}</td>
							<td class="table--time table--left-dotted">{{ gmdate('G:i', $booking->duration()) }}</td>
							@if ($booking->employee)
								<td class="table--left-solid">{{ $booking->employee->firstname . ' ' . $booking->employee->lastname }}</td>
							@else
								<td class="table--left-solid"> </td>
							@endif
						</tr>
					@endforeach
			    </table>
			</div>
			<button class="btn btn-lg btn-primary btn-block" href="/admin/employees/assign">Assign Employee</button>
		@else
			@include('shared.error_message_thumbs_down', [
				'message' => 'No bookings found.',
				'subMessage' => 'There are no bookings available for ' . $selectedEmployee->firstname . ' ' . $selectedEmployee->lastname . ' (' . $selectedEmployee->id . ')<br><br>Add a working time for this employee <a href="/admin/roster">here</a>'
			])
		@endif
	</form>
</div>
<hr>
@if ($unassignBookings->count())
	<div class="dash__block" id="bookings">
		<h1 class="dash__header dash__header--margin-top">All Unassigned Bookings</h1>
		<h4 class="main_description">Present all unassigned bookings.</h4>
		<div class="alert alert-warning">
			<strong>Warning!</strong> Employee must be working during a booking time. Add a working time <a href="/admin/roster">here</a>.
		</div>
		<div class="table-responsive dash__table-wrapper">
		    <table class="table table--no-margin dash__table">
		        <tr>
					<th class="table--name">Customer</th>
					<th class="table--name">Activity</th>
					<th class="table--time">Start</th>
					<th class="table--time">End</th>
					<th class="table--date">Date</th>
					<th class="table--time">Duration</th>
				</tr>
				@foreach ($unassignBookings as $booking)
					<tr>
						<td class="table--name">{{ $booking->customer->firstname . ' ' . $booking->customer->lastname }}</td>
						<td class="table--time table--left-dotted">{{ $booking->activity->name }}</td>
						<td class="table--time table--left-dotted">{{ $booking->start_time }}</td>
						<td class="table--time table--left-dotted">{{ $booking->end_time }}</td>
						<td class="table--date table--left-dotted">{{ Carbon\Carbon::parse($booking->date)->format('d/m/y') }}</td>
						<td class="table--time table--left-dotted">{{ $booking->activity->duration }}</td>
					</tr>
				@endforeach
		    </table>
		</div>
	</div>
@else
	@include('shared.notice_glyphicon', [
		'glyphicon' => 'thumbs-up',
		'message' => 'No unassigned bookings found.',
		'subMessage' => 'Congratulations! There are no more unassigned bookings for the next 30 days'
	])
@endif
@endsection