@extends('layouts.master')

@section('content')
	@if ($bookings->count())
		<table class="table no-margin">
			<tr>
				<th class="table__id table__right-solid">ID</th>
				<th class="table__name">Employee</th>
				<th class="table__name">Activity</th>
				<th class="table__time">Start</th>
				<th class="table__time">End</th>
				<th class="table__time">Duration</th>
				<th class="table__date">Date</th>
			</tr>
			@foreach ($bookings as $booking)
				<tr>
					<td class="table__id table__right-solid">{{ $booking->id }}</td>
					<td class="table__name table__right-dotted">{{ $booking->employee->firstname . ' ' . $booking->employee->lastname }}</td>
					<td class="table__name table__right-dotted">{{ $booking->activity->name }}</td>
					<td class="table__time table__right-dotted">{{ toTime($booking->start_time, true) }}</td>
					<td class="table__time table__right-dotted">{{ toTime($booking->end_time, true) }}</td>
					<td class="table__time table__right-dotted">{{ $booking->activity->duration }}</td>
					<td class="table__date">{{ toDate($booking->date, true) }}</td>
				</tr>
			@endforeach
		</table>
	@else
		<div class="block">
			@include('shared.error_message_thumbs_down', [
				'message' => 'No Bookings Found.',
				'subMessage' => 'Create a new booking <a href="/bookings/new">here</a>'
			])
		</div>
	@endif
@endsection