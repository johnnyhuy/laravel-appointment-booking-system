@extends('layouts.master')

@section('content')
    <div class="block request">
        @include('shared.error_message')
        @if (!Activity::first())
            @include('shared.error_message_custom', [
                'title' => 'Activities do not exist',
                'message' => 'Please contact a site administrator.',
                'type' => 'danger'
            ])
        @endif
        <form class="request" method="POST" action="/bookings">
            @include('shared.loading_message')
            {{ csrf_field() }}
            <div class="form-group">
                <label for="input_employee">Employee <span class="request__validate">(Title - Full Name - ID)</span></label>
                <select name="employee_id" id="input_employee" class="form-control request__input" onchange="showRedirect('.loading', '/bookings/{{ $dateString }}/new/' + this.value)">
                    @foreach (Employee::all()->sortBy('lastname')->sortBy('firstname')->sortBy('title') as $e)
                        <option value="{{ $e->id }}" {{ old('employee_id') == $e->id || $employeeID == $e->id ? 'selected' : null }}>{{ $e->title . ' - ' . $e->firstname . ' ' . $e->lastname }}</option>
                    @endforeach
                    <option value="" {{ old('employee_id') || $employeeID ? null : 'selected' }}>-- None --</option>
                </select>
            </div>
            <div class="form-group request__flex-container">
                <div class="request__flex request__flex--left">
                    <label for="booking_month_year">Month & Year <span class="request__validate">(Select to go to month)</span></label>
                    <select name="month_year" id="booking_month_year" class="form-control request__input" onchange="showRedirect('.loading', '/bookings/' + this.value + '/new/{{ $employeeID ? $employeeID : null }}')">
                        @foreach ($months as $month)
                            <option value="{{ $month->format('m-Y') }}" {{ $date->format('m-Y') == $month->format('m-Y') ? 'selected' : null }}>{{ $month->format('F Y') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="request__flex request__flex--right">
                    <label for="booking_day">Day <span class="request__validate"></span></label>
                    <select name="day" id="booking_day" class="form-control request__input">
                        @for ($day = 1; $day <= $date->endOfMonth()->day; $day++)
                            <option value="{{ $day }}" {{ old('day') == $day ? 'selected' : null }}>{{ $day }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="booking_activity">Activity <span class="request__validate">(Name - Duration)</span></label>
                <select name="activity_id" id="booking_activity" class="form-control request__input">
                    @foreach (Activity::all()->sortBy('duration')->sortBy('name') as $activity)
                        <option value="{{ $activity->id }}" {{ old('activity_id') == $activity->id ? 'selected' : null }}>{{ $activity->name . ' - ' . $activity->duration }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="booking_start_time">Start Time <span class="request__validate">(24 hour format)</span></label>
                <input name="start_time" type="text" id="booking_start_time" class="form-control request__input" placeholder="hh:mm" value="{{ old('start_time') ? old('start_time') : '09:00' }}" masked-time>
            </div>
            <button class="btn btn-lg btn-primary btn-block btn--margin-top">Add Booking</button>
        </form>
    </div>
    <hr>
    <h1 class="text-center margin-bottom-three">{{ $date->format('F Y') }}</h1>
    @include('shared.calendar', [
        'pDate' => $date,
        'items' => $roster,
        'type' => 'customer'
    ])
@endsection