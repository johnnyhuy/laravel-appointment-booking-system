@extends('layouts.master')

@section('content')
	<div class="block block--no-padding">
		@if ($bookings->count())
			<table class="table table--no-margin cus_table">
				<tr>
					<th class="cus_table--id cus_table--right-solid">ID</th>
					<th class="cus_table--name">Employee</th>
					<th class="cus_table--name">Activity</th>
					<th class="cus_table--time">Start</th>
					<th class="cus_table--time">End</th>
					<th class="cus_table--time">Duration</th>
					<th class="cus_table--date">Date</th>
				</tr>
				@foreach ($bookings as $booking)
					<tr>
						<td class="cus_table--id cus_table--right-solid">{{ $booking->id }}</td>
						@if ($booking->employee)
							<td class="cus_table--name cus_table--right-dotted">
								{{ $booking->employee->firstname . ' ' . $booking->employee->lastname }}
							</td>
						@else
							<td class="cus_table--name cus_table--right-dotted cus_table--yellow">
								Pending
							</td>
						@endif
						<td class="cus_table--name cus_table--right-dotted">{{ $booking->activity->name }}</td>
						<td class="cus_table--time cus_table--right-dotted">{{ Carbon\Carbon::parse($booking->start_time)->format('h:i A') }}</td>
						<td class="cus_table--time cus_table--right-dotted">{{ Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</td>
						<td class="cus_table--time cus_table--right-dotted">{{ $booking->activity->duration }}</td>
						<td class="cus_table--date">{{ Carbon\Carbon::parse($booking->date)->format('d/m/y') }}</td>
					</tr>
				@endforeach
			</table>
		@else
			@include('shared.error_message_thumbs_down', [
				'message' => 'No customer bookings found.',
				'subMessage' => 'Create a new booking <a href="/bookings/new">here</a>'
			])
		@endif
	</div>
@endsection