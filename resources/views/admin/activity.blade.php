@extends('layouts.dashboard')

@section('content')
	<div class="dash__block">
		<h1 class="dash__header">Add Activity</h1>
		<h4 class="dash__description">Add a new employee to the system.</h4>
		@if ($flash = session('message'))
			<div class="alert alert-success">
				{{ $flash }}	
			</div>
		@endif
		@if (count($errors))
			<div class="alert alert-danger">
				@foreach ($errors->all() as $error)
					{{ $error }}<br>
				@endforeach
			</div>
		@endif
		<form class="request" method="POST" action="/admin/activity">
			{{ csrf_field() }}
			<div class="form-group">
				<label for="inputName">Name <span class="request__validate">(e.g. Haircut, Coloring)</span></label>
				<input name="name" type="text" id="inputName" class="form-control request__input" placeholder="Name" value="{{ old('name') }}" autofocus>
			</div>
			<div class="form-group">
				<label for="inputDescription">Description <span class="request__validate">(optional)</span></label>
				<input name="description" type="text" id="inputDescription" class="form-control request__input" placeholder="Description" value="{{ old('description') }}" autofocus>
			</div>
			<div class="form-group">
				<label for="inputDuration">Duration <span class="request__validate">(24 hour format)</span></label>
				<input name="duration" type="time" id="inputDuration" class="form-control request__input" value="{{ old('duration') }}" autofocus>
			</div>
			<button class="btn btn-lg btn-primary btn-block btn--margin-top">Add Activity</button>
		</form>
	</div>
	<hr>
	<div class="dash__block">
		<h1 class="dash__header dash__header--margin-top">Activities</h1>
		<h4 class="dash__description">A table of all activities within the business.</h4>
		@if ($activities->count())
			<div class="table-responsive dash__table-wrapper">
				<table class="table table--no-margin dash__table calender">
			        <tr>
			        	<th class="table--id table--right-solid">ID</th>
						<th class="table--name">Name</th>
						<th class="table--text">Description</th>
						<th class="table--time">Duration</th>
					</tr>
					@foreach ($activities as $activity)
						<tr>
							<td class="table--id table--right-solid">{{ $activity->id }}</td>
							<td class="table--name table--right-dotted">{{ $activity->name }}</td>
							<td class="table--text table--right-dotted">{{ $activity->description }}</td>
							<td class="table--time">{{ $activity->duration }}</td>
						</tr>
					@endforeach
			    </table>
		    </div>
	    @else
			<div class="notice">
				<span class="glyphicon glyphicon-thumbs-down notice__icon" aria-hidden="true"></span>
				<h1 class="notice__message">No activities found.</h1>
				<h4 class="notice__description">Try add an activity using the form above.</h4>
			</div>
		@endif
	</div>
@endsection