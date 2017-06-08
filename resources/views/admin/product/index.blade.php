@extends('admin.layout')
@section('content')
    <div class="product-page container">
        <h1><i class="fa fa-edit"></i> Edit Product</h1>
        <div data-stocklocations="{{ json_encode($view_vars['extra_data']['stock_locations']) }}"
             data-vendors="{{ json_encode($view_vars['extra_data']['vendors']) }}"
             data-categories="{{ json_encode($view_vars['extra_data']['categories']) }}"
             data-product="{{ json_encode($view_vars['record_data']) }}"
             id="product_edit_form" ></div>

    </div>
@endsection