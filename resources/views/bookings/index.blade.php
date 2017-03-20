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
					<th>Date</th>
					<th>Start Time</th>
					<th>End Time</th>
				</tr>
				<tr>
					<td>Example</td>
					<td>10/14/2016</td>
					<td>10:00AM</td>
					<td>11:00PM</td>
				</tr>
			</table>
		</div>
	</div>
@endsection