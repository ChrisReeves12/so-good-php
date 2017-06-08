import React from 'react';
import Util from '../../../../../../app/assets/javascript/core/Util';
const numeral = require('numeral');

export default class ModalAddToCart extends React.Component
{
    constructor()
    {
        super();
        this.state = {};
    }

    componentWillMount()
    {
        // Launches when someone adds something to the cart
        $(document).on('show_add_to_cart_modal', (e, data) => {
            $('#modal_add_to_cart').modal();

            // Find the currently selected item
            if(data.product.default_item_id === data.selected_item_id)
            {
                // Default item is selected, generally means this product is a single item product
                data.selected_item = {
                    id: data.product.default_item_id,
                    sku: data.default_sku,
                    store_price: parseFloat(data.price_display.replace(/\$/, '')),
                    list_price: (data.list_price_display) ? parseFloat(data.list_price_display.replace(/\$/, '')) : '',
                    image: {},
                    details: []
                };
            }
            else
            {
                data.selected_item = data.product.items.find(itm => itm.id === data.selected_item_id);
            }

            this.setState(data);
        });
    }

    render()
    {
        if(this.state.product)
        {
            // Find the correct image to display
            let display_image = '';
            if(!Util.objectIsEmpty(this.state.selected_item.image) || !Util.objectIsEmpty(this.state.original_main_image))
            {
                display_image = !Util.objectIsEmpty(this.state.selected_item.image) ?
                    this.state.selected_item.image.url : this.state.original_main_image.url;
            }
            else if(Array.isArray(this.state.product.images) && this.state.product.images.length > 0)
            {
                display_image = this.state.product.images[0].url;
            }

            // List details of selected item if applicable
            let display_details = null;
            if(this.state.selected_item.details.length > 0)
            {
                display_details = this.state.selected_item.details.map(d => {
                    return(
                        <li key={d.key}>{d.key}: {d.value}</li>
                    );
                });
            }

            return (
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title" id="modal_add_to_cart_title">Product Added To Your Shopping Cart!</h5>
                        </div>
                        <div className="modal-body">
                            <div className="row">
                                <div className="col-md-6">
                                    <img className="img-fluid" src={display_image}/>
                                </div>
                                <div className="col-md-6">
                                    <h4 className="product-name">{this.state.product.name}</h4>
                                    <p className="product-sku">Sku: {this.state.selected_item.sku}</p>
                                    {this.state.selected_item.details.length > 0 ? <div className="product-details-section">
                                        <ul>{display_details}</ul>
                                    </div> : null}
                                    <p className="price">Unit Price: ${this.state.selected_item.store_price}</p>
                                    <p className="quantity">Quantity: {this.state.quantity}</p>
                                    <div className="total-section">
                                        <p className="label">Total</p>
                                        <p className="total">{numeral(this.state.selected_item.store_price * this.state.quantity).format('$0,0.00')}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="modal-footer">
                            <button type="button" className="btn btn-secondary" style={{marginRight: 4}} data-dismiss="modal">Continue Shopping</button>
                            <a className="btn btn-primary" href="/checkout"><i className="fa fa-shopping-cart"/> Go To Checkout</a>
                        </div>
                    </div>
                </div>
            );
        }
        else
        {
            return null;
        }
    }
}