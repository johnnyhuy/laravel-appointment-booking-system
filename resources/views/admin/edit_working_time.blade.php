@extends('layouts.dashboard')

@section('content')

<div class="dash__block">
    <h1 class="dash__header">Edit Working Time</h1>
    <h4 class="dash__description">Add Business Hours for the next month.</h4>
    <form class="request" method="POST" action="/admin/roster/{{ $workingTime->id }}">
        {{ method_field('PUT') }}
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
        <div class="alert alert-warning"><strong>Warning!</strong> Once you have edited the working time of an employee, all working time bookings will be unassigned.</div>
        <div class="form-group">
            <label for="inputEmployee">Employee <span class="request__validate">(ID - Title - Full Name)</span></label>
            <select name="employee_id" id="inputEmployee" class="form-control request__input">
                @foreach (App\Employee::all()->sortBy('title') as $employee)
                    <option value="{{ $employee->id }}" {{ $workingTime->employee->id == $employee->id ? 'selected' : null }}>{{ $employee->id . ' - ' . $employee->title . ' - ' . $employee->firstname . ' ' . $employee->lastname }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group request__flex-container">
            <div class="request__flex request__flex--left">
                <label for="inputStartTime">Start Time <span class="request__validate">(24 hour format)</span></label>
                <input name="start_time" type="time" id="inputStartTime" class="form-control request__input" value="{{ old('start_time') ? old('start_time') : $workingTime->start_time }}" autofocus>
            </div>
            <div class="request__flex request__flex--right">
                <label for="inputEndTime">End Time <span class="request__validate">(24 hour format)</span></label>
                <input name="end_time" type="time" id="inputEndTime" class="form-control request__input" value="{{ old('end_time') ? old('end_time') : '17:00' }}" autofocus>
            </div>
        </div>
        <div class="form-group">
            <label for="inputDate">Date <span class="request__validate">(dd/mm/yyyy)</span></label>
            <input name="date" type="date" id="inputDate" class="form-control request__input" value="{{ old('date') ? old('date') : Carbon\Carbon::parse($workingTime->date)->format('Y-m-d') }}" autofocus>
        </div>
        <button class="btn btn-lg btn-primary btn-block btn--margin-top" href="/admin/employees">Add Working Time</button>
    </form>
</div>
@endsection