@extends('layouts.dashboard')

@section('content')

<div class="dash__block">
	<h1 class="dash__header">Add Working Times</h1>
	<h4 class="dash__description">Add Business Hours for the next month</h4>
	<form class="request" method="POST" action="/admin/roster">
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
				@foreach (App\Employee::all()->sortby('title') as $employee)
					<option value="{{ $employee->id }}">{{ $employee->id . ' - ' . $employee->title . ' - ' . $employee->firstname . ' ' . $employee->lastname }}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group request__flex-container">
			<div class="request__flex request__flex--left">
				<label for="inputStartTime">Start Time <span class="request__validate">(hh:mm AM/PM)</span></label>
				<input name="start_time" type="time" id="inputStartTime" class="form-control request__input" value="{{ old('start_time') ? old('start_time') : '09:00' }}" autofocus>
			</div>
			<div class="request__flex request__flex--right">
				<label for="inputEndTime">End Time <span class="request__validate">(hh:mm AM/PM)</span></label>
				<input name="end_time" type="time" id="inputEndTime" class="form-control request__input" value="{{ old('end_time') ? old('end_time') : '17:00' }}" autofocus>
			</div>
		</div>
		<div class="form-group request__flex-container">
			<div class="request__flex request__flex--left">
				<label for="inputDay">Day <span class="request__validate">(Monday, Tuesday etc.)</span></label>
				<select name="day" id="inputDay" class="form-control request__input">
					<option value="0">Monday</option>
					<option value="1">Tuesday</option>
					<option value="2">Wednesday</option>
					<option value="3">Thursday</option>
					<option value="4">Friday</option>
					<option value="5">Saturday</option>
					<option value="6">Sunday</option>
				</select>
			</div>
			<div class="request__flex request__flex--right">
				<label for="inputWeek">Week <span class="request__validate">(e.g. week 1 of month)</span></label>
				<select name="week" id="inputWeek" class="form-control request__input">
				@for ($i = 1; $i <= 5; $i++)
					<option value="{{ $i - 1 }}">{{ $i }}</option>
				@endfor
				</select>
			</div>
		</div>
		<button class="btn btn-lg btn-primary btn-block btn--margin-top" href="/admin/employees">Add Working Time</button>
	</form>
</div>
<hr>
<div class="dash__block">
	<h1 class="dash__header dash__header--margin-top">Roster</h1>
	<h4 class="dash__description">Working hours for the next month</h4>
	<h1>{{ Carbon\Carbon::now()->addMonth()->format('M Y') }}</h1>
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
						<td class="calender__day calender__day--block">
							<div class="calender__day-label">{{ Carbon\Carbon::now()->addMonth()->startOfMonth()->startOfWeek()->addDays($days)->addWeeks($weeks)->format('d') }}</div>
							<div class="working-time">
								@foreach ($roster as $workingTime)
									@if ($workingTime->date == Carbon\Carbon::now()->addMonth()->startOfMonth()->startOfWeek()->addDays($days)->addWeeks($weeks)->toDateString())
										<section class="working-time__block">
											<div class="working-time__name">{{ $workingTime->employee->firstname . ' ' . $workingTime->employee->lastname }}</div>
											<div class="working-time__title">{{ $workingTime->employee->title }}</div>
											<div class="working-time__time">{{ Carbon\Carbon::parse($workingTime->start_time)->format('h:i A') . ' - ' . Carbon\Carbon::parse($workingTime->end_time)->format('h:i A') }}</div>
										</section>
									@endif
								@endforeach
							</div>
						</td>
					@endfor
				</tr>
			@endfor
	    </table>
    </div>
</div>
@endsection