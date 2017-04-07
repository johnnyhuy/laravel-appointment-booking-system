@extends('layouts.dashboard')

@section('content')
	<div class="dash__block">
		<h1 class="dash__header">Summary of Bookings</h1>
		<h4 class="main_description">Present latest bookings for the next 7 days.</h4>
		<div class="table-responsive dash__table-wrapper">
		    <table class="table table--no-margin dash__table">
		        <tr>
					<th class="table--id">ID</th>
					<th class="table--left-solid">Customer</th>
					<th class="table--time">Start</th>
					<th class="table--time">End</th>
					<th class="table--date">Date</th>
					<th class="table--time">Duration</th>
				</tr>
				@foreach ($latest as $booking)
					<tr>
						<td class="table--id">{{ $booking->id }}</td>
						<td class="table--left-solid">{{ $booking->customer->firstname . ' ' . $booking->customer->lastname }}</td>
						<td class="table--time table--left-dotted">{{ Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</td>
						<td class="table--time table--left-dotted">{{ Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
						<td class="table--date table--left-dotted">{{ Carbon\Carbon::parse($booking->date)->format('d/m/y') }}</td>
						<td class="table--time table--left-dotted">{{ gmdate('G:i', $booking->duration()) }}</td>
					</tr>
				@endforeach
		    </table>
		</div>
	</div>
	<hr>
	<div class="dash__block">
		<h1 class="dash__header">Employee Availability</h1>
		<h4 class="main_description">Show all employee availablity for the next 7 days.</h4>
		<div class="table-responsive dash__table-wrapper">
		    <table class="table table--no-margin dash__table">
		        <tr>
					<th class="table--right-solid">Employee Name</th>
					@for ($days = 1; $days <= 7; $days++)
						<th>{{ Carbon\Carbon::now()->addDays($days)->format('D d/m') }}</th>
					@endfor
				</tr>
				@foreach (App\Employee::all() as $employee)
					<tr>
						<td> {{$employee->firstname . ' ' . $employee->lastname}} </td>
						<td class="table--left-solid"> {{App\Employee::getEmployeeAvailability($employee->id, 'Monday')}} </td>
						<td> {{App\Employee::getEmployeeAvailability($employee->id, 'Tuesday')}} </td>
						<td> {{App\Employee::getEmployeeAvailability($employee->id, 'Wednesday')}} </td>
						<td> {{App\Employee::getEmployeeAvailability($employee->id, 'Thursday')}} </td>
						<td> {{App\Employee::getEmployeeAvailability($employee->id, 'Friday')}} </td>
						<td> {{App\Employee::getEmployeeAvailability($employee->id, 'Saturday')}} </td>
						<td> {{App\Employee::getEmployeeAvailability($employee->id, 'Sunday')}} </td>
					</tr>
				@endforeach
		    </table>
		</div>
	</div>
@endsection