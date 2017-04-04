@extends('layouts.dashboard')

@section('content')
	<div class="main__block">
		<h1 class="main__header">Summary of Business</h1>
		<h2 class="main_description">Details:</h2>
		<h3 class="main_description">Business name: {{ucfirst($business->business_name) }}</h3>
		<h3 class="main_description">Business owner: {{ucfirst($business->owner_name) }}</h3>
		<h2 class="main_description">Contact details:</h2>
		<h3 class="main_description">
		Address: {{$business->address }}</h3>
		<h3 class="main_description">
		Phone: {{$business->phone }}</h3>
	
	</div>
@endsection