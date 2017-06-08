import React from 'react'
import RecordSelector from '../../../components/record_selector/RecordSelector';

export default class LineItem extends React.Component
{
    handleRecordFieldUpdate(search_result, name, record_type)
    {
        // Add record field data to line item data
        let line_item_data = Object.assign({}, this.props.data);

        if(record_type == 'Item')
        {
            // Get info to update item display and prices
            $.get('/admin/record/get-single-record/Item?id=' + search_result.id)
                .always((item_info) => {
                    if(item_info)
                    {
                        line_item_data.item = line_item_data.item || {};
                        line_item_data.item.product = line_item_data.item.product || {};
                        line_item_data.image_url = item_info.image_url;
                        line_item_data.unit_price = item_info.store_price;
                        line_item_data.status = item_info.stock_status;
                        line_item_data.item.product.id = item_info.product_id;
                        line_item_data.item.id = search_result.id;
                        line_item_data.item_id = search_result.id;
                        line_item_data.item.details = item_info.details;

                        this.props.updateHandler(line_item_data, this.props.idx);
                    }
                });

        }
        else if(record_type == 'ShippingMethod')
        {
            line_item_data.shipping_method_id = search_result.id;
        }
        else if(record_type == 'StockLocation')
        {
            line_item_data.ship_from_location_id = search_result.id;
        }

        this.props.updateHandler(line_item_data, this.props.idx);
    }

    handleUpdateLineAttribute(e)
    {
        let line_item_data = Object.assign({}, this.props.data);
        let value = e.target.value;
        let name = e.target.name;
        line_item_data[name] = value;

        this.props.updateHandler(line_item_data, this.props.idx);
    }

    handleDelete(e)
    {
        e.preventDefault();
        this.props.deleteHandler(this.props.idx);
    }

    displayAttributes(item)
    {
        let details = (item.details) ? item.details.map(detail => {
            return(
                <div key={detail.key} className="attribute">
                    <strong>{detail.key}:</strong> {detail.value}
                </div>
            );
        }) : null;

        return(
            <div>
                <div className="attribute">
                    <strong>Product ID:</strong> {item.product_id}
                </div>
                {details}
            </div>
        );
    }

    render()
    {
        let data = this.props.data;

        return(
            <div className="row list-item">
                <a onClick={this.handleDelete.bind(this)} className="delete-button" href=""><i className="fa fa-times-circle" /></a>
                <div className="col-sm-9">
                    <div className="row">
                        {data.item ? <div className="col-xs-2">
                            <a target="_blank" className="product-photo" href={'/admin/product/' + (data.item.product_id || data.item.product_id)}><img src={(data.image_url) ? data.image_url : ''} /></a>
                        </div> : null}
                        <div className="col-xs-5">
                            <div className="form-group">
                                <select name="status" onChange={this.handleUpdateLineAttribute.bind(this)} className="form-control" value={data.status}>
                                    <option value="in_stock">In Stock</option>
                                    <option value="out_of_stock">Out Of Stock</option>
                                    <option value="canceled">Canceled</option>
                                </select>
                            </div>
                            <div className="form-group">
                                <label>Quantity</label>
                                <input onChange={this.handleUpdateLineAttribute.bind(this)} name="quantity" value={data.quantity} placeholder="Quantity" type="text" className="form-control" />
                            </div>
                            <div style={{marginTop: '10px'}} className="form-group">
                                <label>Unit Price</label>
                                <input onChange={this.handleUpdateLineAttribute.bind(this)} name="unit_price" value={data.unit_price} placeholder="Unit Price" type="text" className="form-control" />
                            </div>
                            <div className="form-group">
                                <label>Tax</label>
                                <input onChange={this.handleUpdateLineAttribute.bind(this)} name="tax" placeholder="Tax" value={data.tax} type="text" className="form-control" />
                            </div>
                            <div className="form-group">
                                <label>Discount Amount</label>
                                <input onChange={this.handleUpdateLineAttribute.bind(this)} name="discount_amount" placeholder="Discount Amount" value={data.discount_amount} type="text" className="form-control" />
                            </div>
                            <div className="form-group">
                                <label>Shipping Charge</label>
                                <input onChange={this.handleUpdateLineAttribute.bind(this)} name="shipping_charge" placeholder="Shipping Cost" value={data.shipping_charge} type="text" className="form-control" />
                            </div>
                        </div>
                        <div className="col-xs-5">
                            <RecordSelector initial_value={data.item ? {id: data.item.id, label: data.item.product_name} : null} updateHandler={this.handleRecordFieldUpdate.bind(this)} label="Item" name={'item_' + this.props.idx} record_type="Item"/>
                            <RecordSelector initial_value={data.shipping_method ? {id: data.shipping_method.id, label: data.shipping_method.name} : null} updateHandler={this.handleRecordFieldUpdate.bind(this)} label="Shipping Method" name={'shipping_method_' + this.props.idx} record_type="ShippingMethod"/>
                            <RecordSelector initial_value={data.ship_from_location ? {id: data.ship_from_location.id, label: data.ship_from_location.name} : null} updateHandler={this.handleRecordFieldUpdate.bind(this)} label="Stock Location" name={'stock_location_' + this.props.idx} record_type="StockLocation"/>
                        </div>
                    </div>
                </div>
                <div className="col-sm-3 list-item-info">
                    <p>Sub-Total: $<span data-id={ data.id || data.temp_id } className="sub_total" />{data.sub_total || '0.00'}</p>
                    <p>Total: $<span data-id={ data.id || data.temp_id } className="total_price" />{data.total_price || '0.00'}</p>
                    <h5>Item Attributes</h5>
                    {(data.item && data.item.product_id) && <div className="attribute-section">
                        {this.displayAttributes(data.item)}
                    </div>}
                    {(data.item && data.item.product_id) &&
                        <a style={{marginTop: 10}} className="btn btn-info" target="_blank" href={`/admin/product/${data.item.product_id}`}><i className="fa fa-eye"/> View Product</a>}
                </div>
            </div>
        );
    }
}