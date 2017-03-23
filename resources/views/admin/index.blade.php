@extends('layouts.dashboard')

@section('content')
	<h1 class="page-header">Booking Summaries</h1>
	<div class="table-responsive">
	    <table class="table table-striped">
	      <thead>
	        <tr>
	          <th>Booking id</th>
	          <th>Start time</th>
	          <th>End time</th>
	          <th>Customer id</th>
	        </tr>
	      </thead>
	      <tbody>
	      	<?php
	            $bookings = DB::table('bookings')->get()->alL();
	      	?>
	    	@foreach($bookings as $booking)
	        	<tr>
	        		<td> {{$booking->id }}</td>
	        		<td>{{ \Carbon\Carbon::parse($booking->booking_start_time)->toDayDateTimeString() }}</td>
	        		<td>{{ \Carbon\Carbon::parse($booking->booking_start_time)->toDayDateTimeString() }}</td>
	        		<td>{{ $booking->customer_id }}</td>
	        	</tr>
	    	@endforeach
	      </tbody>
	    </table>
	</div>
@endsection