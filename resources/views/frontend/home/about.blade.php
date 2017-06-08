@extends('frontend.layout')
@section('content')
    <div class="container about-us-container">
        <div class="row about-us-photo">
            <div class="col-12">
                <img class="img-fluid" src="{{ layout_assets('about_us_photo.jpg') }}"/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <h2>Who We Are</h2>
                <p class="about-content">Established in 2000, SO GOOD has been providing a unique shopping experience for women of all ages.
                    At SO GOOD, you can expect a plethora of beautiful things: wigs, weaves, hair products, jewelry and more.
                    We curate products with our customers in mind: confident, fun women. You can look fabulous and
                    elegant without breaking the bank!</p>
            </div>
            <div class="col-md-4">
                <img class="img-fluid" src="{{ layout_assets('about_us_photo2.jpg') }}"/>
            </div>
        </div>
    </div>
@endsection