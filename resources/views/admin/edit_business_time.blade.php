@extends('layouts.dashboard')

@section('content')
    <div class="dash__block">
        <h1 class="dash__header">Edit Business Time</h1>
        <h4 class="dash__description">Edit an existing business time below.</h4>
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
        <form class="request" method="POST" action="/admin/times/{{ $bTime->id }}">
            {{ method_field('PUT') }}
            {{ csrf_field() }}
            <div class="form-group">
                <label>Day</label>
                <p>{{ ucfirst($bTime->day) }}</p>
            </div>
            <div class="form-group request__flex-container">
                <div class="request__flex request__flex--left">
                    <label for="times_start_time">Start Time <span class="request__validate">(24 hour format e.g. 17:00 = 05:00 PM)</span></label>
                    <input name="start_time" type="time" id="times_start_time" class="form-control request__input" value="{{ old('start_time') ? old('start_time') : '09:00' }}" autofocus>
                </div>
                <div class="request__flex request__flex--right">
                    <label for="times_end_time">End Time</label>
                    <input name="end_time" type="time" id="times_end_time" class="form-control request__input" value="{{ old('end_time') ? old('end_time') : '17:00' }}" autofocus>
                </div>
            </div>
            <button class="btn btn-lg btn-primary btn-block btn--margin-top">Edit Business Time</button>
        </form>
    </div>
@endsection