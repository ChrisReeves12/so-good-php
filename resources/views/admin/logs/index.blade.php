@extends('admin.layout')
@section('content')
    <div class="container">
        <h1>Logs</h1>
        <div id="logs"></div>
    </div>
@endsection
@section('javascript-globals')
    <script>
        window.sogood.reactjs.initial_data = {!! json_encode($log_data) !!};
    </script>
@endsection