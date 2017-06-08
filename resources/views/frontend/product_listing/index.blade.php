@php
    $use_cache = (env('APP_ENV') == 'production');
    if(!empty($listing_data['banner']))
        $banner_image = ($use_cache) ? "/imagecache/825x272-0{$listing_data['banner']}" : $listing_data['banner'];
@endphp
@extends('frontend.layout')

@section('javascript-globals')
    @parent
    <script>
        window.sogood.reactjs.listing_data = {!! json_encode($listing_data) !!};
    </script>
@endsection

@section('content')
    <div class="container product-listing-page">
        <div id="product_listing_content">
            <div data-reactroot="" class="row">
                <div class="col-lg-3 facet-section">
                    @if(!empty($listing_data['sub_categories']))
                        <div class="facet-section-block"><h4>{{ $listing_data['title'] }}</h4>
                            <ul class="hidden-md-down">
                                @foreach($listing_data['sub_categories'] as $sub_category)
                                    <li><a href="/category/{{ $sub_category['slug'] }}">{{ $sub_category['name'] }}</a></li>
                                @endforeach
                            </ul>
                            <div class="form-group hidden-lg-up">
                                <select class="form-control">
                                    <option value="">Select Sub-Category</option>
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="facet-section-block">
                        <div>
                            <div class="hidden-md-down"><h4>By Price</h4>
                                <ul class="price-filter">
                                    <li class="selected"><a data-filter="all" href="">All Prices</a></li>
                                    <li class=""><a data-filter="0_25" href="">$0 - $25</a></li>
                                    <li class=""><a data-filter="25_50" href="">$25 - $50</a></li>
                                    <li class=""><a data-filter="50_75" href="">$50 - $75</a></li>
                                    <li class=""><a data-filter="75_100" href="">$75 - $100</a></li>
                                    <li class=""><a data-filter="100_*" href="">$100+</a></li>
                                </ul>
                            </div>
                            <div class="hidden-lg-up"><label class="form-control-label" style="font-weight: bold;">Filter
                                    By Price</label>
                                <div class="form-group"><select class="form-control">
                                        <option value="all">All Prices</option>
                                        <option value="0_25">$0 - $25</option>
                                        <option value="25_50">$25 - $50</option>
                                        <option value="50_75">$50 - $75</option>
                                        <option value="75_100">$75 - $100</option>
                                        <option value="100_*">$100+</option>
                                    </select></div>
                            </div>
                        </div>
                    </div>
                    <div class="facet-section-block">
                        @if(!empty($listing_data['brand_facets']))
                            <div class="facet-section-block brand-facets">
                                <h4>Popular Brands</h4>
                                <ul class="brand-facets">
                                    @foreach($listing_data['brand_facets'] as $brand_facet)
                                        <li><a href="/site-search?keyword={{ urlencode(key($brand_facet)) }}">{{ key($brand_facet) }} ({{ current($brand_facet) }})</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-9">
                    @if(!empty($banner_image))
                        <div class="row product-listing-banner">
                            <div class="col-sm-12">
                                <img class="img-fluid" src="{{ $banner_image }}">
                            </div>
                        </div>
                    @endif
                    <div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div style="float: right;">
                                    <div class="pagination-controls"><a href=""><i class="fa fa-arrow-circle-left"></i></a>
                                        <select></select><a href=""><i class="fa fa-arrow-circle-right"></i></a></div>
                                </div>
                                <div style="float: right; margin-right: 20px;"><select style="width: 100%;"></select></div>
                            </div>
                        </div>
                        <div class="row">
                            @if(!empty($listing_data['products']))
                                @foreach($listing_data['products'] as $product)
                                    @php $product = $product->getData(); @endphp
                                    @if(!empty($product['image']))
                                        @php $image_url = ($use_cache) ? "/imagecache/174x240-0{$product['image']}" : $product['image']; @endphp
                                    @endif
                                    <div class="col-sm-4">
                                        <div class="mini-buy-box">
                                            @if(!empty($image_url))
                                                <a class="mini-buy-box-image-link" href="/{{ $product['slug'] }}">
                                                    <img src="{{ $image_url }}">
                                                </a>
                                            @endif
                                            <a class="product-name-section" href="/{{ $product['slug'] }}"><p class="brand-name">{{ $product['brand'] }}</p>
                                                <p class="product-name">{{ $product['name'] }}</p></a>
                                            <div class="product-price-section">
                                                <p class="product_price">
                                                    @if(!empty($product['list_price']) && is_numeric($product['list_price']) && $product['list_price'] > 0)
                                                        <span class="list-price">{{ money($product['list_price']) }}</span>
                                                    @endif
                                                    <span class="store-price">{{ money($product['store_price']) }}</span>
                                                </p>
                                            </div>
                                            <a href="/{{ $product['slug'] }}" class="add-to-cart">Add To Cart</a>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="row" style="margin-top: 30px;">
                            <div class="col-sm-12">
                                <div style="float: right;">
                                    <div class="pagination-controls"><a href=""><i class="fa fa-arrow-circle-left"></i></a>
                                        <select></select>
                                        <a href=""><i class="fa fa-arrow-circle-right"></i></a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection