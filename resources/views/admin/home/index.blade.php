@extends('admin.layout')
@section('content')
    <div class="admin-home container">
        <h1><i class="fa fa-shopping-cart"></i> Today's Orders ({{ $today_orders->count() }})</h1>
        <div class="row">
            <div class="col-sm-12">
                @if($today_orders->count() > 0)
                    <table class="table">
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Sub-Total</th>
                            <th>Total</th>
                            <th>Marketing Source</th>
                            <th>Date Ordered</th>
                        </tr>
                        @foreach($today_orders as $order)
                            <tr>
                                <td><a href="/admin/sales-order/{{ $order->sales_order_id }}"><i class="fa fa-pencil"></i> Edit
                                        Order</a></td>
                                <td>{{ $order->sales_order_id }}</td>
                                <td>{{ $order->first_name . ' ' . $order->last_name }}</td>
                                <td>{{ $order->status }}</td>
                                <td>{{ number_format($order->sub_total, 2, '.', ',') }}</td>
                                <td>{{ number_format($order->total, 2, '.', ',') }}</td>
                                <td>{{ $order->marketing_channel or 'N/A' }}</td>
                                <td>{{ human_time($order->sales_order_created_at) }}</td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    <div class="col-sm-12">
                        <h4><i class="fa fa-info-circle"></i> There are no orders for today.</h4>
                    </div>
                @endif
            </div>
        </div>
        <h1><i class="fa fa-hourglass-end"></i> Pending And Processing Orders ({{ $pending_orders->count() }})</h1>
        <div class="row">
            <div class="col-sm-12">
                @if($pending_orders->count() > 0)
                    <table class="table">
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Sub-Total</th>
                            <th>Total</th>
                            <th>Marketing Source</th>
                            <th>Date Ordered</th>
                        </tr>
                        @foreach($pending_orders as $order)
                            <tr>
                                <td><a href="/admin/sales-order/{{ $order->sales_order_id }}"><i class="fa fa-pencil"></i> Edit
                                        Order</a></td>
                                <td>{{ $order->sales_order_id }}</td>
                                <td>{{ $order->first_name . ' ' . $order->last_name }}</td>
                                <td>{{ $order->status }}</td>
                                <td>{{ number_format($order->sub_total, 2, '.', ',') }}</td>
                                <td>{{ number_format($order->total, 2, '.', ',') }}</td>
                                <td>{{ $order->marketing_channel or 'N/A' }}</td>
                                <td>{{ human_time($order->sales_order_created_at) }}</td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    <div class="col-sm-12">
                        <h4><i class="fa fa-info-circle"></i> There are no orders for today.</h4>
                    </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-sm-7">
                @if($today_users->count() > 0)
                    <h1><i class="fa fa-user"></i> Today's New Registered Customers</h1>
                    <table class="table">
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Date Joined</th>
                        </tr>
                        @foreach($today_users as $user)
                            <tr>
                                <td><a href="/admin/entity/{{ $user->id }}"><i class="fa fa-edit"></i> Edit User</a></td>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                                <td>{{ $user->created_at }}</td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    <h4><i class="fa fa-info-circle"></i> No new customers for today yet...</h4>
                @endif
            </div>
            <div class="col-sm-5">
                <h1><i class="fa fa-bank"></i> Statistics</h1>
                <table class="table">
                    <tr>
                        <td><strong>Today's Revenue</strong></td>
                        <td><strong>${{ number_format($stats['today_revenue'], 2, '.', ',') }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>This Week's Revenue</strong></td>
                        <td><strong>${{ number_format($stats['week_revenue'], 2, '.', ',') }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>This Month's Revenue</strong></td>
                        <td><strong>${{ number_format($stats['month_revenue'], 2, '.', ',') }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>This Year's Revenue</strong></td>
                        <td><strong>${{ number_format($stats['year_revenue'], 2, '.', ',') }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h1><i class="fa fa-gear"></i> Operations</h1>
                <a class="btn btn-info" href="/admin/reports/email-subscribers/newsletter" download="newsletter_subs.csv">Download
                    Newsletter Subscriber Email List</a>
                <a class="btn btn-info" href="/admin/reports/email-subscribers/sales-orders" download="order_email_subs.csv">Download
                    Email List From Orders</a>
                <a class="btn btn-info" href="/admin/reports/email-subscribers/users" download="user_email_subs.csv">Download
                    Email List From Users</a>
            </div>
        </div>
    </div>
@endsection