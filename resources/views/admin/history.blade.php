@extends('layouts.dashboard')

@section('content')
	<div class="dash__block">
		@if (count($history))
			<h1 class="dash__header">History of Bookings</h1>
			<h4 class="main_description">Present older bookings.</h4>
			<div class="table-responsive dash__table-wrapper">
			    <table class="table table--no-margin dash__table">
			        <tr>
						<th class="table--id">ID</th>
						<th class="table--customer table--left-solid">Customer</th>
						<th class="table--time">Start</th>
						<th class="table--time">End</th>
						<th class="table--date">Date</th>
					</tr>
					
					@foreach ($history as $booking)
						<tr>
							<td class="table--id">{{ $booking->id }}</td>
							<td class="table--customer table--left-solid">
								{{ $booking->customer->firstname . ' ' . $booking->customer->lastname }}
							</td>
							<td class="table--time table--left-dotted">{{ Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</td>
							<td class="table--time table--left-dotted">{{ Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
							<td class="table--date table--left-dotted">{{ Carbon\Carbon::parse($booking->date)->format('d/m/y') }}</td>
						</tr>
					@endforeach
			    </table>
			</div>
		@else
		<div class="notice">
			<span class="glyphicon glyphicon-thumbs-down notice__icon" aria-hidden="true"></span>
			<h1 class="notice__message">No history of bookings found.</h1>
			<h4 class="notice__description">Try add a booking before today.</h4>
		</div>
		@endif
	</div>
@endsection