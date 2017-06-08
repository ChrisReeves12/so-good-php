@php $use_cache = (env('APP_ENV') == 'production'); @endphp
@extends('frontend.layout')

@section('head-styles')
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
@endsection

@section('content')
    <div class="container affiliate">
        <div class="row hidden-lg-up">
            <div class="col-xs-12">
                <div class="row mobile-bio-section">
                    <div class="col-xs-5 col-md-3">
                        <a class="profile-pic" target="_blank" href="">
                            <img class="img-fluid" src="{{ $selected_affiliate->main_image_url }}"/>
                        </a>
                    </div>
                    <div class="col-xs-7 col-md-9">
                        <h4>{{ $selected_affiliate->name }}</h4>
                        <p class="bio">{{ $selected_affiliate->short_bio }}</p>
                        <div class="social-media">
                            @if(!empty($selected_affiliate->social_media_links))
                                <ul>
                                    @foreach($selected_affiliate->social_media_links as $social_media_link)
                                        <li>
                                            <a href="{{ $social_media_link['url'] }}">
                                                <img class="img-fluid" src="{{ layout_assets($social_network_icons[$social_media_link['type']]) }}"/>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-xs-12">
                <h1>My Product Reviews</h1>
                @foreach($selected_affiliate->videos as $video)
                    @php
                        $id = preg_match_all('/v=(.*)/i', $video['url'], $matches);
                        $url = 'https://youtube.com/embed/' . $matches[1][0];
                    @endphp
                <div class="row video-row">
                    <div class="col-md-9">
                        <div class="video-container">
                            <iframe src="{{ $url }}" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="col-md-3 video-product">
                        <h4>Shop My Look:</h4>
                        <a class="product-img-link" href="{{ $video['product_url'] . '?channel=' . urlencode($selected_affiliate->affiliate_tag) }}">
                            <img class="img-fluid" src="{{ $selected_affiliate->getImageUrl($video['img']) }}"/>
                        </a>
                        <p class="desc">{{ $video['product_name'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="col-xs-4 hidden-md-down">
                <div class="bio-section">
                    <a class="profile-pic" target="_blank" href="">
                        <img class="img-fluid" src="{{ $selected_affiliate->main_image_url }}"/>
                    </a>
                    <h4>{{ $selected_affiliate->name }}</h4>
                    <p class="bio">{{ $selected_affiliate->short_bio }}</p>
                    <div class="social-media">
                        @if(!empty($selected_affiliate->social_media_links))
                        <h5>Follow Me:</h5>
                        <ul>
                            @foreach($selected_affiliate->social_media_links as $social_media_link)
                                <li>
                                    <a href="{{ $social_media_link['url'] }}">
                                        <img class="img-fluid" src="{{ layout_assets($social_network_icons[$social_media_link['type']]) }}"/>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection