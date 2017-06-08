@extends('admin.layout')
@section('content')
    <div class="container newsletter-sub">
        <h1><i class="fa fa-edit"></i> Edit Newsletter Subscriber</h1>
        <div id="subscriber_form"></div>
    </div>
@endsection
@section('javascript-globals')
    <script>
        window.sogood.reactjs.initial_data = {!! json_encode($view_vars['record_data']) !!};
    </script>
@endsection