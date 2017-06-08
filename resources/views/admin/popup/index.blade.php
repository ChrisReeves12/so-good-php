@extends('admin.layout')
@section('content')
    <div class="popup-page container">
        <h1><i class="fa fa-edit"></i> Edit Popup</h1>
        <div id="popup_form"></div>
    </div>
@endsection
@section('javascript-globals')
    <script>
        window.sogood.reactjs.initial_data = {!! json_encode($view_vars['record_data']) !!};
    </script>
    <script src="/ext/ckeditor/ckeditor.js"></script>
    <script src="/ext/ace/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
@endsection