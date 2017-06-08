import React from 'react';
import ReactDOM from 'react-dom';
import Input from '../../../admin/forms/Input';
import Util from '../../../core/Util';
import Popup from '../../../core/Popup';
import AddressForms from '../address_forms/AddressForms';
import SignInForm from '../../components/sign_in_form/SignInForm';

export default class UserRegistrationForm extends React.Component
{
    constructor()
    {
        super();
        this.state_options = [];
        this.popup = new Popup();

        this.state = {
            billing_address: {company: ''},
            shipping_address: {same_as_billing: true},
            errors: []
        };
    }

    updateField(field)
    {
        let state = Object.assign({}, this.state);
        state[field.name] = $(field).is(':checkbox') ? $(field).is(':checked') : field.value;
        this.setState(state);
    }

    updateBillingAddressField(field)
    {
        let state = Object.assign({}, this.state);
        state.billing_address[field.name] = $(field).is(':checkbox') ? $(field).is(':checked') : field.value;
        this.setState(state);
    }

    updateShippingAddressField(target)
    {
        let form_name = target.name;

        if($(target).is(':checkbox'))
        {
            this.state.shipping_address[form_name] = $(target).is(':checked');
        }
        else
        {
            this.state.shipping_address[form_name] = target.value;
        }

        this.setState(this.state);
    }

    doSubmit(e)
    {
        e.preventDefault();
        this.setState({errors: []});
        let state = Object.assign({}, this.state);
        if(state.terms_check)
        {
            // Handle submission of user
            $.ajax({
                url: '/register',
                method: 'POST',
                timeout: 3000,
                dataType: 'json',
                data: {_token: Util.get_auth_token(), data: state},
                complete: (res) => {
                    if(res.status == 200)
                    {
                        if(res.responseJSON.errors)
                        {
                            state.errors = res.responseJSON.errors;
                            this.setState(state);
                        }
                        else if(res.responseJSON.system_error)
                        {
                            this.popup.show(res.responseJSON.system_error);
                        }
                        else
                        {
                            // Registration success
                            window.location = '/';
                        }
                    }
                    else if(res.status == 0)
                    {

                    }
                }
            });
        }
        else
        {
            // Must agree to TOS
            this.popup.show('You must agree to our Terms Of Service Agreement to register for an account.');
        }
    }

    render()
    {
        let address_forms = new AddressForms({
            errors: this.state.errors,
            section_class_name: 'tab-pane fade',
            billing_address: this.state.billing_address,
            shipping_address: this.state.shipping_address,
            updateBillingAddress: this.updateBillingAddressField.bind(this),
            updateShippingAddress: this.updateShippingAddressField.bind(this)
        });

        // Check for errors in addresses
        let has_billing_address_errors = false;
        let has_shipping_address_errors = false;

        for(let c = 0; c < this.state.errors.length; c++)
        {
            let error = this.state.errors[c];
            for(let key in error)
            {
                if(!error.hasOwnProperty(key))
                    continue;

                if(!has_billing_address_errors)
                    has_billing_address_errors = (key.match('billing_address') !== null);

                if(!has_shipping_address_errors)
                    has_shipping_address_errors = (key.match('shipping_address') !== null);
            }
        }

        return(
            <div className="row">
                <div className="col-lg-4">
                    <h2 style={{textTransform: 'uppercase', fontSize: 21}}>Returning Customers</h2>
                    <SignInForm sign_in_link="/sign-in"/>
                </div>
                <div className="col-lg-8">
                    <h2 style={{textTransform: 'uppercase', fontSize: 21}}>New Customers</h2>
                    <form onSubmit={this.doSubmit.bind(this)} className="user-form" method="post">
                        <ul style={{marginBottom: 0, marginTop: 30}} className="nav nav-tabs">
                            <li className={'nav-item' + (!Util.objectIsEmpty(this.state.errors) ? ' has-errors' : '')}>
                                <a className="nav-link active" data-toggle="tab" href="#general"><i className="fa fa-info-circle" /> General Information</a>
                            </li>
                            <li className={'nav-item' + (has_billing_address_errors ? ' has-errors' : '')}>
                                <a className="nav-link" data-toggle="tab" href="#billing_address"><i className="fa fa-map-marker" /> Billing Address</a>
                            </li>
                            <li className={'nav-item' + (has_shipping_address_errors ? ' has-errors' : '')}>
                                <a className="nav-link" data-toggle="tab" href="#shipping_address"><i className="fa fa-truck" /> Shipping Address</a>
                            </li>
                        </ul>
                        <div className="tab-content">
                            <div id="general" className="tab-pane fade in active">
                                <Input label="First Name" errors={this.state.errors} updateValueHandler={this.updateField.bind(this)} value={this.state.first_name} type="text" name="first_name" is_required="true"/>
                                <Input label="Last Name" errors={this.state.errors} updateValueHandler={this.updateField.bind(this)} value={this.state.last_name} type="text" name="last_name" is_required="true"/>
                                <Input label="Email" errors={this.state.errors} updateValueHandler={this.updateField.bind(this)} value={this.state.email} type="text" name="email" is_required="true"/>
                                <Input label="Phone Number" errors={this.state.errors} updateValueHandler={this.updateField.bind(this)} value={this.state.phone_number} type="text" name="phone_number"/>
                                <Input label="Password" errors={this.state.errors} updateValueHandler={this.updateField.bind(this)} value={this.state.password} type="password" name="password" is_required="true"/>
                                <Input label="Confirm Password" errors={this.state.errors} updateValueHandler={this.updateField.bind(this)} value={this.state.confirm_password} type="password" name="password_confirmation" is_required="true"/>
                            </div>

                            {address_forms.render_billing_section()}
                            {address_forms.render_shipping_section()}

                            <div className="form-group confirm-checks">
                                <Input label="I have read, understand and agree to the Terms of Service"
                                       errors={this.state.errors}
                                       updateValueHandler={this.updateField.bind(this)} value={this.state.terms_check} type="checkbox" name="terms_check" is_required="true"/>
                                <div>
                                    <a href="/terms" target="_blank">Read The Terms Of Service</a>
                                </div>
                            </div>
                            <div className="form-group">
                                <button type="submit" className="btn btn-success save-form"><i className="fa fa-user"/> Register</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('user_registration_form');
        if(element)
            ReactDOM.render(<UserRegistrationForm/>, element);
    }
}