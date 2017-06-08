/**
 * Class definition of AddressForms
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import Input from '../../../admin/forms/Input';
import states_json from '../../../admin/config/States';

export default class AddressForms extends React.Component {
    constructor(props) {
        super(props);

        this.state_options = [];
        for(let initial in states_json)
        {
            if(states_json.hasOwnProperty(initial))
            {
                this.state_options.push({id: initial, label: states_json[initial]});
            }
        }
    }

    render_billing_section()
    {
        return(
            <div id="billing_address" className={this.props.section_class_name}>
                <Input label="Company" errors={this.props.errors} error_key='billing_address.company' updateValueHandler={this.props.updateBillingAddress} value={this.props.billing_address.company} type="text" name="company"/>
                <Input label="Address Line 1" errors={this.props.errors} error_key='billing_address.line_1' is_required="true" updateValueHandler={this.props.updateBillingAddress} value={this.props.billing_address.line_1} type="text" name="line_1"/>
                <Input label="Address Line 2" errors={this.props.errors} error_key='billing_address.line_2' updateValueHandler={this.props.updateBillingAddress} value={this.props.billing_address.line_2} type="text" name="line_2"/>
                <Input label="City" errors={this.props.errors} error_key='billing_address.city' is_required="true" updateValueHandler={this.props.updateBillingAddress} value={this.props.billing_address.city} type="text" name="city"/>
                <Input label="State" errors={this.props.errors} error_key='billing_address.state' is_required="true" updateValueHandler={this.props.updateBillingAddress} value={this.props.billing_address.state} type="select" options={this.state_options} name="state"/>
                <Input label="Zip Code" errors={this.props.errors} error_key='billing_address.zip' is_required="true" updateValueHandler={this.props.updateBillingAddress} value={this.props.billing_address.zip} type="text" name="zip"/>
            </div>
        );
    }

    render_shipping_section()
    {
        return(
            <div id="shipping_address" className={this.props.section_class_name}>
                <Input label="Same as billing address" updateValueHandler={this.props.updateShippingAddress} value={this.props.shipping_address.same_as_billing} type="checkbox" name="same_as_billing"/>
                <Input label="Company" errors={this.props.errors} error_key='shipping_address.company' updateValueHandler={this.props.updateShippingAddress} value={this.props.shipping_address.company} type="text" name="company"/>
                <Input label="Address Line 1" errors={this.props.errors} error_key='shipping_address.line_1' updateValueHandler={this.props.updateShippingAddress} value={this.props.shipping_address.line_1} type="text" name="line_1"/>
                <Input label="Address Line 2" errors={this.props.errors} error_key='shipping_address.line_2' updateValueHandler={this.props.updateShippingAddress} value={this.props.shipping_address.line_2} type="text" name="line_2"/>
                <Input label="City" errors={this.props.errors} error_key='shipping_address.city' updateValueHandler={this.props.updateShippingAddress} value={this.props.shipping_address.city} type="text" name="city"/>
                <Input label="State" errors={this.props.errors} error_key='shipping_address.state' updateValueHandler={this.props.updateShippingAddress} value={this.props.shipping_address.state} type="select" options={this.state_options} name="state"/>
                <Input label="Zip Code" errors={this.props.errors} error_key='shipping_address.zip' updateValueHandler={this.props.updateShippingAddress} value={this.props.shipping_address.zip} type="text" name="zip"/>
            </div>
        );
    }

    render() {
        return(
            <div>
                {this.render_billing_section()}
                {this.render_shipping_section()}
            </div>
        );
    }
}