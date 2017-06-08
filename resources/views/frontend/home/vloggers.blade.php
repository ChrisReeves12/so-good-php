@extends('frontend.layout')

@section('head-styles')
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
@endsection

@section('content')
    <div class="vloggers-section container">
        <div class="row">
            <div class="col-xs-12 banner">
                <img class="img-fluid" src="/assets/img/layout/frontend/mainbanner.jpg"/>
                <h1>Beauty Vloggers</h1>
                <h2>Give their honest opinions about products of their choice</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 lower-title-section">
                <h2>Discover Vloggers</h2>
                <h3>Click on a vlogger below to see their video reviews.</h3>
            </div>
        </div>
        <div class="row vlogger-list-section">
            @foreach($vloggers as $vlogger)
                <div class="col-lg-4 col-sm-6 vlogger-entry">
                    <a href="/{{ $vlogger->slug }}">
                        <img src="{{ $vlogger->list_page_image_url }}"/>
                    </a>
                    <p class="vlogger-name">{{ $vlogger->name }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection