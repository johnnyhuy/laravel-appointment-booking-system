@extends('layouts.master')

@section('content')
	<div class="container">
		<div class="block">
			<form class="request" method="POST" action="/admin/login">
				@include('shared.error_message')
				{{ csrf_field() }}
				<label for="inputUsername">Username</label>
				<input type="text" name="username" id="inputUsername" class="form-control request__input" placeholder="Username" autofocus>
				<label for="inputPassword">Password</label>
				<input type="password" name="password" id="inputPassword" class="form-control request__input" placeholder="Password" >
				<button class="btn btn-lg btn-primary btn-block request__button" type="submit">Sign in</button>
			</form>
		</div>
	</div>
@endsection