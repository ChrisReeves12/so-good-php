<!DOCTYPE html>
<html>
<head>
    <title>{{ business('store_name') }} | Administration</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="/css/admin/combined.min.css?v={{ business('static_resource_version') }}" media="all" type="text/css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body>
    <div class="invoice-section">
        <div class="logo-section">
            <div class="logo">
                <img class="img-responsive" src="{{ layout_assets('header_logo.png') }}"/>
            </div>
            <div class="title">
                <h1>Invoice</h1>
            </div>
        </div>
        <div class="address-date-section">
            <div class="store-address">
                <h2>{{ business('store_name') }}</h2>
                {{ business('store_address')['line_1'] }}<br/>
                {{ business('store_address')['city'] }}, {{ business('store_address')['state'] }} {{ business('store_address')['zip'] }}<br/>
                @foreach(business('store_phones') as $phone)
                    {{ $phone }}<br/>
                @endforeach
                <strong><br/>Shipping Method</strong><br/>
                {{ $invoice_data['shipping_method'] }}
            </div>
            <div class="date-customer">
                <strong>ORDER #: {{ $invoice_data['formatted_order_id'] }}</strong><br/>
                Order Date: {{ $invoice_data['date_created'] }}<br/>
                Customer Email: {{ $invoice_data['email'] }}
                @if(!empty($invoice_data['phone']))
                    <br/>Phone: {{ $invoice_data['phone'] }}
                @endif
            </div>
        </div>
        <div class="customer-address-section">
            <div class="shipping">
                <h4>Ship To</h4>
                @if($invoice_data['billing_address'] == $invoice_data['shipping_address'])
                    {{ $invoice_data['name'] }}<br/>
                @endif
                @if(!empty($invoice_data['shipping_address']->company))
                    {{ $invoice_data['shipping_address']->company }}<br/>
                @endif
                {{ $invoice_data['shipping_address']->line_1 }}<br/>
                @if(!empty($invoice_data['shipping_address']->line_2))
                    {{ $invoice_data['shipping_address']->line_2 }}<br/>
                @endif
                {{ $invoice_data['shipping_address']->city }}, {{ $invoice_data['shipping_address']->state }} {{ $invoice_data['shipping_address']->zip }}
            </div>
            <div class="billing">
                <h4>Bill To</h4>
                {{ $invoice_data['name'] }}<br/>
                @if(!empty($invoice_data['billing_address']->company))
                    {{ $invoice_data['billing_address']->company }}<br/>
                @endif
                {{ $invoice_data['billing_address']->line_1 }}<br/>
                @if(!empty($invoice_data['billing_address']->line_2))
                    {{ $invoice_data['billing_address']->line_2 }}<br/>
                @endif
                {{ $invoice_data['billing_address']->city }}, {{ $invoice_data['billing_address']->state }} {{ $invoice_data['billing_address']->zip }}
            </div>
        </div>
        <div class="list-items-section">
            <table class="table">
                <tr>
                    <th>Quantity</th>
                    <th>Sku</th>
                    <th>Description</th>
                    <th>Details</th>
                    <th>Unit Price</th>
                    <th>Sub-Total</th>
                </tr>
                @foreach($invoice_data['items'] as $item)
                    <tr>
                        <td>{{ $item['qty'] }}</td>
                        <td>{{ $item['sku'] }}</td>
                        <td>{{ $item['desc'] }}</td>
                        <td class="details">{!! $item['details'] !!}</td>
                        <td>{{ money($item['price']) }}</td>
                        <td>{{ money($item['sub_total']) }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
        <div class="total-section">
            <table>
                <tr>
                    <td>Product Total:</td>
                    <td>{{ money($invoice_data['sub_total']) }}</td>
                </tr>
                @if(!empty($invoice_data['discount']) && $invoice_data['discount'] > 0)
                    <tr>
                        <td>Discount:</td>
                        <td>{{ money($invoice_data['discount']) }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Sales tax:</td>
                    <td>{{ money($invoice_data['tax']) }}</td>
                </tr>
                <tr>
                    <td>Shipping:</td>
                    <td>{{ is_numeric($invoice_data['shipping']) ? money($invoice_data['shipping']) : $invoice_data['shipping'] }}</td>
                </tr>
                <tr class="grand-total">
                    <td>Grand Total:</td>
                    <td>{{ money($invoice_data['total']) }}</td>
                </tr>
            </table>
        </div>
    </div>
    <script>
        window.print();
    </script>
</body>
</html>