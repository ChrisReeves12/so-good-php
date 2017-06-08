@extends('admin.layout')
@section('content')
    <div class="container affiliate-page">
        <h1><i class="fa fa-edit"></i> Edit Affiliate</h1>
        <div id="affiliate_form"></div>
    </div>
@endsection
@section('javascript-globals')
    <script src="/ext/ace/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
    <script>
        window.sogood.reactjs.initial_data = {!! json_encode($view_vars['record_data']) !!};
    </script>
@endsection