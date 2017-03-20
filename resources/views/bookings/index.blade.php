@extends('layouts.master')

@section('content')
	<div class="container">
		<div class="block">
			<table class="table">
				<tr>
					<th>Title</th>
					<th>Start Time</th>
					<th>End Time</th>
					<th>Customer id</th>
				</tr>
				<?php
					function fillDBTest()
					{
						for($i = 0; $i<100; $i++)
						{
							factory(App\Booking::class)->create();
						}
					}

					$customerID = rand(1,10);
					$bookings = DB::table('bookings')
						->where('customer_id', '=', $customerID)
						->get();
				?>
				@foreach ($bookings as $booking)
					<tr>
						<td>{{$booking->title}}</td>
						<td>{{$booking->booking_start_time}}</td>
						<td>{{$booking->booking_end_time}}</td>	
						<td>{{$booking->customer_id}}</td>
					</tr>
				@endforeach
			</table>
		</div>
	</div>
@endsection