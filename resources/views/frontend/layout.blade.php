<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <title>{{ business('store_name') }} | {{ $page_title or business('slogan') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="title" content="{{ business('store_name') }} - {{ business('meta_title') }}"/>
    <meta name="keywords" content="{{ business('meta_keywords') }}"/>
    <meta name="description" content="{{ business('meta_description') }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ layout_assets('favicon.png') }}" type="image/png"/>
    <link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'/>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css"/>
    <link href="/css/frontend/combined.min.css?v={{ business('static_resource_version') }}" rel="stylesheet" type="text/css" media="all" />
    @yield('head-styles')
    <meta name="use-cache" content="{{ env('APP_ENV') == 'production' ? 'true' : 'false' }}" />
    <meta name="env" content="{{ env('APP_ENV') }}"/>
    @if(env('APP_ENV') == 'production')
        <!-- Google Tag Manager -->
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','GTM-TDWN93F');</script>
            <!-- End Google Tag Manager -->
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body>
<div class="site-wrapper">
    @include('frontend.components.header')
    <div class="row">
        <div class="container">
            @if(session('flash_success'))
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <i class="fa fa-check-circle"></i> {{ session('flash_success') }}
                </div>
            @elseif(session('flash_alert'))
                <div class="alert alert-error">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <i class="fa fa-times-circle"></i> {{ session('flash_alert') }}
                </div>
            @endif
        </div>
  </div>
    <div class='content-area'>
        @if(request()->getRequestUri() !== '/')
            <div class="container">
                <div class="row header-bottom-row">
                    <div class="col-lg-8">
                        {!! breadcrumbs() !!}
                    </div>
                </div>
            </div>
        @endif
        @yield('content')
    </div>
    @include('frontend.components.footer')
</div>

<!-- Gift Card Balance Modal -->
<div class="modal fade" id="modal_gift_card_balance" tabindex="-1" role="dialog" aria-labelledby="gift_card_balance_modal" aria-hidden="true">

</div>

<!-- Add To Cart Modal -->
<div class="modal fade" id="modal_add_to_cart" tabindex="-1" role="dialog" aria-labelledby="add_to_cart_modal" aria-hidden="true"></div>

<!-- Hidden mobile menu to be revealed by mobile menu toggle -->
<div style="display: none;" data-user-data="{{ json_encode(current_user()) }}" id="side_mobile_menu"></div>
<script>
    window.sogood = window.sogood || {};
    window.sogood.reactjs = window.sogood.reactjs || {};
</script>
@yield('javascript-globals')
<script
        src="https://code.jquery.com/jquery-3.1.1.min.js"
        integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
        crossorigin="anonymous"></script>
@yield('footer-scripts')
<script src="/js/frontend/combined.min.js?v={{ business('static_resource_version') }}"></script>
</body>