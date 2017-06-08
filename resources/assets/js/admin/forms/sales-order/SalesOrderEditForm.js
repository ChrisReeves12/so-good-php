/**
 * Form that is used to create and edit sales orders
 * @author Christopher Reeves <ChrisReeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';

import Util from '../../../core/Util';
import Popup from '../../../core/Popup';
import random_string from 'randomstring';
import Input from '../../forms/Input';
import LineItem from './line-item/LineItem';
import RecordSelector from '../../components/record_selector/RecordSelector';
import KeyValBox from '../../components/key_val_box/KeyValBox';

export default class SalesOrderEditForm extends React.Component
{
    constructor()
    {
        super();
        this.lines_for_deletion = [];
        this.shipping_carrier_options = [
            {key: 'USPS', value: 'U. S. Post Office'},
            {key: 'UPS', value: 'UPS'},
            {key: 'Fedex', value: 'FedEx'}
        ];
    }

    componentWillMount()
    {
        let initial_data = {};

        if(this.props.sales_order_data.id)
        {
            initial_data = this.props;
            initial_data.sales_order_data.parent_transaction.transaction_line_items.map(tli => tli.saved = true);
        }
        else
        {
            initial_data = {
                sales_order_data: {
                    tracking_numbers: [],
                    parent_transaction: {
                        billing_address: {},
                        shipping_address: {},
                        transaction_line_items: []
                    }
                }
            }
        }

        this.setState(initial_data);
    }

    updateLineItem(line_item_data, idx)
    {
        // Update line data in state
        let sales_order_data = Object.assign({}, this.state.sales_order_data);
        sales_order_data.parent_transaction.transaction_line_items[idx] = line_item_data;
        this.setState({sales_order_data});
    }

    saveOrder(e)
    {
        e.preventDefault();
        let sales_order_data = Object.assign({}, this.state.sales_order_data);
        sales_order_data.order_source = 'admin';

        // Get general information
        let address_info = {};
        let address_inputs = $('#addresses').find(':input');

        // Get address information
        for(let input of address_inputs)
        {
            address_info[input.name] = input.value;
        }

        sales_order_data
            .parent_transaction
            .billing_address = {
                company: address_info['billing_company'],
                city: address_info['billing_city'],
                first_name: address_info['billing_first_name'],
                last_name: address_info['billing_last_name'],
                line_1: address_info['billing_line_1'],
                line_2: address_info['billing_line_2'],
                state: address_info['billing_state'],
                zip: address_info['billing_zipcode']
            };

        sales_order_data
            .parent_transaction
            .shipping_address = {
            company: address_info['shipping_company'],
            city: address_info['shipping_city'],
            first_name: address_info['shipping_first_name'],
            last_name: address_info['shipping_last_name'],
            line_1: address_info['shipping_line_1'],
            line_2: address_info['shipping_line_2'],
            state: address_info['shipping_state'],
            zip: address_info['shipping_zipcode']
        };

        // For new line items, remove their ids
        sales_order_data.parent_transaction.transaction_line_items = sales_order_data.parent_transaction
            .transaction_line_items
            .map((tli) => {
                if(!tli.saved)
                    tli.id = null;
                return tli;
            });

        sales_order_data.payment_method = $('#payment_info').find('[name="payment_method"]').val();
        sales_order_data.payment_info = $('#payment_info').find('[name="payment_info"]').val();
        sales_order_data.payment_fees = $('#payment_info').find('[name="payment_fees"]').val();
        sales_order_data.parent_transaction.email = $('#customer').find('[name="email"]').val();
        sales_order_data.parent_transaction.first_name = $('#customer').find('[name="first_name"]').val();
        sales_order_data.parent_transaction.last_name = $('#customer').find('[name="last_name"]').val();
        sales_order_data.parent_transaction.phone_number = $('#customer').find('[name="phone_number"]').val();
        sales_order_data.memo = $('#customer').find('[name="memo"]').val();
        sales_order_data.status = $('select[name="order_status"]').val();
        sales_order_data.tracking_numbers_data = JSON.parse($('input[name="tracking_numbers"]').val());

        this.setState({errors: null});

        // Send data to server
        let can_save = true;
        if(sales_order_data.status == 'shipped')
        {
            if(!Array.isArray(sales_order_data.tracking_numbers_data) || sales_order_data.tracking_numbers_data.length == 0)
            {
                can_save = confirm("You have no tracking numbers on this order.\nStill save as Shipped?");
            }
        }

        if(can_save)
        {
            $.ajax({
                url: '/admin/record/SalesOrder/' + ((sales_order_data.id) ? sales_order_data.id : ''),
                method: (sales_order_data.id) ? 'PUT' : 'POST',
                dataType: 'json',
                data: {data: sales_order_data, _token: Util.get_auth_token()},
                timeout: 4000,
                complete: (res) => {
                    if(res.status == 200)
                    {
                        if(res.responseJSON.errors)
                        {
                            this.setState({errors: res.responseJSON.errors});
                        }
                        else if(res.responseJSON.system_error)
                        {
                            let popup = new Popup();
                            popup.show(res.responseJSON.system_error);
                        }
                        else
                        {
                            window.location = '/admin/sales-order/' + res.responseJSON.id;
                        }
                    }
                    else if(res.status == 0)
                    {
                        // Todo; handle timeout
                    }
                }
            });
        }
    }

    closeErrors()
    {
        this.setState({errors: null});
    }

    displayErrors()
    {
        let formatted_errors = [];
        for(let error of this.state.errors)
        {
            let field_name = Object.getOwnPropertyNames(error)[0];
            formatted_errors[field_name] = formatted_errors[field_name] || [];
            formatted_errors[field_name].push(error[field_name][0]);
        }

        return(
            <div className="error-messages">
                <div onClick={this.closeErrors.bind(this)} className="close-button" href=""><i className="fa fa-times"/></div>
                {(() => {
                    let idx = -1;
                    return(this.state.errors.map((err) => {
                        let field_name = Object.getOwnPropertyNames(err)[0];
                        let errors = err[field_name];
                        return(
                            <div key={idx} className="error">
                                <h4>{field_name}</h4>
                                <ul>
                                {(() => {
                                    let li_idx = -1;
                                    return errors.map(e => {
                                        return(<li key={li_idx}>{e}</li>);
                                    });
                                })()}
                                </ul>
                            </div>
                        );
                    }));
                })()}
            </div>
        );
    }

    updateUser(search_result)
    {
        let sales_order_data = Object.assign({}, this.state.sales_order_data);
        sales_order_data.parent_transaction.entity_id = search_result.id;
        this.setState({sales_order_data});
    }

    getDiscountCodes()
    {
        let ret_val = '';
        let sales_order_data = this.state.sales_order_data;

        if(Array.isArray(sales_order_data.discount_codes) && sales_order_data.discount_codes.length > 0)
        {
            ret_val = sales_order_data.discount_codes.map(discount => <li className="list-group-item">{discount}</li>);
        }

        return ret_val;
    }

    addLineItem(e)
    {
        e.preventDefault();
        let sales_order_data = Object.assign({}, this.state.sales_order_data);

        sales_order_data.parent_transaction.transaction_line_items.push({
            id: random_string.generate(100),
            status: 'in_stock',
            shipping_charge: '0.00',
            tax: '0.00',
            discount_amount: '0.00',
            quantity: 1,
            saved: false
        });

        this.setState({sales_order_data});
    }

    removeLineItem(idx)
    {
        let sales_order_data = Object.assign({}, this.state.sales_order_data);

        if(sales_order_data.parent_transaction.transaction_line_items[idx].saved)
            this.lines_for_deletion.push(sales_order_data.parent_transaction.transaction_line_items[idx].id);

        sales_order_data.lines_for_deletion = this.lines_for_deletion;

        sales_order_data.parent_transaction.transaction_line_items.splice(idx, 1);
        this.setState({sales_order_data});
    }

    doDelete(e)
    {
        e.preventDefault();
        if(confirm('Delete sales order?'))
        {
            $.ajax({
                url: '/admin/record/SalesOrder/' + this.state.sales_order_data.id,
                method: 'DELETE',
                data: {_token: Util.get_auth_token()},
                timeout: 3000,
                dataType: 'json',
                complete: (res) => {
                    if(res.status == 200)
                    {
                        window.location = '/admin/list/salesOrder';
                    }
                    else if(res. status == 0)
                    {
                        // Todo: handle timeout
                    }
                }
            });
        }
    }

    printInvoice(e)
    {
        e.preventDefault();
        if(!this.state.sales_order_data.id)
        {
            let popup = new Popup();
            popup.show('Please create and save the sales order and then print the invoice.');
        }

        window.open('/admin/sales-order/invoice/' + this.state.sales_order_data.id);
    }

    render()
    {
        let parent_trans = this.state.sales_order_data.parent_transaction;
        parent_trans.shipping_address = parent_trans.shipping_address || {};
        let payment_fees = this.state.sales_order_data.payment_fees;
        let idx = -1;
        let list_items = parent_trans.transaction_line_items.map(tli => {
            idx++;
            return(<LineItem data={tli} deleteHandler={this.removeLineItem.bind(this)} updateHandler={this.updateLineItem.bind(this)} idx={idx} key={tli.id}/>);
        });
        let tracking_numbers = [];
        if(Array.isArray(this.state.sales_order_data.tracking_numbers))
            tracking_numbers = this.state.sales_order_data.tracking_numbers;
        else if(this.state.sales_order_data.tracking_numbers !== '')
            tracking_numbers = JSON.parse(this.state.sales_order_data.tracking_numbers);

        return(
            <div>
                <div style={{marginBottom: '32px'}}>
                    <a href="/admin/sales-order" className="btn btn-secondary"><i className="fa fa-pencil-square" /> Create New</a>
                    <a href="/admin/list/salesOrder" className="btn btn-secondary"><i className="fa fa-list" /> List All Sales Orders</a>
                </div>
                {this.state.errors ? this.displayErrors() : null}
                <form method="post">
                    <div className="row">
                        <div className="col-sm-4">
                            <div className="order-totals-section">
                                <p>Order ID: {this.state.sales_order_data.id || 'N/A'}</p>
                                <p>DB Transaction ID: {this.state.sales_order_data.transaction_id}</p>
                                <p>Marketing Source: {this.state.sales_order_data.marketing_channel}</p>
                                <p>Sub-Total: ${parent_trans.sub_total ? parent_trans.sub_total : '0.00'}</p>
                                <p>Shipping: ${parent_trans.shipping_total ? parent_trans.shipping_total : '0.00'}</p>
                                <p>Discount: ${parent_trans.discount_amount ? parent_trans.discount_amount : '0.00'}</p>
                                <p>Tax: ${parent_trans.tax ? parent_trans.tax : '0.00'}</p>
                                <p>Gift Card: ${parent_trans.gift_card_amount ? parent_trans.gift_card_amount : '0.00'}</p>
                                <p>Payment Fees: ${payment_fees ? payment_fees : '0.00'}</p>
                                <p className="grand-total">Grand Total: ${parent_trans.total ? parent_trans.total : '0.00'}</p>
                                <p style={{marginTop: 10}}>Date Ordered: {this.state.sales_order_data.order_time}</p>
                            </div>
                        </div>
                        <div className="row">
                            <div className="col-sm-4">
                                <div className="form-group">
                                    <Input type="select"
                                           value={this.state.sales_order_data.status}
                                           name="order_status"
                                           is_required="true"
                                           label={'Order Status'}
                                           options={[{id: 'pending', label: 'Pending'}, {id: 'processing', label: 'Processing'}, {id: 'shipped', label: 'Shipped'}, {id: 'canceled', label: 'Canceled'}]}/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button onClick={this.addLineItem.bind(this)} className="btn btn-secondary"><i className="fa fa-plus-circle"/> Add Line Item</button>
                    {/*<button className="btn btn-info"><i className="fa fa-truck" /> Calculate Shipping</button>*/}
                    <button onClick={this.saveOrder.bind(this)} className="btn btn-success"><i className="fa fa-save" /> Save Sales Order</button>

                    {this.state.sales_order_data.id &&
                        <button onClick={this.printInvoice.bind(this)} className="btn btn-info"><i className="fa fa-print" /> Print Invoice</button>}

                    <button onClick={this.doDelete.bind(this)} className="btn btn-danger"><i className="fa fa-times"/> Delete Sales Order</button>
                    <div style={{marginTop: '20px'}} className="row">
                        <div className="col-xs-12">
                            <ul className="nav nav-pills">
                                <li className="nav-item"><a className="nav-link active" data-toggle="tab" href="#list_items"><i className="fa fa-list" /> List Items</a></li>
                                <li className="nav-item"><a className="nav-link" data-toggle="tab" href="#addresses"><i className="fa fa-map-marker" /> Addresses</a></li>
                                <li className="nav-item"><a className="nav-link" data-toggle="tab" href="#customer"><i className="fa fa-user" /> Customer</a></li>
                                <li className="nav-item"><a className="nav-link" data-toggle="tab" href="#fulfillment"><i className="fa fa-truck" /> Fulfillment/Shipping</a></li>
                                <li className="nav-item"><a className="nav-link" data-toggle="tab" href="#payment_info"><i className="fa fa-credit-card" /> Payment Information</a></li>
                            </ul>
                            <div className="tab-content">
                                <div id="list_items" className="tab-pane fade in active">{list_items}</div>
                                <div id="addresses" className="tab-pane fade">
                                    <div className="row">
                                        <div className="col-sm-6">
                                            <h4>Billing Address</h4>
                                            <Input type="text" name="billing_company" value={parent_trans.billing_address.company} label="Company"/>
                                            <Input type="text" name="billing_first_name" value={parent_trans.billing_address.first_name} label="First Name"/>
                                            <Input type="text" name="billing_last_name" value={parent_trans.billing_address.last_name} label="Last Name"/>
                                            <Input type="text" name="billing_line_1" value={parent_trans.billing_address.line_1} label="Address Line 1"/>
                                            <Input type="text" name="billing_line_2" value={parent_trans.billing_address.line_2} label="Address Line 2"/>
                                            <Input type="text" name="billing_city" value={parent_trans.billing_address.city} label="City"/>
                                            <Input type="text" name="billing_state" value={parent_trans.billing_address.state} label="State"/>
                                            <Input type="text" name="billing_zipcode" value={parent_trans.billing_address.zip} label="Zip Code"/>
                                            <Input type="text" name="billing_country" value={parent_trans.billing_address.country} label="Country"/>
                                        </div>
                                        <div className="col-sm-6">
                                            <h4>Shipping Address</h4>
                                            <p>Leave blank if same as billing address.</p>
                                            <Input type="text" name="shipping_company" value={parent_trans.shipping_address.company} label="Company"/>
                                            <Input type="text" name="shipping_first_name" value={parent_trans.shipping_address.first_name} label="First Name"/>
                                            <Input type="text" name="shipping_last_name" value={parent_trans.shipping_address.last_name} label="Last Name"/>
                                            <Input type="text" name="shipping_line_1" value={parent_trans.shipping_address.line_1} label="Address Line 1"/>
                                            <Input type="text" name="shipping_line_2" value={parent_trans.shipping_address.line_2} label="Address Line 2"/>
                                            <Input type="text" name="shipping_city" value={parent_trans.shipping_address.city} label="City"/>
                                            <Input type="text" name="shipping_state" value={parent_trans.shipping_address.state} label="State"/>
                                            <Input type="text" name="shipping_zipcode" value={parent_trans.shipping_address.zip} label="Zip Code"/>
                                            <Input type="text" name="shipping_country" value={parent_trans.shipping_address.country} label="Country"/>
                                        </div>
                                    </div>
                                </div>
                                <div id="customer" className="tab-pane fade">
                                    <div className="col-sm-6">
                                        <p>If a user record is not provided, then an email address is required.</p>
                                        <Input type="text" name="first_name" label="First Name" value={this.state.sales_order_data.parent_transaction.first_name}/>
                                        <Input type="text" name="last_name" label="Last Name" value={this.state.sales_order_data.parent_transaction.last_name}/>
                                        <Input type="text" name="email" label="Customer Email" value={this.state.sales_order_data.parent_transaction.email}/>
                                        <Input type="text" name="phone_number" label="Phone Number" value={this.state.sales_order_data.parent_transaction.phone_number}/>
                                        <RecordSelector
                                            updateHandler={this.updateUser.bind(this)}
                                            initial_value={this.state.sales_order_data.parent_transaction.user ?
                                            {id: this.state.sales_order_data.parent_transaction.user.id,
                                            label: this.state.sales_order_data.parent_transaction.user.first_name + ' ' + this.state.sales_order_data.parent_transaction.user.last_name} : null}
                                            label="User" name="user" record_type="Entity"/>
                                    </div>
                                    <div className="col-sm-6">
                                        <Input type="textarea" name="memo" label="Memo" value={this.state.sales_order_data.memo}/>
                                    </div>
                                </div>
                                <div id="payment_info" className="tab-pane fade">
                                    <div className="col-sm-6">
                                        <h4>Payment Information</h4>
                                        <Input type="select" name="payment_method" value={this.state.sales_order_data.payment_method} options={[{id: 'credit_card', label: 'Credit Card'}, {id: 'paypal', label: 'PayPal'}]}/>
                                        <Input type="text" name="payment_fees" label="Payment Fees" value={this.state.sales_order_data.payment_fees}/>
                                        <Input type="text" name="auth_code" label="Auth Code" value={this.state.sales_order_data.auth_code}/>
                                        <Input type="textarea" name="payment_info" label="Payment Info" value={this.state.sales_order_data.payment_info}/>
                                        <label>Gift Card</label>
                                        <div>
                                            {this.state.sales_order_data.parent_transaction.gift_card_number}
                                        </div>
                                        <label>Discount Codes</label>
                                        <div className="discount-codes">
                                            <ul className="list-group">
                                                {this.getDiscountCodes()}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div id="fulfillment" className="tab-pane fade">
                                    <h4>Fulfillment</h4>
                                     <div className="row">
                                         <div className="col-sm-6">
                                            <h5>Tracking Numbers</h5>
                                            <KeyValBox key_choices={this.shipping_carrier_options} name="tracking_numbers"
                                                       values={(this.state.sales_order_data.tracking_numbers && this.state.sales_order_data.tracking_numbers != '') ?
                                                           tracking_numbers : []}/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('sales_order_form');
        if(element)
            ReactDOM.render(<SalesOrderEditForm sales_order_data={JSON.parse(element.dataset.orderData)}/>, element);
    }
}