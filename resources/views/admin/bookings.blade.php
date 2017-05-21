@extends('layouts.dashboard')

@section('content')

<div class="dash__block">
	@include('shared.session_message')
	@include('shared.error_message')
	<ul class="nav nav-tabs" role="tablist">
	    <li role="presentation" class="active"><a href="#bookings" aria-controls="bookings" role="tab" data-toggle="tab">Bookings</a></li>
	    <li role="presentation"><a href="#roster" aria-controls="roster" role="tab" data-toggle="tab">Roster</a></li>
  	</ul>

  	<div class="tab-content">
    	<div role="tabpanel" class="tab-pane active" id="bookings">
    		<div class="block request">
    		<h1 class="dash__header">Add Booking</h1>
			<h4 class="dash__description">Add a new booking to the system</h4>
    		<form class="request" method="POST" action="/admin/bookings">
				@include('shared.loading_message')
				{{ csrf_field() }}
				<div class="form-group">
					<label for="input_employee">Employee <span class="request__validate">(Title - Full Name - ID)</span></label>
					<select name="employee_id" id="input_employee" class="form-control request__input" onchange="showRedirect('.loading', '/admin/bookings/{{ $dateString }}/' + this.value)">
						@foreach (Employee::all()->sortBy('lastname')->sortBy('firstname')->sortBy('title') as $e)
							<option value="{{ $e->id }}" {{ old('employee_id') == $e->id || $employeeID == $e->id ? 'selected' : null }}>{{ $e->title . ' - ' . $e->firstname . ' ' . $e->lastname . ' - ' . $e->id }}</option>
						@endforeach
						<option value="none" {{ old('employee_id') || $employeeID ? null : 'selected' }}>-- None --</option>
					</select>
				</div>
				<div class="form-group request__flex-container">
					<div class="request__flex request__flex--left">
						<label for="input_month_year">Month & Year <span class="request__validate">(Select to go to month)</span></label>
					    <select name="month_year" id="input_month_year" class="form-control request__input" onchange="showRedirect('.loading', '/admin/bookings/' + this.value + '{{ $employeeID ? '/' . $employeeID : null }}')">
					        @foreach ($months as $month)
					            <option value="{{ $month->format('m-Y') }}" {{ $date->format('m-Y') == $month->format('m-Y') ? 'selected' : null }}>{{ $month->format('F Y') }}</option>
					        @endforeach
					    </select>
					</div>
					<div class="request__flex request__flex--right">
						<label for="inputDay">Day <span class="request__validate"></span></label>
					    <select name="day" id="inputMonthYear" class="form-control request__input">
					        @for ($day = 1; $day <= $date->endOfMonth()->day; $day++)
					            <option value="{{ $day }}" {{ old('day') == $day ? 'selected' : null }}>{{ $day }}</option>
					        @endfor
					    </select>
					</div>
				</div>
				<div class="form-group">
					<label for="input_customer">Customer <span class="request__validate">(Full Name - ID)</span></label>
					<select name="customer_id" id="input_customer" class="form-control request__input">
						@foreach (Customer::all()->sortBy('lastname')->sortBy('firstname') as $customer)
							<option value="{{ $customer->id }}">{{ $customer->firstname }} - {{$customer->lastname }} - {{ $customer->id }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="input_activity">Activity <span class="request__validate">(Name - Duration)</span></label>
					<select name="activity_id" id="input_activity" class="form-control request__input">
						@foreach (Activity::all()->sortBy('name') as $activity)
							<option value="{{ $activity->id }}" {{ old('activity_id') == $activity->id ? 'selected' : null }}>{{ $activity->name . ' - ' . $activity->duration }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="input_start_time">Start Time <span class="request__validate">(24 hour format)</span></label>
					<input name="start_time" type="time" id="input_start_time" class="form-control request__input" value="{{ old('start_time') ? old('start_time') : '09:00' }}" autofocus>
				</div>
				<button class="btn btn-lg btn-primary btn-block btn--margin-top">Add Booking</button>
			</form>
			<h1 class="dash__header dash__header--margin-top">Bookings</h1>
			<h4 class="main_description">A table of all bookings on {{ $date->format('F Y') }}</h4>
			<div class="form-group">
				<label for="input_month_year">Month & Year <span class="request__validate">(Select to go to month)</span></label>
			    <select name="month_year" id="input_month_year" class="form-control request__input" onchange="location = '/admin/bookings/' + this.value + '#bookings'">
			        @foreach ($months as $month)
			            <option value="{{ $month->format('m-Y') }}" {{ $date->format('m-Y') == $month->format('m-Y') ? 'selected' : null }}>{{ $month->format('F Y') }}</option>
			        @endforeach
			    </select>
		    </div>
		    @if ($bookings->count())
			    <table class="table no-margin">
			        <tr>
						<th class="table__id table__right-solid">ID</th>
						<th class="table__name">Customer</th>
						<th class="table__name">Employee</th>
						<th class="table__name">Activity</th>
						<th class="table__time">Start</th>
						<th class="table__time">End</th>
						<th class="table__time">Duration</th>
						<th class="table__date">Date</th>
					</tr>
					@foreach ($bookings as $booking)
						<tr>
							<td class="table__id table__right-solid">{{ $booking->id }}</td>
							<td class="table__name table__right-dotted">{{ $booking->customer->firstname . ' ' . $booking->customer->lastname }}</td>
							<td class="table__name table__right-dotted">{{ $booking->employee->firstname . ' ' . $booking->employee->lastname }}</td>
							<td class="table__name table__right-dotted">{{ $booking->activity->name }}</td>
							<td class="table__time table__right-dotted">{{ toTime($booking->start_time, false) }}</td>
							<td class="table__time table__right-dotted">{{ toTime($booking->end_time, false) }}</td>
							<td class="table__time table__right-dotted">{{ $booking->activity->duration }}</td>
							<td class="table__date">{{ toDate($booking->date, true) }}</td>
						</tr>
					@endforeach
			    </table>
			@else
				@include('shared.error_message_thumbs_down', [
					'message' => 'No bookings found.',
					'subMessage' => 'Try add a booking using the form above.'
				])
			@endif
			</div>
		</div>

    	<div role="tabpanel" class="tab-pane" id="roster">
    		<div class="block request">
			<div class="dash__block" id="roster">
				<h1 class="dash__header">Roster {{ $employee ? ' for ' . $employee->firstname . ' ' . $employee->lastname : null }}</h1>
				<h4 class="dash__description">Show the roster of a given month.</h4>
				<div class="form-group">
					<label for="input_month_year">Month & Year <span class="request__validate">(Select to go to month)</span></label>
				    <select name="month_year" id="input_month_year" class="form-control request__input" onchange="location = '/admin/bookings/' + this.value + '#roster'">
				        @foreach ($months as $month)
				            <option value="{{ $month->format('m-Y') }}" {{ $date->format('m-Y') == $month->format('m-Y') ? 'selected' : null }}>{{ $month->format('F Y') }}</option>
				        @endforeach
				    </select>
			    </div>
			   <h1>{{ $date->format('F Y') }}</h1>
				@include('shared.calendar', [
					'pDate' => $date,
					'items' => $roster,
					'type' => 'admin'
				])
			</div>
			</div>
		</div>

    </div>
</div>
<hr>

@endsection