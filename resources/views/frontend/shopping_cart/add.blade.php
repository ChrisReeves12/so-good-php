<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modal_add_to_cart_title">Product Added To Your Shopping Cart!</h5>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <img class="img-fluid" src="{{ $transaction_line_item->image_url }}"/>
                </div>
                <div class="col-md-6">
                    <h4 class="product-name">{{ $transaction_line_item->item->product->name }}</h4>
                    <p class="product-sku">Sku: {{ $transaction_line_item->item->sku }}</p>
                    @if(!empty($transaction_line_item->item->details))
                        <div class="product-details-section">
                            <ul>
                                @foreach($transaction_line_item->item->details as $detail)
                                    <li>{{ $detail['key'] }}: {{ $detail['value'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <p class="price">Unit Price: ${{ number_format($transaction_line_item->item->store_price, 2) }}</p>
                    <p class="quantity">Quantity: {{ request()->get('quantity') }}</p>
                    <div class="total-section">
                        <p class="label">Total</p>
                        <p class="total">${{ number_format($transaction_line_item->item->store_price * request()->get('quantity'), 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" style="margin-right: 4px;" data-dismiss="modal">Continue Shopping</button>
            <a class="btn btn-primary" href="/checkout"><i class="fa fa-shopping-cart"/> Go To Checkout</a>
        </div>
    </div>
</div>