/**
 * Class definition of DiscountCodeForm
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';

export default class DiscountCodeForm extends React.Component {
    constructor(props) {
        super(props);
    }

    handleDiscountAdd(e)
    {
        this.props.addDiscountHandler();
    }

    handleUpdateDiscountCode(e)
    {
        e.preventDefault();
        this.props.discountCodeUpdateHandler(e.target.value);
    }

    render() {
        return(
            <div className="discount-code-form">
                <h5><i className="fa fa-shopping-cart"/> Have A Discount Code? Enter It Below!</h5>
                <div className="form-group">
                    <div className="form-group">
                        <input value={this.props.discountCode} onChange={this.handleUpdateDiscountCode.bind(this)} type="text" placeholder="DISCOUNT CODE" className="form-control"/>
                    </div>
                    <button onClick={this.handleDiscountAdd.bind(this)} className="btn btn-success"><i className="fa fa-check"/> Add Discount</button>
                </div>
            </div>
        );
    }
}