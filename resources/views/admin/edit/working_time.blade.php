@extends('layouts.dashboard')

@section('content')

<div class="dash__block">
    <h1 class="dash__header">Edit Working Time</h1>
    <h4 class="dash__description">Add Business Hours for the next month.</h4>
    @include('shared.session_message')
    @include('shared.error_message')
    <form class="request" method="POST" action="/admin/roster/{{ $workingTime->id }}">
        {{ method_field('PUT') }}
        {{ csrf_field() }}
        <div class="alert alert-warning"><strong>Warning!</strong> Once you have edited the working time of an employee, all bookings related to working time will be deleted.</div>
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
                <label for="roster_start_time">Start Time <span class="request__validate">(24 hour format)</span></label>
                <input name="start_time" type="text" id="roster_start_time" class="form-control request__input" placeholder="hh:mm" value="{{ old('start_time') ? old('start_time') : $workingTime->start_time }}" masked-time>
            </div>
            <div class="request__flex request__flex--right">
                <label for="roster_end_time">Start Time <span class="request__validate">(24 hour format)</span></label>
                <input name="end_time" type="text" id="roster_end_time" class="form-control request__input" placeholder="hh:mm" value="{{ old('end_time') ? old('end_time') : $workingTime->end_time }}" masked-time>
            </div>
        </div>
        <div class="form-group">
            <label for="roster_date">Date <span class="request__validate">(dd/mm/yyyy)</span></label>
            <p id="roster_date">{{ Time::parse($workingTime->date)->format('d/m/Y') }}</p>
        </div>
        <button class="btn btn-lg btn-primary btn-block btn--margin-top" href="/admin/employees">Edit Working Time</button>
    </form>
</div>
@endsection