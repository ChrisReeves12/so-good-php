import React from 'react';
import ReactDOM from 'react-dom';
const numeral = require('numeral');

export default class HeaderCartDisplay extends React.Component
{
    componentWillMount()
    {
        this.setState(window.sogood.reactjs.header_shopping_cart_data);
        $(document).on('cart-update', () => { this._updateCart(); });
    }

    _updateCart()
    {
        $.get('/shopping-cart/ajax/update')
            .then(res => {
                if(res)
                {
                    this.setState({
                        item_count: res.item_count,
                        sub_total: numeral(res.sub_total).format('0,0.00')
                    });
                }
            });
    }

    render()
    {
        return(
            <div className="shopping-cart">
                <a href="/checkout">
                    <i className="fa fa-shopping-cart"/> {this.state.item_count || 0} | ${this.state.sub_total || '0.00'}<span className="hidden-md-down"> Checkout</span>
                </a>
            </div>
        );
    }

    static initialize()
    {
        let elements = document.getElementsByClassName('shopping_cart_display');
        if(elements.length > 0)
        {
            for(let x = 0; x < elements.length; x++)
            {
                let element = elements[x];

                if(element)
                    ReactDOM.render(<HeaderCartDisplay/>, element);
            }
        }
    }
}