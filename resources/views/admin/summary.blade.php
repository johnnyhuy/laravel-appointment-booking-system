@extends('layouts.dashboard')

@section('content')
	<div class="main__block">
		<h1 class="main__header">Summary of Bookings</h1>
		<h4 class="main_description">Present latest bookings.</h4>
		<div class="table-responsive">
		    <table class="table main__table">
		        <tr>
					<th>Booking ID</th>
					<th>Customer ID</th>
					<th>Start time</th>
					<th>End time</th>
					<th>Date</th>
					<th>Duration</th>
				</tr>
				@foreach(App\Booking::whereDate('booking_start_time', '>', Carbon\Carbon::now())->get() as $booking)
					<tr>
						<td>{{ $booking->id }}</td>
						<td>{{ $booking->customer_id }}</td>
						<td>{{ Carbon\Carbon::parse($booking->booking_start_time)->format('h:i A') }}</td>
						<td>{{ Carbon\Carbon::parse($booking->booking_end_time)->format('h:i A') }}</td>
						<td>{{ Carbon\Carbon::parse($booking->booking_start_time)->toDateString() }}</td>
						<td>{{ gmdate('G:i', $booking->duration()) }}</td>
					</tr>
				@endforeach
		    </table>
		</div>
	</div>
	<div class="main__block">
		<h1 class="main__header">Employee Availability</h1>
		<h4 class="main_description">Show all employee availablity for the next 7 days.</h4>
		<div class="table-responsive">
		    <table class="table main__table">
		        <tr>
					<th>Employee Name</th>
					<th>Monday</th>
					<th>Tuesday</th>
					<th>Wednesday</th>
					<th>Thursday</th>
					<th>Friday</th>
					<th>Saturday</th>
					<th>Sunday</th>
				</tr>
				@foreach(DB::table('employees')->get() as $employee)
					<tr>
						<td> {{$employee->firstname . ' ' . $employee->lastname}} </td>
						<td> {{App\Employee::getEmployeeAvailability($employee->id, 'Monday')}} </td>
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