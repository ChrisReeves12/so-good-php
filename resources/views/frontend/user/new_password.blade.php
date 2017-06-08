@extends('frontend.layout')
@section('content')
    <div class="container">
        <div class="col-sm-6">
            <h4>Password Reset</h4>
            <p>Use the form below to reset your password</p>
            <div id='new_password_form' data-user-id="{{ $user->id }}"></div>
        </div>
    </div>
@endsection