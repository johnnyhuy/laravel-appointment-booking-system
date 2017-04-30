@extends('layouts.master')

@section('content')
	<div class="block request">
		@include('shared.error_message')
		@if (!App\Activity::first())
			@include('shared.error_message_custom', [
				'title' => 'Activities do not exist',
				'message' => 'Please contact a site administrator.',
				'type' => 'danger'
			])
		@endif
		<form class="request" method="POST" action="/bookings">
			{{ csrf_field() }}
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
@endsection