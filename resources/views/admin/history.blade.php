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
				@foreach(DB::table('bookings')->where('booking_start_time', '<' \Carbon\Carbon::now()) as $booking)
					<tr>
						<td> {{$booking->id }}</td>
						<td>{{ \Carbon\Carbon::parse($booking->booking_start_time)->toDayDateTimeString() }}</td>
						<td>{{ \Carbon\Carbon::parse($booking->booking_start_time)->toDayDateTimeString() }}</td>
						<td>{{ $booking->customer_id }}</td>
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
					<th>Employee ID</th>
					<th>Monday</th>
					<th>Tuesday</th>
					<th>Wednesday</th>
					<th>Thursday</th>
					<th>Friday</th>
					<th>Saturday</th>
					<th>Sunday</th>
				</tr>
		    </table>
		</div>
	</div>
@endsection