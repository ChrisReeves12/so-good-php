@extends('admin.layout')
@section('head-scripts')
    <script src="/ext/ckeditor/ckeditor.js"></script>
@endsection
@section('content')
    <div class="container article-page">
        <h1><i class="fa fa-edit"></i> Edit Article</h1>
        <div data-article-categories="{{ json_encode($view_vars['extra_data']['article_categories']) }}"
             data-initial-data="{{ json_encode($view_vars['record_data']) }}" id="article_page_section"></div>
    </div>
@endsection