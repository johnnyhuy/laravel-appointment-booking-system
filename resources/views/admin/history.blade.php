@extends('layouts.dashboard')

@section('content')
	<div class="main__block">
		<h1 class="main__header">History of Bookings</h1>
		<h4 class="main_description">Present older bookings.</h4>
		<div class="table-responsive main__table-wrapper">
		    <table class="table table--no-margin main__table">
		        <tr>
					<th class="table--id">ID</th>
					<th class="table--customer main__table--left-solid">Customer</th>
					<th class="table--time">Start</th>
					<th class="table--time">End</th>
					<th class="table--date">Date</th>
				</tr>
				@foreach ($history as $booking)
					<tr>
						<td class="table--id">{{ $booking->id }}</td>
						<td class="table--customer main__table--left-solid">
							{{ $booking->customer->firstname . ' ' . $booking->customer->lastname }}
						</td>
						<td class="table--time main__table--left-dotted">{{ Carbon\Carbon::parse($booking->booking_start_time)->format('H:i') }}</td>
						<td class="table--time main__table--left-dotted">{{ Carbon\Carbon::parse($booking->booking_end_time)->format('H:i') }}</td>
						<td class="table--date main__table--left-dotted">{{ Carbon\Carbon::parse($booking->booking_start_time)->toDateString() }}</td>
					</tr>
				@endforeach
		    </table>
		</div>
	</div>
@endsection