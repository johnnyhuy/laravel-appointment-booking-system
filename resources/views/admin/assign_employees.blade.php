@extends('layouts.dashboard')

@section('content')

<div class="dash__block">
	<h1 class="dash__header">Assign Employee</h1>
	<h4 class="dash__description">Assign an employee to a particular booking</h4>
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
	<form class="request" method="POST" action="/admin/employees/assign">
		{{ csrf_field() }}

		<div class="form-group">
			<label for="inputEmployee">Employee <span class="request__validate">(ID - Title - Full Name)</span></label>
			<select name="employee_id" id="inputEmployee" class="form-control request__input" onchange="location = '/admin/employees/assign/' + this.value" value="{{ $employee_id }}">
				@foreach (App\Employee::all()->sortBy('title')->sortBy('firstname')->sortBy('lastname') as $employee)
					<option value="{{ $employee->id }}" <?php if($employee->id == $employee_id) echo 'selected'; ?>> {{ $employee->id . ' - ' . $employee->title . ' - ' . $employee->firstname . ' ' . $employee->lastname }}</option>
				@endforeach
			</select>
		</div>

		<label>Select which bookings to assign the employee to 
		<span class="request__validate">(only bookings which the employee is available to work are shown)</span></label>
		<div class="table-responsive dash__table-wrapper">
		    <table class="table table--no-margin dash__table">
		        <tr>
					<th class="table--id">Select</th>
					<th class="table--left-solid">Customer</th>
					<th class="table--time">Start</th>
					<th class="table--time">End</th>
					<th class="table--date">Date</th>
					<th class="table--time">Duration</th>
					<th class="table--right-solid">Employee Assigned</th>
				</tr>
				@foreach (App\Booking::getWorkableBookingsForEmployee($employee_id,30) as $booking)
					<tr>
						<td class="table--id"><input name="bookings" value="{{ $booking->id }}" type="checkbox"></input></td>
						<td class="table--left-solid">{{ $booking->customer->firstname . ' ' . $booking->customer->lastname }}</td>
						<td class="table--time table--left-dotted">{{ Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</td>
						<td class="table--time table--left-dotted">{{ Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
						<td class="table--date table--left-dotted">{{ Carbon\Carbon::parse($booking->date)->format('d/m/y') }}</td>
						<td class="table--time table--left-dotted">{{ gmdate('G:i', $booking->duration()) }}</td>
						@if (isset($booking->employee))
							<td class="table--left-solid">{{ $booking->employee->firstname . ' ' . $booking->employee->lastname }}</td>
						@else
							<td class="table--left-solid"> </td>
						@endif
					</tr>
				@endforeach
		    </table>
		</div>
		<button class="btn btn-lg btn-primary btn-block btn--margin-top" href="/admin/employees/assign">Assign Employee</button>
		<hr>
	</form>
</div>
@endsection