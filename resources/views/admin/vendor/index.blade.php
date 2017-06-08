@extends('admin.layout')
@section('content')
    <div class="container vendor">
        <h1><i class="fa fa-edit"></i> Edit Vendor</h1>
        <div data-vendor-data="{{ json_encode($view_vars['record_data']) }}" id="vendor_edit_form"></div>
    </div>
@endsection