@php
    $use_cache = (env('APP_ENV') == 'production');
@endphp
@extends('frontend.layout')

@section('javascript-globals')
    @parent
    <script>
        window.sogood.reactjs.product_page_data = {!! json_encode($product_page_data) !!};
    </script>
@endsection

@section('content')
    <div class='product-detail-page'>
        <div class="container">
            @if(current_user('role') == 'admin')
                <a class="btn btn-success"
                   style="margin-bottom: 18px"
                   target="_blank"
                   href="/admin/product/{{ $product_page_data['product']['id'] }}"><i class="fa fa-edit"></i> Edit Product</a>
            @endif
            <div id='product_detail_content'>
                <div data-reactroot="">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="main-photo-section"><a href="{{ $selected_product->default_image_url }}">
                                    <img src="{{ (($use_cache) ? '/imagecache/330x460-0' : '') . $selected_product->default_image_url }}"></a>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <h1 class="product-name">{{ $selected_product->name }}</h1><h4
                                    class="brand-name">{{ $product_page_data['brand_name'] }}</h4>
                            <div class="price-section"></div>
                            <div class="sku-section"><p class="sku"></p></div>
                            <div class="product-options-section">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 product-description-section">
                            <div class="product-information-section">
                                <div class="tabs">

                                </div>
                                <div class="product-info-tab-content">
                                    <p class="tab-text-content">
                                        @if(!empty($selected_product->description))
                                            {!! $selected_product->description !!}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @if(!empty($product_page_data['recommended_products']))
                            <div class="product-rec-section"><h4>You May Also Like</h4>
                                @foreach($product_page_data['recommended_products'] as $recommended_product)
                                    <div class="col-md-2">
                                        <div class="mini-buy-box">
                                            @if(!empty($recommended_product['image']))
                                            <a class="mini-buy-box-image-link" href="/{{ $recommended_product['slug'] }}"><img
                                                        src="{{ ($use_cache ? '/imagecache/174x240-0' : '') . $recommended_product['image'] }}"></a>
                                            @endif
                                            <a class="product-name-section"
                                                    href="/{{ $recommended_product['slug'] }}"><p
                                                        class="brand-name">{{ $recommended_product['brand'] }}</p>
                                                <p class="product-name">{{ $recommended_product['name'] }}</p></a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection