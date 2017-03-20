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
					<th>Customer id</th>
				</tr>
				<?php
					/*
						Testing stuff
							-first we fill the DB with sample data
							-then we pick a random customerID (from 1 to 10)
							-then display bookings with this customerid
							-This shows the functionality of pulling bookings from the DB
					*/
					#fills the database with test data
					function fillDBTest()
					{
						for($i = 0; $i<100; $i++)
						{
							factory(App\Booking::class)->create();
						}
					}
					#uncomment this to have the database be filled
					#fillDBtest();
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