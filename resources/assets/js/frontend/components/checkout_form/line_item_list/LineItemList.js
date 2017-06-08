/**
 * Class definition of LineItemList
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import Util from '../../../../core/Util';

const numeral = require('numeral');

export default class LineItemList extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <table className="table product-listing">
                <tbody>
                <tr>
                    <th/>
                    <th>Product</th>
                    <th>Details</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
                {(() => {let idx = -1; return this.props.list_items.map(list_item => {
                    idx++;
                    let quantity_limit = 10;
                    if(this.props.quantity_limits && typeof this.props.quantity_limits[list_item.id] !== 'undefined')
                        quantity_limit = this.props.quantity_limits[list_item.id];

                    return(
                    <tr className={(this.props.out_of_stock_lines.indexOf(list_item.id) > -1) ? 'insufficient-stock' : ''} key={list_item.id}>
                        <td>
                            <button style={{fontSize: 13, padding: '6px 10px'}} onClick={this.props.removeLineItem} data-idx={idx}
                                    className="btn btn-danger remove-cart-item" href=""><i className="fa fa-times-circle"/> Remove</button>
                        </td>
                        <td><a href={list_item.item_url} style={{maxWidth: 45, fontSize: 13, fontWeight: 'bold'}}>
                            <img style={{maxWidth: 40, maxHeight: 40, displayType: 'inline-block'}} src={list_item.image_url}/> {list_item.name}</a></td>
                        <td className="details" dangerouslySetInnerHTML={{__html: list_item.details_for_checkout}}/>
                        <td>{numeral(list_item.unit_price).format('$0,0.00')}</td>
                        <td>
                            <select data-idx={idx} onChange={this.props.updateLineQuantity} value={list_item.quantity} className="quantity">
                                {(() => { return Util.fillArray(quantity_limit).map(i => { return(
                                    <option key={i+1} value={i+1}>{i+1}</option>
                                )}); })()}
                            </select>
                        </td>
                        <td>{numeral(list_item.sub_total).format('$0,0.00')}</td>
                    </tr>
                )});
                })()}
                </tbody>
            </table>
        );
    }
}