@extends('layouts.dashboard')

@section('content')
	@if ($bookings->count())
		<div class="dash__block">
			<h1 class="dash__header">Summary of Bookings</h1>
			<h4 class="main_description">Present latest bookings for the next 7 days.</h4>
			<div class="table-responsive dash__table-wrapper">
			    <table class="table table--no-margin dash__table">
			        <tr>
						<th class="table--id table--right-solid">ID</th>
						<th class="table--name">Customer</th>
						<th class="table--name">Employee</th>
						<th class="table--name">Activity</th>
						<th class="table--time">Start</th>
						<th class="table--time">End</th>
						<th class="table--time">Duration</th>
						<th class="table--date">Date</th>
					</tr>
					@foreach ($bookings as $booking)
						<tr>
							<td class="table--id">{{ $booking->id }}</td>
							<td class="table--name table--left-solid table--right-dotted">{{ $booking->customer->firstname . ' ' . $booking->customer->lastname }}</td>
							@if ($booking->employee)
								<td class="table--name table--right-dotted">
									{{ $booking->employee->firstname . ' ' . $booking->employee->lastname }}
								</td>
							@else
								<td class="table--name table--right-dotted table--red">
									Unassigned
								</td>
							@endif
							<td class="table--name table--left-dotted">{{ $booking->activity->name }}</td>
							<td class="table--time table--left-dotted">{{ $booking->start_time }}</td>
							<td class="table--time table--left-dotted">{{ $booking->end_time }}</td>
							<td class="table--time table--left-dotted">{{ $booking->activity->duration }}</td>
							<td class="table--date table--left-dotted">{{ Carbon\Carbon::parse($booking->date)->format('d/m/y') }}</td>
						</tr>
					@endforeach
			    </table>
			</div>
		</div>
	@else
		@include('shared.error_message_thumbs_down', [
			'message' => 'No bookings found for the next 7 days.',
			'subMessage' => 'Add a new booking <a href="/admin/booking">here</a>.'
		])
	@endif
@endsection