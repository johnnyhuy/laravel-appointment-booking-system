@extends('layouts.master')

@section('content')
	<div class="container">
		<div class="header">
			<h1 class="header__title">Business Name</h1>
			<h3 class="header__subtitle">My Bookings</h3>
		</div>
		<div class="block">
			<table class="table">
				<tr>
					<th>Title</th>
					<th>Start Time</th>
					<th>End Time</th>
				</tr>
				<?php
					#temp for testing
					$customerID = 1;
					$bookings = DB::table('bookings')->get();
            		#echo "$bookings";
				?>
				@foreach ($bookings as $booking)
					@if($booking->customer_id == $customerID)
						<tr>
							<td>{{$booking->title}}</td>
							<td>{{$booking->booking_start_time}}</td>
							<td>{{$booking->booking_end_time}}</td>	
							<td>{{$booking->customer_id}}</td>
						</tr>
					@endif
				@endforeach
			</table>
		</div>
	</div>
@endsection