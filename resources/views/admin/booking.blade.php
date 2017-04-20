@extends('layouts.dashboard')

@section('content')

<div class="dash__block">
	<h1 class="dash__header">Add Booking</h1>
	<h4 class="dash__description">Add a new booking to the system</h4>
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
	<form class="request" method="POST" action="/admin/booking">
		{{ csrf_field() }}
		<div class="form-group">
			<label for="inputCustomer">Customer <span class="request__validate">(ID - Full Name)</span></label>
			<select name="customer_id" id="inputCustomer" class="form-control request__input">
				@foreach (App\Customer::all()->sortBy('firstname')->sortBy('lastname') as $customer)
					<option value="{{ $customer->id }}">{{ $customer->id . ' - ' . $customer->firstname . ' ' . $customer->lastname }}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group">
			<label for="inputEmployee">Employee <span class="request__validate">(ID - Title - Full Name)</span></label>
			<select name="employee_id" id="inputEmployee" class="form-control request__input">
				@foreach (App\Employee::all()->sortBy('title')->sortBy('firstname')->sortBy('lastname') as $employee)
					<option value="{{ $employee->id }}">{{ $employee->id . ' - ' . $employee->title . ' - ' . $employee->firstname . ' ' . $employee->lastname }}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group">
			<label for="inputActivity">Activity <span class="request__validate">(ID - Name - Duration)</span></label>
			<select name="activity_id" id="inputActivity" class="form-control request__input">
				@foreach (App\Activity::all()->sortBy('name') as $activity)
					<option value="{{ $activity->id }}">{{ $activity->id . ' - ' . $activity->name . ' - ' . $activity->duration }}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group">
			<label for="inputStartTime">Start Time <span class="request__validate">(24 hour format)</span></label>
			<input name="start_time" type="time" id="inputStartTime" class="form-control request__input" value="{{ old('start_time') ? old('start_time') : '09:00' }}" autofocus>
		</div>
		<div class="form-group">
			<label for="inputDate">Date <span class="request__validate">(dd/mm/yyyy format)</span></label>
			<input name="date" type="date" id="inputDate" class="form-control request__input" value="{{ old('date') ? old('date') : Carbon\Carbon::now()->addMonth()->startOfMonth()->format('Y-m-d') }}" autofocus>
		</div>
		<button class="btn btn-lg btn-primary btn-block btn--margin-top">Add Booking</button>
	</form>
</div>
<hr>
<div class="dash__block">
	<h1 class="dash__header dash__header--margin-top">Bookings</h1>
	@if ($bookings->count())
		<h4 class="main_description">A table of latest bookings within the business.</h4>
		<div class="table-responsive dash__table-wrapper">
		    <table class="table table--no-margin dash__table">
		        <tr>
					<th class="table--id table--right-solid">ID</th>
					<th class="table--name">Customer</th>
					<th class="table--name">Employee</th>
					<th class="table--name">Activity</th>
					<th class="table--time">Start Time</th>
					<th class="table--time">End Time</th>
					<th class="table--date">Date</th>
				</tr>
				@foreach ($bookings as $booking)
					<tr>
						<td class="table--id table--right-solid">{{ $booking->id }}</td>
						<td class="table--name table--right-dotted">{{ $booking->customer->firstname . ' ' . $booking->customer->lastname }}</td>
						<td class="table--name table--right-dotted">{{ $booking->employee->firstname . ' ' . $booking->employee->lastname }}</td>
						<td class="table--name table--right-dotted">{{ $booking->activity->name }}</td>
						<td class="table--time table--right-dotted">{{ $booking->start_time }}</td>
						<td class="table--time table--right-dotted">{{ $booking->start_time }}</td>
						<td class="table--date">{{ Carbon\Carbon::parse($booking->date)->format('d/m/y') }}</td>
					</tr>
				@endforeach
		    </table>
		</div>
	@else
		<div class="notice">
			<span class="glyphicon glyphicon-thumbs-down notice__icon" aria-hidden="true"></span>
			<h1 class="notice__message">No bookings found.</h1>
			<h4 class="notice__description">Try add an employee using the form above.</h4>
		</div>
	@endif
</div>
@endsection