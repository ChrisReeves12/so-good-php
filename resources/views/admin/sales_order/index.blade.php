@extends('admin.layout')
@section('content')
    <div class="container sales-order-page">
        <h1><i class="fa fa-edit"></i> Edit Sales Order</h1>
        <div data-order-data="{{ json_encode($view_vars['record_data']) }}" id="sales_order_form"></div>

    </div>
@endsection