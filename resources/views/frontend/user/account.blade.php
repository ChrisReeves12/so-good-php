@extends('frontend.layout')

@section('javascript-globals')
    @parent
    <script>
        window.sogood.reactjs.user_data = {!! json_encode($user_data) !!};
    </script>
@endsection

@section('content')
    <div class="container account-container">
        <h2>My Account</h2>
        <p>Use the form below to make modifications to your account.</p>
        <div id="user_account_form"> </div>
</div>
@endsection