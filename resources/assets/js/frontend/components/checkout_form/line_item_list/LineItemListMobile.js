/**
 * Class definition of LineItemList
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import Util from '../../../../core/Util';

const numeral = require('numeral');

export default class LineItemListMobile extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div className="product-listing-mobile">
                {(() => {let idx = -1; return this.props.list_items.map(list_item => {
                    idx++;

                    let quantity_limit = 10;
                    if(this.props.quantity_limits && typeof this.props.quantity_limits[list_item.id] !== 'undefined')
                        quantity_limit = this.props.quantity_limits[list_item.id];

                    return(
                    <div className={'single-listing ' + ((this.props.out_of_stock_lines.indexOf(list_item.id) > -1) ? 'insufficient-stock' : '')} key={list_item.id}>
                        <div>
                            <a className="delete-link" onClick={this.props.removeLineItem} data-idx={idx} href="">
                                <i data-idx={idx} className="fa fa-times-circle"/>
                            </a>
                        </div>
                        <a className="image-container" href={list_item.item_url} style={{maxWidth: 70, maxHeight: 70, fontSize: 13, fontWeight: 'bold'}}>
                            <img style={{maxWidth: 70, maxHeight: 70, displayType: 'inline-block'}} src={list_item.image_url}/></a>

                        <h4 className="product-name"><a href={list_item.item_url}>{list_item.name}</a></h4>
                        <div className="details" dangerouslySetInnerHTML={{__html: list_item.details_for_checkout}}/>
                        <div className="unit-price">Unit Price: {numeral(list_item.unit_price).format('$0,0.00')}</div>
                        <div className="quantity">
                            Qty: <select data-idx={idx} onChange={this.props.updateLineQuantity} value={list_item.quantity} className="quantity form-control">
                                {(() => { return Util.fillArray(quantity_limit).map(i => { return(
                                    <option key={i+1} value={i+1}>{i+1}</option>
                                )}); })()}
                            </select>
                        </div>
                        <div className="total">Total: {numeral(list_item.sub_total).format('$0,0.00')}</div>
                    </div>
                )});
                })()}

            </div>
        );
    }
}