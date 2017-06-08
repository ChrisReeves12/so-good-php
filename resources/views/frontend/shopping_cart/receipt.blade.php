@extends('frontend.layout')
@section('head-styles')
    <style>
        .grand-total-section {
            color: darkgreen;
            font-size: 20px;
            font-weight: bold;
        }
    </style>
@endsection
@section('content')
    <div class="container receipt-container">
        <h2 style="margin-bottom: 20px; margin-top: 30px;">Thank You For Your Order</h2>
        <h4>Order Number: S{{ $sales_order->id }}</h4>
        <h5>Order Date: {{ $sales_order->created_at->format('F d, Y') }}</h5>

        <div class="row address-section">
            <div class="col-sm-6">
                <h4>Billing Address</h4>
                @if(!empty($sales_order->parent_transaction->billing_address->first_name) && !empty($sales_order->parent_transaction->billing_address->last_name))
                    {{ $sales_order->parent_transaction->billing_address->first_name }} {{ $sales_order->parent_transaction->billing_address->last_name }}
                @endif
                <p>
                    {{ $sales_order->parent_transaction->billing_address->line_1 }}<br/>
                    {{ $sales_order->parent_transaction->billing_address->line_2 }}<br/>
                    {{ $sales_order->parent_transaction->billing_address->city }}
                    , {{ $sales_order->parent_transaction->billing_address->state }} {{ $sales_order->parent_transaction->billing_address->zip }}
                    <br/>
                </p>
            </div>
            <div class="col-sm-6">
                @if(!empty($sales_order->parent_transaction->shipping_address))
                    <h4>Shipping Address</h4>
                    @if(!empty($sales_order->parent_transaction->shipping_address->first_name) && !empty($sales_order->parent_transaction->shipping_address->last_name))
                        {{ $sales_order->parent_transaction->shipping_address->first_name }} {{ $sales_order->parent_transaction->shipping_address->last_name }}
                    @endif
                    <p>
                        {{ $sales_order->parent_transaction->shipping_address->line_1 }}<br/>
                        {{ $sales_order->parent_transaction->shipping_address->line_2 }}<br/>
                        {{ $sales_order->parent_transaction->shipping_address->city }}
                        , {{ $sales_order->parent_transaction->shipping_address->state }} {{ $sales_order->parent_transaction->shipping_address->zip }}
                        <br/>
                    </p>
                @endif
            </div>
        </div>
        <h5>Order Totals:</h5>
        <table class="table">
            <tr>
                <td>Product Total:</td>
                <td>${{ number_format($sales_order->parent_transaction->sub_total, 2) }}</td>
            </tr>
            @if(!empty($sales_order->parent_transaction->discount_amount) || $sales_order->parent_transaction->discount_amount < 0)
                <tr>
                    <td>Discount Amount:</td>
                    <td>${{ number_format($sales_order->parent_transaction->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td>Shipping Total ({{ $shipping_method_display }}):</td>
                <td>${{ number_format($sales_order->parent_transaction->shipping_total, 2) }}</td>
            </tr>
            <tr>
                <td>Sales Tax:</td>
                <td>${{ number_format($sales_order->parent_transaction->tax, 2) }}</td>
            </tr>
            @if(!empty($sales_order->parent_transaction->gift_card_amount) || $sales_order->parent_transaction->gift_card_amount > 0)
                <tr>
                    <td>Gift Card:</td>
                    <td>${{ number_format($sales_order->parent_transaction->gift_card_amount * -1, 2) }}</td>
                </tr>
            @endif
            <tr class="grand-total-section">
                <td>{{ $sales_order->shipping_calc_needed ? 'Grand Total (Before Shipping):' : 'Grand Total:' }}</td>
                <td>${{ number_format($sales_order->parent_transaction->total, 2) }}</td>
            </tr>
        </table>
    </div>
@endsection