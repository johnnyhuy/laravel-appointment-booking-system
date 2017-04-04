@extends('layouts.dashboard')

@section('content')

<div class="main__block">
	<h1 class="main__header">Add Working Times</h1>
	<h4 class="main__description">Add Business Hours for the next month</h4>
	<form method="POST" action="/admin/roster">
		{{ csrf_field() }}
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
		<label for="inputDate">Date</label>
		<input name="date" type="date" id="inputDate" class="form-control request__input" placeholder="Date" value="{{ old('date') }}" autofocus>
		<label for="inputStartTime">Start Time</label>
		<input name="start_time" type="time" id="inputStartTime" class="form-control request__input" placeholder="12:00" value="{{ old('start_time') }}" autofocus>
		<label for="inputEndTime">End Time</label>
		<input name="end_time" type="time" id="inputEndTime" class="form-control request__input" placeholder="12:00" value="{{ old('end_time') }}" autofocus>
		<button class="btn btn-lg btn-primary btn-block btn--margin-top" href="/admin/employees">Add Working Time</button>
	</form>
	<h1 class="main__header main__header--margin-top">Roster</h1>
	<h4 class="main__description">Working hours for the next month</h4>
	<table class="table main__table">
        <tr>
        	<th>Day</th>
			<th>Date</th>
			<th>Start Time</th>
			<th>End Time</th>
		</tr>
		@foreach(App\WorkingHours::getThisMonthsWorkingHours() as $workingHours)
			<tr>
				<th>
					{{date("l", strtotime($workingHours->day))}} 
				</th>
				<td> {{date("d-m-Y", strtotime($workingHours->day))}} </td>
				<td> {{date("H:i", strtotime($workingHours->start_time))}} </td>
				<td> {{date("H:i", strtotime($workingHours->end_time))}} </td>
			</tr>
		@endforeach
    </table>
</div>
@endsection