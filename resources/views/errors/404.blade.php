@extends('frontend.layout')
@section('content')
    <div class="container not-found-container">
        <div>
            <img src="{{ layout_assets('404_logo.png') }}"/>
        </div>
        <div class="notice">
            <h1>OOPS! Sorry, we couldn't find the page you were looking for</h1>
            <h2><a href="/">Go To Home</a></h2>
        </div>
    </div>
@endsection