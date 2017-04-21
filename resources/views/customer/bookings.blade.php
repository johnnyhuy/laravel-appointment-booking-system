@extends('layouts.master')

@section('content')
	<div class="container">
		<div class="block block--no-padding">
			<table class="table table--no-margin customer_bookings">
				<tr>
					<th class="customer_bookings__left-solid">ID</th>
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
						<td class="customer_bookings__left-dashed">{{ Carbon\Carbon::parse($booking->start_time)->format('h:i A') }}</td>
						<td class="customer_bookings__left-dashed">{{ Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</td>
						<td class="customer_bookings__left-dashed">{{ Carbon\Carbon::parse($booking->date)->format('d/m/y') }}</td>
						<td>{{ gmdate('G:i', $booking->duration()) }}</td>
					</tr>
				@endforeach
			</table>
		</div>
	</div>
@endsection