@extends('layouts.master')

@section('content')
	@if (!BusinessOwner::first())
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>No Business Found!</strong> Register your business <a href="/admin/register">here</a>
		</div>
	@endif
	@if (session('error'))
		<div class="alert alert-danger">
			{{ session('error') }}<br>
		</div>
	@endif
	<div class="block request">
		<form class="request__form" method="POST" action="/login">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div class="form-group">
				<label for="inputUsername">Username</label>
				<input type="text" name="username" id="inputUsername" class="form-control request__input" placeholder="Username"  autofocus>
			</div>
			<div class="form-group">
				<label for="inputPassword">Password</label>
				<input type="password" name="password" id="inputPassword" class="form-control request__input" placeholder="Password" >
			</div>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
		</form>
	</div>
	<hr>
	<a class="btn btn-lg btn-primary btn-block" href="/register">Register</a>
@endsection