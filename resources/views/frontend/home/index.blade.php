@php $use_cache = (env('APP_ENV') == 'production'); @endphp
@extends('frontend.layout')
@section('content')
    <div class="page-home-index">
        <div class="container">
            <div class="top-banner">
                <div class="row">
                    <div class="col-xs-12">
                        <a href="{{ url('/category/haircare-men') }}" >
                            <img class='img-fluid' src="{{ layout_assets('fathersdaybannerforwebsite.png') }}" />
                        </a>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row" >
                        <div class="banner_common">
                            <img class='img-fluid' src="{{ layout_assets('giveawaywinnersbanner.png', ($use_cache ? '/imagecache/276x401-0' : '')) }}"/>
                        </div>
                        <div class="banner_common">
                            <a href='{{ url('/site-search?keyword=summer') }}'>
                                <img class='img-fluid' src="{{ layout_assets('bannermiddle.png', ($use_cache ? '/imagecache/556x405-0' : '')) }}"/>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="banner_common">
                            <a href='{{ url('/beauty-vloggers') }}' >
                                <img class='img-fluid' src="{{ layout_assets('bannerbottomleft.png') }}"/>
                            </a>
                        </div>
                        <div class="banner_common">
                            <a href='{{ url('/ceci-fidget-spinner') }}'>
                                <img class='img-fluid' src="{{ layout_assets('fidgetspinneredit.gif') }}"/>
                            </a>
                        </div>
                    </div>
                    <div class="row hidden-sm-up">
                        <div class="banner_common">
                            <a href='{{ url('/category/hair-care') }}'>
                                <img class='img-fluid' src="{{ layout_assets('mobileversionhaircare.png', ($use_cache ? '/imagecache/273x149-0' : '')) }}"/>
                            </a>
                        </div>
                        <div class="banner_common">
                            <a href='{{ url('/bobbi-boss-synthetic-lace-front-wig-mlf136-yara') }}'>
                                <img class='img-fluid' src="{{ layout_assets('yaramobile.png', ($use_cache ? '/imagecache/236x149-0' : '')) }}"/>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 hidden-sm-down">
                    <div class="row">
                        <div class="banner_common">
                            <a href='{{ url('/category/hair-care') }}'>
                                <img class='img-fluid' src="{{ layout_assets('haircare.png', ($use_cache ? '/imagecache/277x221-0' : '')) }}"/>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="banner_common">
                            <a href='{{ url('/bobbi-boss-synthetic-lace-front-wig-mlf136-yara') }}'>
                                <img class='img-fluid' src="{{ layout_assets('bannerbottomright.png', ($use_cache ? '/imagecache/277x429-0' : '')) }}"/>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class='home-content-area mx-auto'>
                <div class='home-categories-section'>
                    @if($active_vloggers->isNotEmpty())
                        <div class="row">
                            <a href='{{ url('/beauty-vloggers') }}' style="color: black;">
                                <h2>vlogger picks</h2>
                            </a>
                        </div>
                        <div class="row vlogger-section">
                            @foreach($active_vloggers as $vlogger)
                                <div style="text-align: center;" class="col-xs-6 col-sm-4 col-lg-2 vlogger-entry">
                                    <a href="/{{ $vlogger->slug }}">
                                        <img src="{{ $vlogger->list_page_image_url }}"/>
                                    </a>
                                    <p class="vlogger-name">{{ $vlogger->name }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="row">
                        <h2>hot wigs</h2>
                    </div>
                    <div class="row">
                        <div data-id="hot_wigs" class="sg-courasel"></div>
                    </div>
                    <div class="row">
                        <h2>hot lace wigs</h2>
                    </div>
                    <div class="row">
                        <div data-id="hot_lacefront_wigs" class="sg-courasel"></div>
                    </div>
                    <div class="row">
                        <h2>hot braids</h2>
                    </div>
                    <div class="row">
                        @foreach($popular_braids as $popular_braid)
                            <div style="text-align: center;" class="col-xs-6 col-sm-4 col-lg-2">
                                <div class="mini-box">
                                    <a class="mini-box-image-link" href='{{ url('/' . $popular_braid->get('slug')) }}'>
                                        <img alt="{{ $popular_braid->get('name') }}" title="{{ $popular_braid->get('name') }}"
                                             src="{{ ($use_cache ? '/imagecache/160x200-0' : '') . $popular_braid->get('image') }}"/>
                                    </a>
                                    <a class="product-name-section" href='{{ url('/' . $popular_braid->get('slug')) }}'>
                                        <p class="brand-name">{{ $popular_braid->get('brand') }}</p>
                                        <p class="product-name">{{ $popular_braid->get('name') }}</p>
                                    </a>
                                    <div class="product-price-section">
                                        <p class="product-price">
                                            @if(!empty($popular_braid->get('list_price')) && $popular_braid->get('list_price') > 0)
                                                <span class="list-price">{{ money($popular_braid->get('list_price')) }}</span>
                                            @endif

                                            <span class="store-price">{{ money($popular_braid->get('store_price')) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <h2>kids</h2>
                    </div>
                    <div class="row">
                        @foreach($kid_items as $kid)
                            <div style="text-align: center;" class="col-xs-6 col-sm-4 col-lg-2">
                                <div class="mini-box">
                                    <a class="mini-box-image-link" href='{{ url('/' . $kid->get('slug')) }}'>
                                        <img alt="{{ $kid->get('name') }}" title="{{ $kid->get('name') }}"
                                             src="{{ ($use_cache ? '/imagecache/160x160-0' : '') . $kid->get('image') }}"/>
                                    </a>
                                    <a class="product-name-section" href='{{ url('/' . $kid->get('slug')) }}'>
                                        <p class="brand-name">{{ $kid->get('brand') }}</p>
                                        <p class="product-name">{{ $kid->get('name') }}</p>
                                    </a>
                                    <div class="product-price-section">
                                        <p class="product-price">
                                            @if(!empty($kid->get('list_price')) && $kid->get('list_price') > 0)
                                                <span class="list-price">{{ money($kid->get('list_price')) }}</span>
                                            @endif

                                            <span class="store-price">{{ money($kid->get('store_price')) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row">
                        <a href='{{ url('/category/wigs') }}' style="color: black;">
                            <h2>new wigs</h2>
                        </a>
                    </div>
                    <div class="row">
                        @foreach($new_wigs as $new_wig)
                            <div style="text-align: center;" class="col-xs-6 col-sm-4 col-lg-2">
                                <div class="mini-box">
                                    <a class="mini-box-image-link" href='{{ url('/' . $new_wig->get('slug')) }}'>
                                        <img alt="{{ $new_wig->get('name') }}" title="{{ $new_wig->get('name') }}"
                                             src="{{ ($use_cache ? '/imagecache/160x200-0' : '') . $new_wig->get('image') }}"/>
                                    </a>
                                    <a class="product-name-section" href='{{ url('/' . $new_wig->get('slug')) }}'>
                                        <p class="brand-name">{{ $new_wig->get('brand') }}</p>
                                        <p class="product-name">{{ $new_wig->get('name') }}</p>
                                    </a>
                                    <div class="product-price-section">
                                        <p class="product-price">
                                            @if(!empty($new_wig->get('list_price')) && $new_wig->get('list_price') > 0)
                                                <span class="list-price">{{ money($new_wig->get('list_price')) }}</span>
                                            @endif

                                            <span class="store-price">{{ money($new_wig->get('store_price')) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <a href='{{ url('/category/lace-wigs') }}' style="color: black;">
                            <h2>new lace wigs</h2>
                        </a>
                    </div>
                    <div class="row">
                        @foreach($new_lwigs as $new_lwig)
                            <div style="text-align: center;" class="col-xs-6 col-sm-4 col-lg-2">
                                <div class="mini-box">
                                    <a class="mini-box-image-link" href='{{ url('/' . $new_lwig->get('slug')) }}'>
                                        <img alt="{{ $new_lwig->get('name') }}" title="{{ $new_lwig->get('name') }}"
                                             src="{{ ($use_cache ? '/imagecache/160x200-0' : '') . $new_lwig->get('image') }}"/>
                                    </a>
                                    <a class="product-name-section" href='{{ url('/' . $new_lwig->get('slug')) }}'>
                                        <p class="brand-name">{{ $new_lwig->get('brand') }}</p>
                                        <p class="product-name">{{ $new_lwig->get('name') }}</p>
                                    </a>
                                    <div class="product-price-section">
                                        <p class="product-price">
                                            @if(!empty($new_lwig->get('list_price')) && $new_lwig->get('list_price') > 0)
                                                <span class="list-price">{{ money($new_lwig->get('list_price')) }}</span>
                                            @endif

                                            <span class="store-price">{{ money($new_lwig->get('store_price')) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row">
                        <a href='{{ url('/category/braids') }}' style="color: black;">
                            <h2>new braids</h2>
                        </a>
                    </div>
                    <div class="row">
                        @foreach($new_braids as $braid)
                            <div style="text-align: center;" class="col-xs-6 col-sm-4 col-lg-2">
                                <div class="mini-box">
                                    <a class="mini-box-image-link" href='{{ url('/' . $braid->get('slug')) }}'>
                                        <img alt="{{ $braid->get('name') }}" title="{{ $braid->get('name') }}"
                                             src="{{ ($use_cache ? '/imagecache/160x200-0' : '') . $braid->get('image') }}"/>
                                    </a>
                                    <a class="product-name-section" href='{{ url('/' . $braid->get('slug')) }}'>
                                        <p class="brand-name">{{ $braid->get('brand') }}</p>
                                        <p class="product-name">{{ $braid->get('name') }}</p>
                                    </a>
                                    <div class="product-price-section">
                                        <p class="product-price">
                                            @if(!empty($braid->get('list_price')) && $braid->get('list_price') > 0)
                                                <span class="list-price">{{ money($braid->get('list_price')) }}</span>
                                            @endif

                                            <span class="store-price">{{ money($braid->get('store_price')) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row">
                        <a href='{{ url('/category/weaves') }}' style="color: black;">
                            <h2>new weaves</h2>
                        </a>
                    </div>
                    <div class="row">
                        @foreach($new_weaves as $weave)
                            <div style="text-align: center;" class="col-xs-6 col-sm-4 col-lg-2">
                                <div class="mini-box">
                                    <a class="mini-box-image-link" href='{{ url('/' . $weave->get('slug')) }}'>
                                        <img alt="{{ $weave->get('name') }}" title="{{ $weave->get('name') }}"
                                             src="{{ ($use_cache ? '/imagecache/160x200-0' : '') . $weave->get('image') }}"/>
                                    </a>
                                    <a class="product-name-section" href='{{ url('/' . $weave->get('slug')) }}'>
                                        <p class="brand-name">{{ $weave->get('brand') }}</p>
                                        <p class="product-name">{{ $weave->get('name') }}</p>
                                    </a>
                                    <div class="product-price-section">
                                        <p class="product-price">
                                            @if(!empty($weave->get('list_price')) && $weave->get('list_price') > 0)
                                                <span class="list-price">{{ money($weave->get('list_price')) }}</span>
                                            @endif

                                            <span class="store-price">{{ money($weave->get('store_price')) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row">
                        <a href='{{ url('/category/hair-extension') }}' style="color: black;">
                            <h2>new extensions</h2>
                        </a>
                    </div>
                    <div class="row">
                        @foreach($new_extensions as $extension)
                            <div style="text-align: center;" class="col-xs-6 col-sm-4 col-lg-2">
                                <div class="mini-box">
                                    <a class="mini-box-image-link" href='{{ url('/' . $extension->get('slug')) }}'>
                                        <img alt="{{ $extension->get('name') }}" title="{{ $extension->get('name') }}"
                                             src="{{ ($use_cache ? '/imagecache/160x200-0' : '') . $extension->get('image') }}"/>
                                    </a>
                                    <a class="product-name-section" href='{{ url('/' . $extension->get('slug')) }}'>
                                        <p class="brand-name">{{ $extension->get('brand') }}</p>
                                        <p class="product-name">{{ $extension->get('name') }}</p>
                                    </a>
                                    <div class="product-price-section">
                                        <p class="product-price">
                                            @if(!empty($extension->get('list_price')) && $extension->get('list_price') > 0)
                                                <span class="list-price">{{ money($extension->get('list_price')) }}</span>
                                            @endif

                                            <span class="store-price">{{ money($extension->get('store_price')) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row">
                        <a href='{{ url('/category/hair-care') }}' style="color: black;">
                            <h2>haircare</h2>
                        </a>
                    </div>
                    <div class="row">
                        @foreach($new_haircare as $haircare)
                            <div style="text-align: center;" class="col-xs-6 col-sm-4 col-lg-2">
                                <div class="mini-box">
                                    <a class="mini-box-image-link" href='{{ url('/' . $haircare->get('slug')) }}'>
                                        <img alt="{{ $haircare->get('name') }}" title="{{ $haircare->get('name') }}"
                                             src="{{ ($use_cache ? '/imagecache/160x160-0' : '') . $haircare->get('image') }}"/>
                                    </a>
                                    <a class="product-name-section" href='{{ url('/' . $haircare->get('slug')) }}'>
                                        <p class="brand-name">{{ $haircare->get('brand') }}</p>
                                        <p class="product-name" style="font-size: 11px">{{ $haircare->get('name') }}</p>
                                    </a>
                                    <div class="product-price-section">
                                        <p class="product-price">
                                            @if(!empty($haircare->get('list_price')) && $haircare->get('list_price') > 0)
                                                <span class="list-price">{{ money($haircare->get('list_price')) }}</span>
                                            @endif

                                            <span class="store-price">{{ money($haircare->get('store_price')) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row">
                        <a href='{{ url('/category/jewelry') }}' style="color: black;">
                            <h2>jewelry</h2>
                        </a>
                    </div>
                    <div class="row">
                        @foreach($new_jewelry as $jewelry)
                            <div style="text-align: center;" class="col-xs-6 col-sm-4 col-lg-2">
                                <div class="mini-box">
                                    <a class="mini-box-image-link" href='{{ url('/' . $jewelry->get('slug')) }}'>
                                        <img alt="{{ $jewelry->get('name') }}" title="{{ $jewelry->get('name') }}"
                                             src="{{ ($use_cache ? '/imagecache/160x160-0' : '') . $jewelry->get('image') }}"/>
                                    </a>
                                    <a class="product-name-section" href='{{ url('/' . $jewelry->get('slug')) }}'>
                                        <p class="brand-name">{{ $jewelry->get('brand') }}</p>
                                        <p class="product-name">{{ $jewelry->get('name') }}</p>
                                    </a>
                                    <div class="product-price-section">
                                        <p class="product-price">
                                            @if(!empty($jewelry->get('list_price')) && $jewelry->get('list_price') > 0)
                                                <span class="list-price">{{ money($jewelry->get('list_price')) }}</span>
                                            @endif

                                            <span class="store-price">{{ money($jewelry->get('store_price')) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row">
                        <a href='{{ url('/category/misc') }}' style="color: black;">
                            <h2>miscellaneous</h2>
                        </a>
                    </div>
                    <div class="row">
                        @foreach($new_misc as $misc)
                            <div style="text-align: center;" class="col-xs-6 col-sm-4 col-lg-2">
                                <div class="mini-box">
                                    <a class="mini-box-image-link" href='{{ url('/' . $misc->get('slug')) }}'>
                                        <img alt="{{ $misc->get('name') }}" title="{{ $misc->get('name') }}"
                                             src="{{ ($use_cache ? '/imagecache/160x106-0' : '') . $misc->get('image') }}"/>
                                    </a>
                                    <a class="product-name-section" href='{{ url('/' . $misc->get('slug')) }}'>
                                        <p class="brand-name">{{ $misc->get('brand') }}</p>
                                        <p class="product-name">{{ $misc->get('name') }}</p>
                                    </a>
                                    <div class="product-price-section">
                                        <p class="product-price">
                                            @if(!empty($misc->get('list_price')) && $misc->get('list_price') > 0)
                                                <span class="list-price">{{ money($misc->get('list_price')) }}</span>
                                            @endif

                                            <span class="store-price">{{ money($misc->get('store_price')) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row">
                        <h2>top categories</h2>
                    </div>
                    <div class="row">
                        <div style="text-align: center;" class="col-xs-6 col-sm-3">
                            <a href='{{ url('/category/wigs') }}'>
                                <img class='img-fluid' src="{{ layout_assets('topcategorywigs.jpg', ($use_cache ? '/imagecache/255x296-0' : '')) }}"/>
                            </a>
                        </div>
                        <div style="text-align: center;" class="col-xs-6 col-sm-3">
                            <a href='{{ url('/category/weaves') }}'>
                                <img class='img-fluid' src="{{ layout_assets('topcategoryweaves.jpg', ($use_cache ? '/imagecache/255x296-0' : '')) }}"/>
                            </a>
                        </div>
                        <div style="text-align: center;" class="col-xs-6 col-sm-3">
                            <a href='{{ url('/category/braids') }}'>
                                <img class='img-fluid' src="{{ layout_assets('topcategorybraids.jpg', ($use_cache ? '/imagecache/255x296-0' : '')) }}"/>
                            </a>
                        </div>
                        <div style="text-align: center;" class="col-xs-6 col-sm-3">
                            <a href='{{ url('/category/hair-extension') }}'>
                                <img class='img-fluid' src="{{ layout_assets('topcategoryextensions.jpg', ($use_cache ? '/imagecache/255x296-0' : '')) }}"/>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>
        window.sogood.reactjs.courasel = {
            hot_wigs: {slides: {!! json_encode($popular_wigs) !!}},
            hot_lacefront_wigs: {slides: {!! json_encode($popular_lacewigs) !!}}
        };
    </script>
@endsection