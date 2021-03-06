
@extends('layouts.dashboard')

@section('content')
	<div class="dash__block">
		<a class="btn btn-lg btn-primary pull-right" href="/admin/edit/"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit</a>
		<h1 class="dash__header">Business Information</h1>
		<h4 class="main_description">Details of the business</h4>
		@include('shared.session_message')
		<h2>Full Name </h2>
		<p class="dash__info">{{ ucfirst($business->firstname) . ' ' . ucfirst($business->lastname) }}</p>
		<h2>Business name</h2>
		<p class="dash__info">{{ $business->business_name }}</p>
		<h2>Phone</h2>
		<p class="dash__info">{{ $business->phone }}</p>
		<h2>Address</h2>
		<p class="dash__info">{{ ucfirst($business->address) }}</p>
	</div>
@endsection