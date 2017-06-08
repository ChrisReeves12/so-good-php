/**
 * Class definition of CheckoutForm
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';
import Util from '../../../core/Util';
import Popup from '../../../core/Popup';
import AddressForms from '../address_forms/AddressForms';
import ShippingMethods from './ShippingMethods';
import CreditCardForm from './CreditCardForm';
import UserInfoForm from './UserInfoForm';
import LineItemList from './line_item_list/LineItemList';
import LineItemListMobile from './line_item_list/LineItemListMobile';
import DiscountCodeForm from './DiscountCodeForm';
import GiftCardForm from './GiftCardForm';

const numeral = require('numeral');

export default class CheckoutForm extends React.Component {
    constructor(props) {
        super(props);
        this.popup = new Popup();
        this.gf_input_timer = null;

        let state = window.sogood.reactjs.shopping_cart_data;
        state.cart_updating = false;
        state.gift_card_amount = (state.gift_card_amount <= 0) ? '' : state.gift_card_amount;
        state.bypass_pay_method = false;
        state.discount_code = '';
        state.missing_records = [];
        state.billing_address = state.billing_address || {};
        state.billing_address.company = state.billing_address.company || '';
        state.billing_address.line_1 = state.billing_address.line_1 || '';
        state.billing_address.line_2 = state.billing_address.line_2 || '';
        state.billing_address.state = state.billing_address.state || '';
        state.billing_address.city = state.billing_address.city || '';
        state.billing_address.zip = state.billing_address.zip || '';

        let same_as_billing = (state.shipping_address === null);
        state.shipping_address =  state.shipping_address || {};
        state.out_of_stock_lines = [];
        state.shipping_address.company = state.shipping_address.company || '';
        state.shipping_address.line_1 = state.shipping_address.line_1 || '';
        state.shipping_address.line_2 = state.shipping_address.line_2 || '';
        state.shipping_address.state = state.shipping_address.state || '';
        state.shipping_address.city = state.shipping_address.city || '';
        state.shipping_address.zip = state.shipping_address.zip || '';
        state.shipping_address.same_as_billing = same_as_billing;
        state.submitting_order = false;
        state.cc_info = {number: '', exp_month: '1', exp_year: '2017', cvc: '', error: false};
        state.shipping_methods = state.shipping_methods || [];
        state.quantity_limits = {};
        state.subscribe_to_newsletter = true;

        state.first_name = state.first_name || '';
        state.last_name = state.last_name || '';
        state.email = state.email || '';
        state.phone_number = state.phone_number || '';

        state.errors = [];

        state.serialized_addresses = JSON.stringify(state.billing_address) + JSON.stringify(state.shipping_address);
        state.serialized_user_info = JSON.stringify([state.first_name, state.last_name, state.email, state.phone_number]);

        // Display gift card deduction
        if((state.gift_card_amount > 0))
        {
            state.gift_card_amount_display = this.getGiftCardAmountDisplay(state.gift_card_amount);
        }

        // Hide payment buttons and bypass checkout if total is zero
        state.bypass_pay_method = (parseFloat(state.grand_total).toFixed(2) <= 0);


        this.state = state;
    }

    getGiftCardAmountDisplay(amount)
    {
        return numeral(-amount).format('$0,0.00');
    }

    removeLineItem(e)
    {
        e.preventDefault();
        let idx = e.target.dataset.idx;
        let list_items = this.state.list_items.slice();

        // Ensures no other AJAX requests are running
        if($.active === 0)
        {
            this.setState({cart_updating: true});

            $.ajax({
                url: '/shopping-cart/ajax/line-item',
                method: 'DELETE',
                dataType: 'json',
                timeout: 5000,
                data: {_token: Util.get_auth_token(), line_id: this.state.list_items[idx].id}
            })
                .then(res => {
                    if(res.system_error)
                    {
                        this.popup.show(res.system_error);
                    }
                    else
                    {
                        // Send to google Analytics
                        if(Util.env() === 'production')
                        {
                            dataLayer.push({
                                'event': 'removeFromCart',
                                'ecommerce': {
                                    'remove': {
                                        'products': [{
                                            'name': list_items[idx].name,
                                            'id': list_items[idx].name.split(" - ")[0],
                                            'price': list_items[idx].unit_price,
                                            'quantity': list_items[idx].quantity
                                        }]
                                    }
                                }
                            });
                        }

                        list_items.splice(idx, 1);
                        this.setState({list_items});

                        // Update cart
                        return this._getCheckoutPageUpdatedData();
                    }
                })
                .then(res => {
                    this._updateCheckoutPageData(res);
                    this.setState({cart_updating: false});
                })
                .catch(res => {
                    if(res.status === 0)
                        this.popup.show('The operation timed out while deleting the line item, please try again.');

                    this.setState({cart_updating: false});
                })
        }
    }

    renderMissingRecords()
    {
        return(
            <ul className="missing-records">
                {(() => {
                    return this.state.missing_records.map(missing_record => {
                        return(<li>{missing_record}</li>)
                    });
                })()}
            </ul>
        );
    }

    updateLineQuantity(e)
    {
        // Update cart
        if($.active === 0)
        {
            let idx = e.target.dataset.idx;
            let state = Object.assign({}, this.state);
            state.cart_updating = true;
            state.list_items[idx].quantity = e.target.value;
            state.out_of_stock_items = [];
            this.setState(state);

            $.ajax({
                url: '/shopping-cart/ajax/line-item/quantity-change',
                method: 'PUT',
                dataType: 'json',
                data: {_token: Util.get_auth_token(), line_id: state.list_items[idx].id, quantity: e.target.value},
                timeout: 5000
            })
                .then(res => {
                    // Update quantity on line item
                    state = Object.assign({}, this.state);
                    state.list_items[idx].sub_total = res.line_sub_total;
                    this.setState(state);

                    // Update the page
                    return this._getCheckoutPageUpdatedData();
                })
                .then(res => {
                    this._updateCheckoutPageData(res);
                    this.setState({cart_updating: false});
                })
                .catch(res => {
                    if(res.status === 0)
                    {
                        this.popup.show('The operation timed out while trying to update the line item quantity, please try again.');
                    }

                    this.setState({cart_updating: false});
                });
        }
    }

    updateBillingAddressField(field)
    {
        let billing_address = Object.assign({}, this.state.billing_address);
        billing_address[field.name] = field.value;
        this.setState({billing_address});
    }

    updateShippingAddressField(field)
    {
        let shipping_address = Object.assign({}, this.state.shipping_address);
        if($(field).is(':checkbox'))
        {
            shipping_address[field.name] = $(field).is(':checked');
            this.setState({errors: []});
        }
        else
        {
            shipping_address[field.name] = field.value;
        }

        this.setState({shipping_address});
    }

    updateShippingMethod(shipping_method_id)
    {
        // Update shipping method on server
        if($.active === 0)
        {
            this.setState({cart_updating: true});

            $.ajax({
                url: '/shopping-cart/ajax/shipping-method/update',
                method: 'PUT',
                timeout: 5000,
                dataType: 'json',
                data: {_token: Util.get_auth_token(), id: shipping_method_id}
            })
                .then(res => {
                    this.setState({selected_shipping_method_id: shipping_method_id});
                    return this._getCheckoutPageUpdatedData();
                })
                .then(res => {
                    this._updateCheckoutPageData(res);
                    this.setState({cart_updating: false});
                })
                .catch(res => {
                    if(res.status === 0)
                        this.popup.show('The operation timed out while selected the shipping method, please try again.');

                    this.setState({cart_updating: false});
                });
        }
    }

    saveAddressChanges(e)
    {
        e.preventDefault();
        if($.active === 0)
        {
            this.setState({cart_updating: true, errors: []});

            $.ajax({
                url: '/shopping-cart/ajax/address/update',
                method: 'PUT',
                dataType: 'json',
                timeout: 10000,
                data: {_token: Util.get_auth_token(), billing_address: this.state.billing_address, shipping_address: this.state.shipping_address}
            })
                .then(res => {
                    // Handle form errors
                    if(res.errors)
                    {
                        this.setState({errors: res.errors});
                        this.setState({cart_updating: false});
                    }
                    else
                    {
                        // Update the serialized address
                        this.setState({serialized_addresses: JSON.stringify(this.state.billing_address) + JSON.stringify(this.state.shipping_address)});
                        return this._getCheckoutPageUpdatedData();
                    }
                })
                .then(res => {
                    this._updateCheckoutPageData(res);
                    this.setState({cart_updating: false});
                })
                .catch(res => {
                    if(res.status === 0)
                        this.popup.show('Operation timed out while saving addresses, please try again.');

                    this.setState({cart_updating: false});
                })
        }
    }

    updateCCField(field)
    {
        this.state.cc_info[field.name] = field.value;
        this.setState(this.state);
    }

    componentDidMount()
    {
        this._renderPayPalButton();
    }

    componentDidUpdate()
    {
        // Rerender PayPal button if needed
        if($('#paypal-button').find('.xcomponent').length === 0)
        {
            if(!this.state.bypass_pay_method)
            {
                this._renderPayPalButton();
            }
        }
    }

    componentWillMount()
    {
        // Get orderable quantities on each item
        if(Array.isArray(this.state.list_items) && this.state.list_items.length > 0)
        {
            $.ajax({
                url: '/shopping-cart/ajax/update-orderable-qty-on-lines',
                method: 'GET',
                dataType: 'json',
                timeout: 7000,
                complete: (res) => {
                    if(res.status === 200)
                    {
                        if(res.responseJSON.system_error)
                        {
                            let popup = new Popup();
                            popup.show(res.responseJSON.system_error);
                        }
                        else
                        {
                            this.setState({quantity_limits: res.responseJSON.quantities});
                        }
                    }
                }
            });
        }
    }

    submitOrder(e)
    {
        let checkout_complete = false;
        let self = this;

        this.setState({submitting_order: true, missing_records: [], out_of_stock_lines: [], errors: [], cc_info: {
            number: this.state.cc_info.number,
            exp_month: this.state.cc_info.exp_month,
            exp_year: this.state.cc_info.exp_year,
            cvc: this.state.cc_info.cvc,
            error: false
        }});

        // Shipping method required
        if(Array.isArray(this.state.shipping_methods) && this.state.shipping_methods.length > 0 && !this.state.selected_shipping_method_id)
        {
            this.popup.show('Please select a shipping method.');
            this.setState({submitting_order: false});
        }
        else
        {
            if(!this.state.cart_updating && $.active === 0)
            {
                let state = Object.assign({}, this.state);
                state.out_of_stock_lines = [];

                // Validate form
                this._validateCheckoutForm()
                    .then(res => {
                        if(!res.errors && !res.missing_records)
                        {
                            // Check inventory
                            return this._checkInventory();
                        }
                        else
                        {
                            throw {error: 'checkout_errors', data: {errors: res.errors, missing_records: res.missing_records}};
                        }
                    })
                    .then(res => {

                        if(!Array.isArray(res.out_of_stock_lines) || res.out_of_stock_lines.length === 0)
                        {
                            // Validate card
                            if(state.grand_total > 0)
                            {
                                return this._validateCard({
                                    number: state.cc_info.number,
                                    exp_month: state.cc_info.exp_month,
                                    exp_year: state.cc_info.exp_year,
                                    cvc: state.cc_info.cvc
                                });
                            }
                            else
                            {
                                this._createOrder(null, this.state.subscribe_to_newsletter);
                                checkout_complete = true;
                            }
                        }
                        else
                        {
                            // Display errors
                            this.popup.show('There are a few items in your cart that cannot be fulfilled due to insufficient stock.<br/>The items are highlighted above.');
                            throw {error: 'out_of_stock', data: res.out_of_stock_lines};
                        }
                    })
                    .then((stripe_response) => {

                        // Send to server
                        if(!checkout_complete)
                        {
                            self._createOrder(stripe_response, this.state.subscribe_to_newsletter);
                            checkout_complete = true;
                        }
                    })
                    .catch(err => {
                        if(err.error === 'out_of_stock')
                        {
                            this.setState({out_of_stock_lines: err.data});
                        }
                        else if(err.error === 'checkout_errors')
                        {
                            this.setState({errors: err.data.errors, missing_records: err.data.missing_records});
                        }
                        else if(err.error === 'invalid_card')
                        {
                            // Show credit error in form
                            state.cc_info.error = err.data.message;
                            this.setState(state);
                        }
                        else if(err.error === 'inventory_check_timeout')
                        {
                            this.popup.show('The operation timed out while checking inventory, please try again.');
                        }

                        this.setState({submitting_order: false});
                    });
            }
        }
    }

    updateUserInfoField(field)
    {
        if(field.type === 'checkbox')
        {
            this.state[field.name] = $(field).is(':checked');
        }
        else
        {
            this.state[field.name] = field.value;
        }

        this.setState(this.state);
    }

    updateUserInfo()
    {
        if($.active === 0)
        {
            $.ajax({
                url: '/shopping-cart/user-info',
                method: 'PUT',
                dataType: 'json',
                timeout: 4000,
                data: {
                    _token: Util.get_auth_token(),
                    first_name: this.state.first_name,
                    last_name: this.state.last_name,
                    email: this.state.email,
                    phone_number: this.state.phone_number
                },
                complete: (res) => {
                    if(res.status === 200)
                    {
                        if(res.responseJSON.errors)
                        {
                            this.setState({errors: res.responseJSON.errors});
                        }
                        else
                        {
                            this.setState({serialized_user_info: JSON.stringify([this.state.first_name, this.state.last_name, this.state.email, this.state.phone_number])});
                        }
                    }
                    else if(res.status === 0)
                    {
                        this.popup.show('The operation timed out while updating user information, please try again.');
                    }
                }
            });
        }
    }

    updateDiscountCode(code)
    {
        this.setState({discount_code: code});
    }

    addDiscountCode()
    {
        this.setState({cart_updating: true});

        $.ajax({
            url: '/shopping-cart/discount-code/add',
            method: 'PUT',
            dataType: 'json',
            data: {_token: Util.get_auth_token(), code: this.state.discount_code}
        })
            .then((res) => {

                if(res.system_error)
                {
                    this.popup.show(res.system_error);
                }
                else
                {
                    this.popup.show('Your discount code has been successfully applied!');

                    // Update cart
                    this.setState({cart_updating: true});
                    return this._getCheckoutPageUpdatedData();
                }

            })
            .then(res => {
                this._updateCheckoutPageData(res);
                this.setState({cart_updating: false});
            })
            .catch(res => {
                if(res.status === 0)
                    this.popup.show('The operation timed out while deleting the line item, please try again.');

                this.setState({cart_updating: false});
            });
    }

    _createOrder(card_data, subscribe_to_newsletter)
    {
        card_data = (typeof card_data === 'undefined') ? {} : card_data;

        // Send to server
        $.ajax({
            url: '/checkout/submit',
            method: 'POST',
            dataType: 'json',
            timeout: 20000,
            data: {_token: Util.get_auth_token(), card_data: card_data, test_order: this.state.test_order, subscribe_to_newsletter},
            complete: (res) => {
                if(res.status === 200)
                {
                    if(!res.responseJSON.card_error && !res.responseJSON.errors)
                    {
                        window.location = `/checkout/complete/receipt/${res.responseJSON.id}`;
                    }
                    else if(res.responseJSON.errors)
                    {
                        this.popup.show(res.responseJSON.errors);
                    }
                    else
                    {
                        this.popup.show(res.responseJSON.card_error);
                    }
                }
                else
                {
                    state.submitting_order = false;
                    this.popup.show('The operation timed out while placing your order. Please contact technical support.');
                }

                this.setState(state);
            }
        });
    }

    _checkInventory()
    {
        return new Promise((fulfill, reject) => {
            $.ajax({
                url: '/shopping-cart/ajax/inventory-check',
                method: 'GET',
                timeout: 4000,
                dataType: 'json',
                complete: (res) => {
                    if(res.status === 200)
                        fulfill(res);
                    else
                        reject({error: 'inventory_check_timeout'});
                }
            });
        });
    }

    _validateCheckoutForm()
    {
        return new Promise((fulfill, reject) => {
            $.ajax({
                url: '/shopping-cart/checkout/validate',
                method: 'POST',
                timeout: 4000,
                data: {_token: Util.get_auth_token(), data: this.state},
                dataType: 'json',
                complete: (res) => {
                    if(res.status === 200)
                        fulfill(res);
                    else
                        reject(res);
                }
            });
        });
    }

    _validateCard(card_info)
    {
        return new Promise((fulfill, reject) => {
            Stripe.card.createToken(card_info, (status, response) => {
                if(response.error)
                {
                    reject({error: 'invalid_card', data: response.error});
                }
                else
                {
                    fulfill(response);
                }
            });
        });
    }
    
    _updateCheckoutPageData(new_data)
    {
        let state = Object.assign({}, this.state);
        state.tax = parseFloat(new_data.tax).toFixed(2);
        state.discount_amount = parseFloat(new_data.discount_amount).toFixed(2);
        state.shipping_methods = new_data.shipping_methods;
        state.shipping_total = parseFloat(new_data.shipping_total).toFixed(2);
        state.sub_total = parseFloat(new_data.sub_total).toFixed(2);
        state.grand_total = parseFloat(new_data.grand_total).toFixed(2);

        // Handle gift card amount display
        if(new_data.gift_card_amount > 0)
        {
            state.gift_card_amount_display = this.getGiftCardAmountDisplay(new_data.gift_card_amount);
        }
        else
        {
            state.gift_card_amount_display = null;
        }

        // Hide payment buttons and bypass checkout if total is zero
        state.bypass_pay_method = (state.grand_total <= 0);

        this.setState(state);
    }

    _getCheckoutPageUpdatedData()
    {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/shopping-cart/ajax/update',
                method: 'GET',
                timeout: 5000,
                dataType: 'json',
                complete: (res) => {
                    if(res.status === 200)
                    {
                        resolve(res);
                    }
                    else
                    {
                        reject(res);
                    }
                }
            });
        });
    }

    _renderPayPalButton()
    {
        let self = this;
        let environment = (this.state.test_order || Util.env() !== 'production') ? 'sandbox' : 'production';

        paypal.Button.render({

            style: {
                size: 'large',
                color: 'gold',
                shape: 'pill',
                label: 'checkout'
            },

            env: environment,

            payment: (resolve, reject) => {

                if(self.state.submitting_order === false && self.state.cart_updating === false)
                {
                    this.setState({missing_records: [], out_of_stock_lines: [], errors: [], cc_info: {
                        number: this.state.cc_info.number,
                        exp_month: this.state.cc_info.exp_month,
                        exp_year: this.state.cc_info.exp_year,
                        cvc: this.state.cc_info.cvc,
                        error: false
                    }});

                    // Shipping method required
                    if(Array.isArray(this.state.shipping_methods) && this.state.shipping_methods.length > 0 && !this.state.selected_shipping_method_id)
                    {
                        this.popup.show('Please select a shipping method...');
                        throw new Error();
                    }
                    else
                    {
                        if(!this.state.cart_updating && $.active === 0)
                        {
                            let state = Object.assign({}, this.state);
                            state.out_of_stock_lines = [];

                            this._validateCheckoutForm()
                                .then(res => {
                                    if(!res.errors && !res.missing_records)
                                    {
                                        // Check inventory
                                        return this._checkInventory();
                                    }
                                    else
                                    {
                                        throw {error: 'checkout_errors', data: {errors: res.errors, missing_records: res.missing_records}};
                                    }
                                })
                                .then(res => {

                                    if(!Array.isArray(res.out_of_stock_lines) || res.out_of_stock_lines.length == 0)
                                    {
                                        let create_payment_url = '/checkout/send-paypal-purchase';

                                        paypal.request.post(create_payment_url, {_token: Util.get_auth_token(), test_order: self.state.test_order})
                                            .then((data) => {
                                                resolve(data.paymentID)
                                            })
                                            .catch((err) => {
                                                reject(err)
                                            })
                                    }
                                    else
                                    {
                                        // Display errors
                                        this.popup.show('There are a few items in your cart that cannot be fulfilled due to insufficient stock.<br/>The items are highlighted above.');
                                        throw {error: 'out_of_stock', data: res.out_of_stock_lines};
                                    }
                                })
                                .catch(err => {
                                    if(err.error === 'out_of_stock')
                                    {
                                        this.setState({out_of_stock_lines: err.data});
                                    }
                                    else if(err.error == 'checkout_errors')
                                    {
                                        this.setState({errors: err.data.errors, missing_records: err.data.missing_records});
                                    }

                                    reject(err);
                                });
                        }
                    }
                }
            },
            onAuthorize: (data) => {
                let execution_url = '/checkout/execute-paypal-purchase';
                paypal.request.post(execution_url, {_token: Util.get_auth_token(), payerID: data.payerID, paymentID: data.paymentID, test_order: self.state.test_order})
                    .then((data) => {
                        if(data.state != 'approved')
                        {
                            this.popup.show('The PayPal purchase was not approved.');
                        }
                        else
                        {
                            // Send order to server
                            self.setState({submitting_order: true});

                            $.ajax({
                                url: '/checkout/submit',
                                method: 'POST',
                                dataType: 'json',
                                timeout: 20000,
                                data: {_token: Util.get_auth_token(), paypal_data: data, test_order: this.state.test_order},

                                complete: (res) => {
                                    if(res.status == 200)
                                    {
                                        window.location = `/checkout/complete/receipt/${res.responseJSON.id}`;
                                    }
                                    else
                                    {
                                        self.popup.show('The operation timed out while placing your order. Please contact technical support.');
                                    }

                                    self.setState({submitting_order: false});
                                }
                            });
                        }
                    })
                    .catch((err) => {
                        self.popup.show(err);
                    });
            }
        }, '#paypal-button');
    }

    doAddGiftCard(e)
    {
        // Check for shipping method first
        if(Array.isArray(this.state.shipping_methods) && this.state.shipping_methods.length > 0 && !this.state.selected_shipping_method_id)
        {
            this.popup.show('Please select a shipping method before adding a gift card.');
        }
        else
        {
            if($.active === 0)
            {
                let gift_card_data = {
                    number: this.state.gift_card_number,
                    amount: this.state.gift_card_amount,
                    _token: Util.get_auth_token()
                };

                // Send data to server
                $.ajax({
                    url: '/checkout/gift-card/update',
                    method: 'PUT',
                    dataType: 'json',
                    data: gift_card_data,
                    timeout: 5000
                })
                    .then((res) => {
                        if(res.system_error)
                        {
                            this.popup.show(res.system_error);
                        }
                        else
                        {
                            if(res.removing_card) {
                                this.popup.show('Your gift card has been removed from the order.');
                            }
                            else
                            {
                                this.popup.show('Your gift card has been applied to your order.');
                            }

                            this.setState({cart_updating: true});
                            return this._getCheckoutPageUpdatedData();
                        }
                    })
                    .then(res => {
                        this._updateCheckoutPageData(res);
                        this.setState({cart_updating: false});
                    })
                    .catch((err) => {
                        if(err.status === 0)
                            this.popup.show('The response timed out, please try again.');

                        this.setState({cart_updating: false});
                    });
            }
        }
    }

    updateGiftCardNumber(e)
    {
        let value = '';
        for(let c = 0; c < e.target.value.length; c++)
        {
            let letter = e.target.value[c];
            if(letter.match(/\d/))
            {
                value += letter;
            }
        }

        if(value.length <= 10)
        {
            this.setState({gift_card_number: value});
        }
    }

    updateGiftCardAmount(e)
    {
        let value = '';
        for(let c = 0; c < e.target.value.length; c++)
        {
            let letter = e.target.value[c];
            if(letter.match(/[\d\.]/))
            {
                value += letter;
            }
        }

        if(!value.match(/\d+\.\d{3,}/))
        {
            this.setState({gift_card_amount: value});
        }
    }

    doAddTotalToGiftCard(e)
    {
        e.preventDefault();
        let total = parseFloat(this.state.grand_total) + parseFloat(this.state.gift_card_amount || 0);
        this.setState({gift_card_amount: numeral(total).format('0.00')});
    }


    render() {

        let address_forms = new AddressForms({
            errors: this.state.errors,
            section_class_name: 'address-form',
            billing_address: this.state.billing_address,
            shipping_address: this.state.shipping_address,
            updateBillingAddress: this.updateBillingAddressField.bind(this),
            updateShippingAddress: this.updateShippingAddressField.bind(this)
        });

        let show_addrs_change_notice = (JSON.stringify(this.state.billing_address) + JSON.stringify(this.state.shipping_address)) !== this.state.serialized_addresses;
        let show_user_info_change_notice = JSON.stringify([this.state.first_name, this.state.last_name, this.state.email, this.state.phone_number]) !== this.state.serialized_user_info;

        // Selectively render the page if the cart is empty
        if(Util.objectIsEmpty(this.state.list_items))
        {
            return(
                <div>
                    <h5><i className="fa fa-info-circle"/> Your shopping cart is currently empty.</h5>
                </div>
            );
        }
        else
        {
            // Render checkout items
            return(
                <div>
                    <div className="row">
                        <div className="col-sm-12">
                            <div className="hidden-md-down">
                                <LineItemList
                                    out_of_stock_lines={this.state.out_of_stock_lines}
                                    removeLineItem={this.removeLineItem.bind(this)}
                                    updateLineQuantity={this.updateLineQuantity.bind(this)}
                                    quantity_limits={this.state.quantity_limits}
                                    list_items={this.state.list_items}/>
                            </div>
                            <div className="hidden-lg-up">
                                <LineItemListMobile
                                    out_of_stock_lines={this.state.out_of_stock_lines}
                                    quantity_limits={this.state.quantity_limits}
                                    removeLineItem={this.removeLineItem.bind(this)}
                                    updateLineQuantity={this.updateLineQuantity.bind(this)}
                                    list_items={this.state.list_items}/>
                            </div>
                        </div>
                    </div>
                    <UserInfoForm submitHandler={this.updateUserInfo.bind(this)}
                                  subscribe_to_newsletter={this.state.subscribe_to_newsletter}
                                  user_info={{first_name: this.state.first_name, last_name: this.state.last_name, email: this.state.email, phone_number: this.state.phone_number}}
                                  errors={this.state.errors} updateFieldHandler={this.updateUserInfoField.bind(this)}
                                  show_save_notice={show_user_info_change_notice}/>
                    <div className="row addresses">
                        <form onSubmit={this.saveAddressChanges.bind(this)}>
                            <div className="col-md-6">
                                <h4>Billing Address</h4>
                                {address_forms.render_billing_section()}
                                <div className="hidden-sm-down">
                                    <button className="btn btn-info" disabled={show_addrs_change_notice ? '' : 'disabled'}><i className="fa fa-save"/> Save Address Changes</button>
                                    {show_addrs_change_notice ? <p className="address-save-notification">Make sure you save the address changes.</p> : null}
                                </div>
                            </div>
                            <div className="col-md-6">
                                <h4>Shipping Address</h4>
                                {address_forms.render_shipping_section()}
                                <div className="hidden-md-up">
                                    <button className="btn btn-info" disabled={show_addrs_change_notice ? '' : 'disabled'}><i className="fa fa-save"/> Save Address Changes</button>
                                    {show_addrs_change_notice ? <p className="address-save-notification">Make sure you save the address changes.</p> : null}
                                </div>
                            </div>
                        </form>
                    </div>
                    <div style={{marginBottom: 15}} className="row">
                        <div className="col-md-6">
                            {(Array.isArray(this.state.shipping_methods) && this.state.shipping_methods.length > 0) &&
                            <ShippingMethods updateHandler={this.updateShippingMethod.bind(this)}
                                             cart_updating={this.state.cart_updating}
                                             selected_shipping_method_id={this.state.selected_shipping_method_id}
                                             shipping_methods={this.state.shipping_methods}/>}
                        </div>
                        <div className="col-md-6">
                            <DiscountCodeForm discountCode={this.state.discount_code}
                                              addDiscountHandler={this.addDiscountCode.bind(this)}
                                              discountCodeUpdateHandler={this.updateDiscountCode.bind(this)}/>
                        </div>
                    </div>
                    {this.state.errors.length > 0 &&  <div className="row error-alert">
                        <div className="col-sm-12">
                            Missing or invalid information provided, please check the form above and try again.
                        </div>
                    </div>}
                    {this.state.missing_records.length > 0 &&  <div className="row missing-records">
                        <div className="col-sm-12">
                            {this.renderMissingRecords()}
                        </div>
                    </div>}
                    <div className="row" style={{marginBottom: 15}}>
                        <div className="col-md-6 offset-md-6">
                            <GiftCardForm updateGiftCardNumber={this.updateGiftCardNumber.bind(this)}
                                          updateGiftCardAmount={this.updateGiftCardAmount.bind(this)}
                                          doAddGiftCard={this.doAddGiftCard.bind(this)}
                                          doAddTotalToGiftCard={this.doAddTotalToGiftCard.bind(this)}
                                          gift_card_amount={this.state.gift_card_amount}
                                          gift_card_number={this.state.gift_card_number}/>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-6">
                            {!this.state.cart_updating ? <table className="table">
                                    <tbody>
                                    <tr>
                                        <td><strong>Product Total:</strong></td>
                                        <td>{numeral(this.state.sub_total).format('$0,0.00')}</td>
                                    </tr>
                                    {(this.state.discount_amount && this.state.discount_amount < 0) ? <tr>
                                            <td><strong>Discount Amount:</strong></td>
                                            <td>{numeral(this.state.discount_amount).format('$0,0.00')}</td>
                                        </tr> : null}
                                    <tr>
                                        <td><strong>Sales Tax:</strong></td>
                                        <td>{numeral(this.state.tax).format('$0,0.00')}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Shipping Total:</strong></td>
                                        <td>{(this.state.shipping_total > 0) ? numeral(this.state.shipping_total).format('$0,0.00') : 'Free'}</td>
                                    </tr>
                                    {this.state.gift_card_amount_display && <tr>
                                        <td><strong>Gift Card:</strong></td>
                                        <td>{this.state.gift_card_amount_display}</td>
                                    </tr>}
                                    <tr className="grand-total">
                                        <td><strong>Grand Total:</strong></td>
                                        <td>{numeral(this.state.grand_total).format('$0,0.00')}</td>
                                    </tr>
                                    </tbody>
                                </table> : <h6><i className="fa fa-hourglass-half"/> Totals are being recalculated, please wait...</h6>}
                            {!this.state.bypass_pay_method && <div id="paypal-button"> </div>}
                        </div>
                        <div className="col-md-6">
                            {!this.state.bypass_pay_method && <CreditCardForm submitting_order={this.state.submitting_order} cc_info={this.state.cc_info}
                                            updateFieldHandler={this.updateCCField.bind(this)} orderSubmitHandler={this.submitOrder.bind(this)}
                                            cart_updating={this.state.cart_updating}/>}

                            {this.state.bypass_pay_method &&
                            <button style={{width: '100%', marginTop: 30}}
                                    onClick={this.submitOrder.bind(this)}
                                    disabled={(this.state.cart_updating || this.state.submitting_order) ? 'disabled' : ''} className="btn btn-success">
                                <i style={{marginRight: 4}} className={(this.state.cart_updating || this.state.submitting_order) ? 'fa fa-hourglass' : 'fa fa-check-circle'}/>
                                {(this.state.cart_updating || this.state.submitting_order) ? 'Please Wait...' : 'Submit Order'}</button>}
                        </div>
                    </div>
                </div>
            );
        }
    }

    static initialize()
    {
        let element = document.getElementById('checkout_form');
        if(element)
            ReactDOM.render(<CheckoutForm/>, element);
    }
}