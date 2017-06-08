@extends('admin.layout')
@section('content')
    <div class="container gift_card">
        <h1><i class="fa fa-edit"></i> Edit Gift Card</h1>
        <div id="gift_card_edit_form"></div>
    </div>
@endsection
@section('javascript-globals')
    <script>
        window.sogood.reactjs.initial_data = {!! json_encode($view_vars['record_data']) !!};
    </script>
@endsection