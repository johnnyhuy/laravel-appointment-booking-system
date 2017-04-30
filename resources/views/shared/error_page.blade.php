@extends('layouts.dashboard')

@section('content')
    <div class="notice">
        <span class="glyphicon glyphicon-thumbs-down notice__icon" aria-hidden="true"></span>
        <h1 class="notice__message">{{ $message }}</h1>
        <h4 class="notice__description">{!! $subMessage !!}</h4>
    </div>
@endsection