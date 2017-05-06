@extends('layouts.dashboard')

@section('content')
	<div class="dash__block">
		@if (count($history))
			<h1 class="dash__header">History of Bookings</h1>
			<h4 class="main_description">Present older bookings.</h4>
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
				@foreach ($history as $booking)
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
		@else
			@include('shared.error_message_thumbs_down', [
				'message' => 'No history of bookings found.',
				'subMessage' => 'Try add a booking before today.'
			])
		@endif
	</div>
@endsection