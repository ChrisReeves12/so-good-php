@php $use_cache = (env('APP_ENV') == 'production'); @endphp
<nav class="top-nav hidden-md-down">
    <div class="container">
        <ul>
            @if(empty(current_user()))
                <li><a href="/register"><i class="fa fa-pencil-square"></i> Create An Account</a></li>
                <li><a href="/sign-in"><i class="fa fa-sign-in"></i> Sign In</a></li>
            @else
                <li>Hello, {{ current_user('first_name') }}</li>
                <li><a href="/account"><i class="fa fa-user"></i> My Account</a></li>
                <li><a href="/sign-out" class="do-delete"><i class="fa fa-sign-out"></i> Sign Out</a></li>
                @if(current_user('role') == 'admin')
                    <li><a href="/admin"><i class="fa fa-gear"></i> Admin Panel</a></li>
                @endif
            @endif
            <li><a class="gift_card_balance_link" href=""><i class="fa fa-credit-card-alt"></i> Gift Card Balance</a></li>
        </ul>
        <div class="shopping_cart_display"
             data-initial-data="{{ !empty($shopping_cart) ? $shopping_cart->toJson() : '[]' }}"></div>
    </div>
</nav>
<header>
    <div class="container">
        <div class="row">
            <div class="col-lg-3 hidden-md-down">
                <div class="newsletter_signup"></div>
            </div>
            <div class="col-lg-6 col-xs-8 header-logo left-logo-section">
                <a href='/'>
                    <img src="{{ layout_assets('header_logo.png', ($use_cache ? '/imagecache/506x80-0' : '')) }}"/>
                </a>
            </div>
            <div class="col-lg-3 col-xs-4">
                <div id="freeshipping_banner"><img src="{{layout_assets('freeshippingbanner.png')}}"/></div>
                <div class='header_search_field'></div>
            </div>
        </div>
        @include('frontend.components.top_navigation')
        <div class="row hidden-lg-up">
            <div class='col-xs-12 newsletter_signup'></div>
        </div>
    </div>
</header>