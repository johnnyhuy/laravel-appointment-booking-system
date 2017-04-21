@extends('layouts.dashboard')

@section('content')

<div class="dash__block">
	<h1 class="dash__header">Add Working Times</h1>
	<h4 class="dash__description">Add Business Hours for the next month.</h4>
	<form class="request" method="POST" action="/admin/roster/{{ $date->format('m-Y') }}">
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
		<div class="form-group">
			<label for="inputEmployee">Employee <span class="request__validate">(ID - Title - Full Name)</span></label>
			<select name="employee_id" id="inputEmployee" class="form-control request__input">
				@foreach (App\Employee::all()->sortBy('title') as $employee)
					<option value="{{ $employee->id }}">{{ $employee->id . ' - ' . $employee->title . ' - ' . $employee->firstname . ' ' . $employee->lastname }}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group request__flex-container">
			<div class="request__flex request__flex--left">
				<label for="inputStartTime">Start Time <span class="request__validate">(24 hour format)</span></label>
				<input name="start_time" type="time" id="inputStartTime" class="form-control request__input" value="{{ old('start_time') ? old('start_time') : '09:00' }}" autofocus>
			</div>
			<div class="request__flex request__flex--right">
				<label for="inputEndTime">End Time <span class="request__validate">(24 hour format)</span></label>
				<input name="end_time" type="time" id="inputEndTime" class="form-control request__input" value="{{ old('end_time') ? old('end_time') : '17:00' }}" autofocus>
			</div>
		</div>
		<div class="form-group">
			<label for="inputDate">Date <span class="request__validate">(dd/mm/yyyy)</span></label>
				<input name="date" type="date" id="inputDate" class="form-control request__input" value="{{ old('date') ? old('date') : $date->startOfMonth()->format('Y-m-d') }}" autofocus>
		</div>
		<button class="btn btn-lg btn-primary btn-block btn--margin-top" href="/admin/employees">Add Working Time</button>
	</form>
</div>
<hr>
<div class="dash__block">
	<h1 class="dash__header dash__header--margin-top">Roster</h1>
	<h4 class="dash__description">Show the roster of a given month.</h4>
	<div class="form-group">
		<label for="inputDay">Date <span class="request__validate">(Select a dropdown item to go to month)</span></label>
		<select name="month_year" id="inputMonthYear" class="form-control request__input" onchange="location = '/admin/roster/' + this.value">
			@foreach ($months as $month)
				<option value="{{ $month->format('m-Y') }}" {{ $date->format('m-Y') == $month->format('m-Y') ? 'selected' : null }}>{{ $month->format('F Y') }}</option>
			@endforeach
		</select>
	</div>
	<h1>{{ $date->format('F Y') }}</h1>
	<div class="table-responsive dash__table-wrapper">
		<table class="table table--no-margin dash__table calender">
	        <tr>
	        	<th class="calender__week">Week</th>
				<th class="calender__day">Monday</th>
				<th class="calender__day">Tuesday</th>
				<th class="calender__day">Wednesday</th>
				<th class="calender__day">Thursday</th>
				<th class="calender__day">Friday</th>
				<th class="calender__day">Saturday</th>
				<th class="calender__day">Sunday</th>
			</tr>
			@for ($weeks = 0; $weeks < 5; $weeks++)
				<tr>
					<td class="calender__week calender__week--label">{{ $weeks + 1 }}</td>
					@for ($days = 0; $days < 7; $days++)
						@php
							$cDate = Carbon\Carbon::parse($date->toDateString())->startOfMonth()->startOfWeek()->addDays($days)->addWeeks($weeks);
						@endphp
						<td class="calender__day calender__day--block {{ $cDate->month != $date->month ? 'calender__day--disabled' : null }}">
							@if ($cDate->month == $date->month)
							<div class="calender__day-label">{{ $cDate->format('d') }}</div>
							<div class="working-time">
								@foreach ($roster as $workingTime)
									@php
										$wDate = Carbon\Carbon::parse($workingTime->date);
									@endphp
									@if ($workingTime->date == $cDate->startOfMonth()->startOfWeek()->addDays($days)->addWeeks($weeks)->toDateString())
										<section class="working-time__block">
											<div class="working-time__name">{{ $workingTime->employee->firstname . ' ' . $workingTime->employee->lastname }}</div>
											<div class="working-time__title">{{ $workingTime->employee->title }}</div>
											<div class="working-time__time">{{ Carbon\Carbon::parse($workingTime->start_time)->format('h:i A') . ' - ' . Carbon\Carbon::parse($workingTime->end_time)->format('h:i A') }}</div>
										</section>
									@endif
								@endforeach
							</div>
							@endif
						</td>
					@endfor
				</tr>
			@endfor
	    </table>
    </div>
</div>
@endsection