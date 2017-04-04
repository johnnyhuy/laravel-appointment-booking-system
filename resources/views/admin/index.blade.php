@extends('layouts.dashboard')

@section('content')
	<div class="main__block">
		<h2 class="main__header">Business Information</h2>
		<h4 class="main_description">Details of the business</h4>


		<h2 class="main_description">Full Name </h2>
		<h4 class="main_description"> {{ucfirst($business->owner_name) }}</h4>

		<h2 class="main_description">Business name</h2> 
		<h4 class="main_description">{{ucfirst($business->business_name) }}</h4>

		<h2 class="main_description">Phone</h2>
		<h4 class="main_description">
		{{$business->phone }}</h4>

		<h2 class="main_description">Address</h2>
		<h4 class="main_description">{{ucfirst($business->address) }}</h4>
		
	
	</div>
@endsection