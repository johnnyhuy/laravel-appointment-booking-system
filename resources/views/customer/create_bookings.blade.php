@extends('layouts.master')

@section('content')
	<div class="container">
		<div class="block request">
			<!--<h1 class="dash__header">Add Booking</h1>
			<h4 class="dash__description">Add a new booking to the system</h4>-->
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
			<form class="request" method="POST" action="/create_booking">
				{{ csrf_field() }}		
				<input name='customer_id' value='{{ 1 }}'type='hidden'></input>
				<input name='employee_id' value='-1' type='hidden'></input>
				
				<div class="form-group">
					<label for="inputActivity">Activity <span class="request__validate">(ID - Name - Duration)</span></label>
					<select name="activity_id" id="inputActivity" class="form-control request__input">
						@foreach (App\Activity::all()->sortBy('name') as $activity)
							<option value="{{ $activity->id }}">{{ $activity->id . ' - ' . $activity->name . ' - ' . $activity->duration }}</option>
						@endforeach
					</select>
				</div>

				<div class="form-group">
					<label for="inputStartTime">Start Time <span class="request__validate">(24 hour format)</span></label>
					<input name="start_time" type="time" id="inputStartTime" class="form-control request__input" value="{{ old('start_time') ? old('start_time') : '09:00' }}" autofocus>
				</div>

				<div class="form-group">
					<label for="inputDate">Date <span class="request__validate">(dd/mm/yyyy format)</span></label>
					<input name="date" type="date" id="inputDate" class="form-control request__input" value="{{ old('date') ? old('date') : Carbon\Carbon::now()->addMonth()->startOfMonth()->format('Y-m-d') }}" autofocus>
				</div>
			
				<button class="btn btn-lg btn-primary btn-block btn--margin-top">Add Booking</button>
			</form>
		</div>
	</div>
@endsection