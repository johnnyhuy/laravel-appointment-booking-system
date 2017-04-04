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
				</tr>
				<?php 
					// TESTING DATA PLS IGNORE
				 	/*$booking->customer_id = 1;
				 	$booking->day = \Carbon\Carbon::now();
				 	$booking->booking_start_time = \Carbon\Carbon::now();
				 	$booking->booking_end_time = \Carbon\Carbon::now();
				 	$booking->save();*/
				 ?>
				@foreach(App\Booking::whereDate('booking_start_time', '<', Carbon\Carbon::now())->get() as $booking)
					<tr>
						<td>{{ $booking->id }}</td>
						<td>{{ $booking->customer_id }}</td>
						<td>{{ Carbon\Carbon::parse($booking->booking_start_time)->format('h:i A') }}</td>
						<td>{{ Carbon\Carbon::parse($booking->booking_end_time)->format('h:i A') }}</td>
						<td>{{ Carbon\Carbon::parse($booking->booking_start_time)->toDateString() }}</td>
					</tr>
				@endforeach
		    </table>
		</div>
	</div>
@endsection