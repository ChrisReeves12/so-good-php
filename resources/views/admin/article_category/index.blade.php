@extends('admin.layout')
@section('content')
    <div class="container article-category-page">
        <h1><i class="fa fa-edit"></i> Edit Article Category</h1>
        <div data-initial-data="{{ json_encode($view_vars['record_data']) }}" id="article_category_section"></div>
    </div>
@endsection