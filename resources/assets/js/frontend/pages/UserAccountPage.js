/**
 * Class definition of UserAccountPage
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';
import Util from '../../../../../app/assets/javascript/core/Util';
import Popup from '../../../../../app/assets/javascript/core/Popup';
import Input from '../../../../../app/assets/javascript/react/forms/Input';
import AddressForms from '../../react/components/address_forms/AddressForms';

class UserAccountPage extends React.Component {
    constructor(props) {
        super(props);

        let state = props.user_data.user;
        state.errors = {billing_address: [], shipping_address: [], form_errors: []};
        state.billing_address = props.user_data.billing_address;
        state.shipping_address = props.user_data.shipping_address || {};
        state.shipping_address.same_as_billing = (props.user_data.shipping_address == null);
        state.password = '';
        state.password_confirmation = '';
        state.initial_data = JSON.stringify({
            first_name: props.user_data.user.first_name,
            last_name: props.user_data.user.last_name,
            email: props.user_data.user.email,
            password: state.password,
            password_confirmation: state.password_confirmation,
            billing_address: state.billing_address,
            shipping_address: state.shipping_address
        });

        this.popup = new Popup();
        this.state = state;
    }

    updateBillingAddress(target)
    {
        let form_name = target.name;

        this.state.billing_address[form_name] = target.value;
        this.setState(this.state);
    }

    updateShippingAddress(target)
    {
        let form_name = target.name;

        this.state.shipping_address[form_name] = target.value;
        this.setState(this.state);
    }

    updateGeneralInfo(target)
    {
        let form_name = target.name;

        this.state[form_name] = target.value;
        this.setState(this.state);
    }

    doSubmit(e)
    {
        let state = Object.assign({}, this.state);
        state.errors = {billing_address: [], shipping_address: [], form_errors: []};
        state.initial_data = {};
        this.setState(state);

        e.preventDefault();
        $.ajax({
            url: '/account',
            method: 'PUT',
            dataType: 'json',
            timeout: 4000,
            data: {data: state, authenticity_token: Util.get_auth_token()},
            complete: (res) => {
                if(res.status == 200)
                {
                    if(res.responseJSON.errors == 'Validation Error')
                    {
                        let errors = res.responseJSON.form_errors;
                        errors.shipping_address = res.responseJSON.shipping_address_errors;
                        errors.billing_address = res.responseJSON.billing_address_errors;
                        errors.form_errors = res.responseJSON.form_errors;

                        this.setState({errors});
                    }
                    else
                    {
                        let initial_data = JSON.stringify({
                            first_name: state.first_name,
                            last_name: state.last_name,
                            email: state.email,
                            password: '',
                            password_confirmation: '',
                            billing_address: state.billing_address,
                            shipping_address: state.shipping_address
                        });
                        
                        this.setState({initial_data, password: '', password_confirmation: ''});
                    }
                }
                else if(res.status == 0)
                {
                    this.popup.show('Timed out while saving account settings, please try again.');
                }
            }
        });
    }

    render()
    {
        let address_props = {
                errors: this.state.errors,
                section_class_name: 'address-section',
                billing_address: this.state.billing_address,
                shipping_address: this.state.shipping_address,
                updateBillingAddress: this.updateBillingAddress.bind(this),
                updateShippingAddress: this.updateShippingAddress.bind(this)
            };

        let address_forms = new AddressForms(address_props);

        let form_data = JSON.stringify({
            first_name: this.state.first_name,
            last_name: this.state.last_name,
            email: this.state.email,
            password: this.state.password,
            password_confirmation: this.state.password_confirmation,
            billing_address: this.state.billing_address,
            shipping_address: this.state.shipping_address
        });

        let enable_update_button = (this.state.initial_data !== form_data);

        return(
            <div className="col-md-8 col-lg-6">
                <form onSubmit={this.doSubmit.bind(this)}>
                    <ul style={{marginBottom: 25}} className="nav nav-pills">
                        <li className={"nav-item " + ((this.state.errors.form_errors.length > 0) ? 'has-errors' : '')}>
                            <a className="nav-link active" data-toggle="tab" href="#general_form">General Information</a>
                        </li>
                        <li className={"nav-item " + ((this.state.errors.billing_address.length > 0) ? 'has-errors' : '')}>
                            <a className="nav-link" data-toggle="tab" href="#billing_form">Billing Shipping</a>
                        </li>
                        <li className={"nav-item " + ((this.state.errors.shipping_address.length > 0) ? 'has-errors' : '')}>
                            <a className="nav-link" data-toggle="tab" href="#shipping_form">Shipping Address</a>
                        </li>
                    </ul>
                    <div className="tab-content">
                        <div className="tab-pane active" id="general_form">
                            <Input errors={this.state.errors.form_errors} value={this.state.first_name} updateValueHandler={this.updateGeneralInfo.bind(this)} type="text" label="First Name" name="first_name"/>
                            <Input errors={this.state.errors.form_errors} value={this.state.last_name} updateValueHandler={this.updateGeneralInfo.bind(this)} type="text" label="Last Name" name="last_name"/>
                            <Input errors={this.state.errors.form_errors} value={this.state.email} updateValueHandler={this.updateGeneralInfo.bind(this)} type="text" label="Email Address" name="email"/>
                            <Input errors={this.state.errors.form_errors} value={this.state.password} updateValueHandler={this.updateGeneralInfo.bind(this)} type="password" label="Password" name="password"/>
                            <Input errors={this.state.errors.form_errors} value={this.state.password_confirmation} updateValueHandler={this.updateGeneralInfo.bind(this)} type="password" label="Confirm Password" name="password_confirmation"/>
                        </div>
                        <div className="tab-pane" id="billing_form">
                            {address_forms.render_billing_section()}
                        </div>
                        <div className="tab-pane" id="shipping_form">
                            {address_forms.render_shipping_section()}
                        </div>
                    </div>
                    <button disabled={enable_update_button ? '' : 'disabled'} type="submit" className="btn btn-success"><i className="fa fa-save"/> Update Changes</button>
                </form>
            </div>
        );
    }
}

export default class UserAccountPageInitializer
{
    static initialize()
    {
        let element = document.getElementById('user_account_form');

        if(element)
            ReactDOM.render(<UserAccountPage user_data={JSON.parse(element.dataset.userData)}/>, element);
    }
}

