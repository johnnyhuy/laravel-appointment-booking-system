@extends('layouts.dashboard')

@section('content')
	<div class="main__block">
		<h1 class="main__header">History of Bookings</h1>
		<h4 class="main_description">Present older bookings.</h4>
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
				@foreach(DB::table('bookings')->whereDate('booking_start_time', '<', \Carbon\Carbon::now())->get() as $booking)
					<tr>
						<td> {{	$booking->id }}</td>
						<td>{{ \Carbon\Carbon::parse($booking->booking_start_time)->toDayDateTimeString() }}</td>
						<td>{{ \Carbon\Carbon::parse($booking->booking_start_time)->toDayDateTimeString() }}</td>
						<td>{{ $booking->customer_id }}</td>
					</tr>
				@endforeach
		    </table>
		</div>
	</div>
@endsection