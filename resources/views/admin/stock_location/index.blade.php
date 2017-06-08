@extends('admin.layout')
@section('content')
    <div class="container stock-location">
        <h1><i class="fa fa-edit"></i> Edit Stock Location</h1>
        <div data-initial-data="{{ json_encode($view_vars['record_data']) }}" id="stock_location_form"></div>
    </div>
@endsection