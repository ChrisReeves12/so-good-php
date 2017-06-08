<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
</head>
<body style="font-family: 'Roboto', sans-serif;">
<a href="https://sogoodbb.com" style="text-decoration: none;">
    <h2 style="color: #e9168d">So Good Shop</h2>
</a>
<h1>Your Recent So Good Order</h1>
<h3>Order # S{{ $sales_order->id }}</h3>
<div style="width: 100%; height: 200px;">
    <div style="display: inline-block; float: left;">
        <h4 style="margin: 0;">Billing Address</h4>
        <p style="margin: 0;">
            {{ $sales_order->parent_transaction->first_name }}<br/>
            Email: {{ $sales_order->parent_transaction->email }}<br/>
            Phone: {{ $sales_order->parent_transaction->phone_number }}<br/>
            {{ $sales_order->parent_transaction->billing_address->line_1 }}<br/>
            @if(!empty($sales_order->parent_transaction->billing_address->line_2))
                {{ $sales_order->parent_transaction->billing_address->line_2 }}<br/>
            @endif
            {{ $sales_order->parent_transaction->billing_address->city }} {{ $sales_order->parent_transaction->billing_address->state }}
            , {{ $sales_order->parent_transaction->billing_address->zip }}
        </p>
    </div>
    <div style="display: inline-block; float: right;">
        <h4 style="margin: 0;">Shipping Address</h4>
        <p style="margin: 0;">
            @if(empty($sales_order->parent_transaction->shipping_address))
                Same As Billing
            @else
                {{ $sales_order->parent_transaction->shipping_address->line_1 }}<br/>
                @if(!empty($sales_order->parent_transaction->shipping_address->line_2))
                    {{ $sales_order->parent_transaction->shipping_address->line_2 }}<br/>
                @endif
                {{ $sales_order->parent_transaction->shipping_address->city }} {{ $sales_order->parent_transaction->shipping_address->state }}
                , {{ $sales_order->parent_transaction->shipping_address->zip }}
            @endif
        </p>
    </div>
</div>
<table style="width: 100%; margin-bottom: 60px;">
    <tr>
        <th style="text-align: left; padding: 5px 10px; background-color: #bfbfbf;">Sku</th>
        <th style="text-align: left; padding: 5px 10px; background-color: #bfbfbf;">Product Name</th>
        <th style="text-align: left; padding: 5px 10px; background-color: #bfbfbf;">Unit Price</th>
        <th style="text-align: left; padding: 5px 10px; background-color: #bfbfbf;">Tax</th>
        <th style="text-align: left; padding: 5px 10px; background-color: #bfbfbf;">Quantity</th>
        <th style="text-align: left; padding: 5px 10px; background-color: #bfbfbf;">Line Total</th>
    </tr>
    @foreach($sales_order->parent_transaction->transaction_line_items as $tli)
        <tr>
            <td style="padding: 5px 10px;">{{ $tli->item->sku }}</td>
            <td style="padding: 5px 10px;">{{ $tli->name }}</td>
            <td style="padding: 5px 10px;">${{ number_format($tli->unit_price, 2) }}</td>
            <td style="padding: 5px 10px;">${{ number_format($tli->tax, 2) }}</td>
            <td style="padding: 5px 10px;">{{ $tli->quantity }}</td>
            <td style="padding: 5px 10px;">${{ number_format($tli->total_price, 2) }}</td>
        </tr>
    @endforeach
</table>
<table style="width: 100%; border-collapse: collapse; border: 1px solid #d8d8d8; background-color: #f1f1f1;">
    <tr>
        <td style="padding: 7px 9px;">Sub-Total:</td>
        <td style="padding: 7px 9px;">${{ number_format($sales_order->parent_transaction->sub_total, 2) }}</td>
    </tr>
    @if(!empty($sales_order->parent_transaction->discount_amount) && $sales_order->parent_transaction->discount_amount > 0)
        <tr>
            <td style="padding: 7px 9px;">Discount:</td>
            <td style="padding: 7px 9px;">
                -${{ number_format($sales_order->parent_transaction->discount_amount, 2) }}</td>
        </tr>
    @endif
    <tr>
        <td style="padding: 7px 9px;">Tax:</td>
        <td style="padding: 7px 9px;">${{ number_format($sales_order->parent_transaction->tax, 2) }}</td>
    </tr>
    <tr>
        <td style="padding: 7px 9px;">Shipping ({{ $shipping_method_name }}):</td>
        <td style="padding: 7px 9px;">${{ number_format($sales_order->parent_transaction->shipping_total, 2) }}</td>
    </tr>
    <tr style="font-size: 22px; font-weight: bold; color: darkgreen;">
        <td style="padding: 7px 9px;">Total:</td>
        <td style="padding: 7px 9px;">${{ number_format($sales_order->parent_transaction->total, 2) }}</td>
    </tr>
</table>
<div style="width: 100%; display: inline-block; float: left;">
    <h4 style="margin: 0;">
        * You will receive an email with your tracking number when your order is shipped. Thank you!
    </h4>
</div>
</body>
</html>