@extends('admin.layout')
@section('content')
    <div class="container product-category-list">
        <h1><i class="fa fa-list"></i> Product Categories</h1>
        <div data-initial-data="{{ json_encode($parent_categories) }}" id="product_category_list"></div>
    </div>
@endsection