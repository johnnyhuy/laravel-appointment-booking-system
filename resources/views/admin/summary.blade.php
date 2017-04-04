@extends('layouts.dashboard')

@section('content')
	<div class="main__block">
		<h1 class="main__header">Summary of Bookings</h1>
		<h4 class="main_description">Present latest bookings.</h4>
		<div class="table-responsive main__table-wrapper">
		    <table class="table table--no-margin main__table">
		        <tr>
					<th class="table--id">ID</th>
					<th class="main__table--left-solid">Customer</th>
					<th class="table--time">Start</th>
					<th class="table--time">End</th>
					<th class="table--date">Date</th>
					<th class="table--time">Duration</th>
				</tr>
				@foreach (App\Booking::whereDate('booking_start_time', '>', Carbon\Carbon::now())->get() as $booking)
					<tr>
						<td class="table--id">{{ $booking->id }}</td>
						<td class="main__table--left-solid">{{ $booking->customer->firstname . ' ' . $booking->customer->lastname }}</td>
						<td class="table--time main__table--left-dotted">{{ Carbon\Carbon::parse($booking->booking_start_time)->format('H:i') }}</td>
						<td class="table--time main__table--left-dotted">{{ Carbon\Carbon::parse($booking->booking_end_time)->format('H:i') }}</td>
						<td class="table--date main__table--left-dotted">{{ Carbon\Carbon::parse($booking->booking_start_time)->toDateString() }}</td>
						<td class="table--time main__table--left-dotted">{{ gmdate('G:i', $booking->duration()) }}</td>
					</tr>
				@endforeach
		    </table>
		</div>
	</div>
	<div class="main__block">
		<h1 class="main__header">Employee Availability</h1>
		<h4 class="main_description">Show all employee availablity for the next 7 days.</h4>
		<div class="table-responsive main__table-wrapper">
		    <table class="table table--no-margin main__table">
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