@extends('layouts.dashboard')

@section('content')
	<div class="main__block">
		<h2 class="main__header">Business Information</h2>
		<h4 class="main_description">Details of the business</h4>
		<h2>Full Name </h2>
		<p class="main__info">{{ ucfirst($business->owner_name) }}</p>
		<h2>Business name</h2> 
		<p class="main__info">{{ ucfirst($business->business_name) }}</p>
		<h2>Phone</h2>
		<p class="main__info">{{ $business->phone }}</p>
		<h2>Address</h2>
		<p class="main__info">{{ ucfirst($business->address) }}</p>
	</div>
@endsection