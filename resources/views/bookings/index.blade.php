@extends('layouts.master')

@section('content')
	<div class="container">
		<div class="block">
			<table class="table">
				<tr>
					<th>Booking ID</th>
					<th>Start Time</th>
					<th>End Time</th>
				</tr>
				<?php
					$bookings = factory(\App\Booking::class, 20)->make([
						'customer_id' => Auth::user()->id,
					]);

					$i = 0;

					$customerID = Auth::user()->id;
				?>
				@foreach ($bookings as $booking)
					<tr>
						<td>{{ $i = $i + 1 }}</td>
						<td>{{ \Carbon\Carbon::parse($booking->booking_start_time)->toDayDateTimeString() }}</td>
						<td>{{ \Carbon\Carbon::parse($booking->booking_end_time)->toDayDateTimeString() }}</td>
					</tr>
				@endforeach
			</table>
		</div>
	</div>
@endsection