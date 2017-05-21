@extends('layouts.dashboard')

@section('content')
	@if ($bookings->count())
		<div class="dash__block">
			<h1 class="dash__header">Summary of Bookings</h1>
			<h4 class="main_description">Present latest bookings for the next 7 days.</h4>
		    <table class="table no-margin">
		        <tr>
					<th class="table__id table__right-solid">ID</th>
					<th class="table__name">Customer</th>
					<th class="table__name">Employee</th>
					<th class="table__name">Activity</th>
					<th class="table__time">Start</th>
					<th class="table__time">End</th>
					<th class="table__time">Duration</th>
					<th class="table__date">Date</th>
				</tr>
				@foreach ($bookings as $booking)
					<tr>
						<td class="table__id">{{ $booking->id }}</td>
						<td class="table__name table__left-solid table__right-dotted">{{ $booking->customer->firstname . ' ' . $booking->customer->lastname }}</td>
						<td class="table__name table__right-dotted">{{ $booking->employee->firstname . ' ' . $booking->employee->lastname }}</td>
						<td class="table__name table__left-dotted">{{ $booking->activity->name }}</td>
						<td class="table__time table__left-dotted">{{ toTime($booking->start_time, false) }}</td>
						<td class="table__time table__left-dotted">{{ toTime($booking->end_time, false) }}</td>
						<td class="table__time table__left-dotted">{{ $booking->activity->duration }}</td>
						<td class="table__date table__left-dotted">{{ Time::parse($booking->date)->format('d/m/y') }}</td>
					</tr>
				@endforeach
		    </table>
		</div>
	@else
		@include('shared.error_message_thumbs_down', [
			'message' => 'No bookings found for the next 7 days.',
			'subMessage' => 'Add a new booking <a href="/admin/bookings">here</a>.'
		])
	@endif
@endsection