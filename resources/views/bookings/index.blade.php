@extends('layouts.master')

@section('content')
	<div class="container">
		<div class="block block--no-padding">
			<table class="table table--no-margin customer_bookings">
				<tr>
					<th class="customer_bookings__left-solid">Booking ID</th>
					<th>Start Time</th>
					<th>End Time</th>
					<th>Date</th>
					<th>Duration</th>
				</tr>
				@foreach (factory(App\Booking::class, 15)->make() as $booking)
					@php
						$bookingID = rand(0, 999);
					@endphp
					<tr>
						<td class="customer_bookings__left-solid">{{ $bookingID }}</td>
						<td class="customer_bookings__left-dashed">{{ Carbon\Carbon::parse($booking->booking_start_time)->format('h:i A') }}</td>
						<td class="customer_bookings__left-dashed">{{ Carbon\Carbon::parse($booking->booking_end_time)->format('h:i A') }}</td>
						<td class="customer_bookings__left-dashed">{{ Carbon\Carbon::parse($booking->booking_end_time)->toDateString() }}</td>
						<td>{{ gmdate('G:i', $booking->duration()) }}</td>
					</tr>
				@endforeach
			</table>
		</div>
	</div>
@endsection