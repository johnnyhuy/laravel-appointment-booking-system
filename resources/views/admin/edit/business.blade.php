@extends('layouts.dashboard')

@section('content')
<body class="dashboard">
    <div class="dash__block">
        <a class="btn btn-lg btn-primary pull-right" href="/admin">Back</a>
        <h1 class="dash__header">Edit Business Information</h1>
        <h4 class="dash__description">Update the Business Information</h4>
        @include('shared.error_message')
        @include('shared.session_message')
        <form class="request" method="POST" href="/admin/edit">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="inputBusinessName">Business Name</label>
                <input name="businessname" type="text" id="inputBusinessName" class="form-control request__input" value="{{ $business->business_name }}" autofocus>
            </div>
            <div class="form-group">
                <label for="inputFirstName">First Name</label>
                <input name="firstname" type="text" id="inputFirstName" class="form-control request__input" placeholder="First Name" value="{{ $business->firstname }}" autofocus>
            </div>
            <div class="form-group">
                <label for="inputLastName">Last Name</label>
                <input name="lastname" type="text" id="inputLastName" class="form-control request__input" placeholder="Last Name" value="{{ $business->lastname }}" autofocus>
            </div>
            <div class="form-group">
                <label for="inputPhone">Phone <span class="request__validate">(at least 10 characters)</span></label>
                <input name="phone" type="text" id="inputPhone" class="form-control request__input" placeholder="Phone" value="{{ $business->phone }}" autofocus>
            </div>
            <div class="form-group">
                <label for="inputAddress">Address <span class="request__validate">(at least 6 characters)</span></label>
                <input name="address" type="text" id="inputAddress" class="form-control request__input" placeholder="Address" value="{{ $business->address }}" autofocus>
            </div>
            <button class="btn btn-lg btn-primary btn-block margin-top-two">Update</button>
        </form>
    </div>
</body>
@endsection