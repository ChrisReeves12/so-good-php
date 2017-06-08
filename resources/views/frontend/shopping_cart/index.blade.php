@extends('frontend.layout')

@section('javascript-globals')
    @parent
    <script>
        window.sogood.reactjs.shopping_cart_data = {!! empty($shopping_cart_data) ? '{}' : json_encode($shopping_cart_data) !!};
    </script>
@endsection

@section('content')
    <div class="container checkout">
        <h2 style="margin-top: 20px;">Checkout</h2>
        @if($test_order)
            <h4 style="font-size: 14px; color: #848484; margin-bottom: 13px;"><i class="fa fa-hand-stop-o"></i> Test Mode Enabled</h4>
        @endif
        <div id="checkout_form"></div>
    </div>
@endsection
@section('footer-scripts')
    <script src="https://www.paypalobjects.com/api/checkout.js"></script>
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <script type="text/javascript">
        Stripe.setPublishableKey('{{ $stripe_public_key }}');
  </script>
@endsection