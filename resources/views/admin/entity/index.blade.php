@extends('admin.layout')
@section('content')
    <div class="container user">
        <h1><i class="fa fa-edit"></i> Edit User</h1>
        <div data-user-data="{{ json_encode($view_vars['record_data']) }}" id="user_edit_form"></div>
    </div>
@endsection