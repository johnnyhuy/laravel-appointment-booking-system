@extends('layouts.master')

@section('content')
	<div class="block block--no-padding">
		@if ($bookings)
			<table class="table table--no-margin customer_bookings">
				<tr>
					<th class="customer_bookings__left-solid">ID</th>
					<th>Start Time</th>
					<th>End Time</th>
					<th>Date</th>
					<th>Duration</th>
				</tr>
				@foreach ($bookings as $booking)
					<tr>
						<td class="customer_bookings__left-solid">{{ $booking->id }}</td>
						<td class="customer_bookings__left-dashed">{{ Carbon\Carbon::parse($booking->start_time)->format('h:i A') }}</td>
						<td class="customer_bookings__left-dashed">{{ Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</td>
						<td class="customer_bookings__left-dashed">{{ Carbon\Carbon::parse($booking->date)->format('d/m/y') }}</td>
						<td>{{ $booking->activity->duration }}</td>
					</tr>
				@endforeach
			</table>
		@else
			@include('shared.thumbs_down_error_message', [
				'message' => 'No customer bookings found.',
				'subMessage' => 'Create a new booking <a href="/bookings/new">here</a>'
			])
		@endif
	</div>
@endsection