/**
 * Class definition of ShippingMethods
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import Util from '../../../core/Util';
const numeral = require('numeral');

export default class ShippingMethods extends React.Component {
    constructor(props) {
        super(props);
    }

    handleChange(e)
    {
        this.props.updateHandler(e.target.value);
    }

    displayShippingPrice(price)
    {
        let price_display;
        if(price > 0)
            price_display = numeral(price).format('$0,0.00');
        else
            price_display = 'Free';

        return price_display;
    }

    render() {
        if(this.props.cart_updating)
        {
            return(
                <div>
                    <h5>Select Shipping Method</h5>
                    <h6><i className="fa fa-hourglass-half"/> Recalculating shipping rates...please wait...</h6>
                </div>
            );
        }

        return (
            <div>
                <h5>Select Shipping Method</h5>
                {this.props.shipping_methods.length > 0 ? <div>
                {(() => { return this.props.shipping_methods.map(shipping_method => { return(
                    <div key={shipping_method.id} className="shipping-method">
                        <div style={{display: 'inline-block', verticalAlign: 'top', marginRight: 5}}>
                        <input
                            onChange={this.handleChange.bind(this)}
                            type="radio"
                            data-price={shipping_method.price}
                            checked={this.props.selected_shipping_method_id && parseInt(this.props.selected_shipping_method_id) === shipping_method.id ? 'checked' : ''}
                            value={shipping_method.id}
                            name="shipping_method"/>
                        </div>
                        <div style={{display: 'inline-block', verticalAlign: 'top', maxWidth: 200}}>
                            <label>{this.displayShippingPrice(shipping_method.price)} - {shipping_method.name}</label>
                        </div>
                    </div>
                ); }); })()}
                </div> : <p style={{color: '#757575'}}>Your shipping will be calculated after your order is placed.</p>}
            </div>
        );
    }
}