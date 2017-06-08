@extends('admin.layout')
@section('content')
    <div class="container product-category">
        <h1><i class="fa fa-edit"></i> Edit Product Category</h1>
        <div data-product-categories="{{ json_encode($view_vars['extra_data']['parent_categories']) }}"
             data-product-category-data="{{ json_encode($view_vars['record_data']) }}" id="product_category_edit_form"></div>
    </div>
@endsection