/**
 * Form used in Admin panel for creating and updating user records.
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';
import Input from '../Input';
import Util from '../../../core/Util';

export default class UserEditForm extends React.Component
{
    componentWillMount()
    {
        let state = Object.assign({}, this.props);

        state.user_data = state.user_data || {};
        state.user_data.billing_address = state.user_data.billing_address || {};
        state.user_data.shipping_address = state.user_data.shipping_address || {};
        this.setState(state);
    }

    saveUser(e)
    {
        e.preventDefault();

        // Get data for general attributes
        let form_inputs = $('#general').find(':input');
        let form_data = {};
        for(let form_input of form_inputs)
        {
            if(!form_input.name || form_input.name == '')
                continue;

            let value = null;
            if($(form_input).is(':checkbox'))
                value = $(form_input).is(':checked');
            else
                value = form_input.value;

            form_data[form_input.name] = value;
        }
        
        // Get data for billing address attributes
        form_inputs = $('#billing_address').find(':input');
        form_data.billing_address = {};
        for(let form_input of form_inputs)
        {
            if(!form_input.name || form_input.name == '')
                continue;

            let value = null;
            if($(form_input).is(':checkbox'))
                value = $(form_input).is(':checked');
            else
                value = form_input.value;

            form_data.billing_address[form_input.name] = value;
        }

        // Get data for shipping address attributes
        form_inputs = $('#shipping_address').find(':input');
        form_data.shipping_address = {};
        for(let form_input of form_inputs)
        {
            if(!form_input.name || form_input.name == '')
                continue;

            let value = null;
            if($(form_input).is(':checkbox'))
                value = $(form_input).is(':checked');
            else
                value = form_input.value;

            form_data.shipping_address[form_input.name] = value;
        }

        $.ajax({
            url: '/admin/record/Entity/' + (this.state.user_data.id ? this.state.user_data.id : ''),
            method: (this.state.user_data.id) ? 'PUT' : 'POST',
            dataType: 'json',
            timeout: 4000,
            data: {_token: Util.get_auth_token(), data: form_data},
            complete: (res) => {
                if(res.status == 200)
                {
                    if(!Util.objectIsEmpty(res.responseJSON.errors))
                    {
                        this.setState({errors: res.responseJSON.errors});
                    }
                    else
                    {
                        window.location = '/admin/entity/' + res.responseJSON.id;
                    }
                }
                else if(res.status == 0)
                {
                    // Todo: handle timeout
                }
            }
        });
    }

    doDelete(e)
    {
        e.preventDefault();
        if(confirm('Delete user?'))
        {
            $.ajax({
                url: '/admin/record/Entity/' + this.state.user_data.id,
                method: 'DELETE',
                data: {_token: Util.get_auth_token()},
                timeout: 3000,
                dataType: 'json',
                complete: (res) => {
                    if(res.status == 200)
                    {
                        window.location = '/admin/list/entity';
                    }
                    else if(res. status == 0)
                    {
                        // Todo: handle timeout
                    }
                }
            });
        }
    }

    render()
    {
        return(
            <div>
                <div style={{marginBottom: 32}}>
                    <a href="/admin/entity" className="btn btn-secondary"><i className="fa fa-pencil-square" /> Create New</a>
                    <a href="/admin/list/entity" className="btn btn-secondary"><i className="fa fa-list" /> List Users</a>
                </div>
                <form className="user-data-form" method="post">
                    <ul className="nav nav-pills">
                        <li className="nav-item"><a className="nav-link active" data-toggle="tab" href="#general"><i className="fa fa-info-circle" /> General Information</a></li>
                        <li className="nav-item"><a className="nav-link" data-toggle="tab" href="#billing_address"><i className="fa fa-map-marker" /> Billing Address</a></li>
                        <li className="nav-item"><a className="nav-link" data-toggle="tab" href="#shipping_address"><i className="fa fa-truck" /> Shipping Address</a></li>
                    </ul>
                    <div className="tab-content">
                        <div id="general" className="tab-pane fade in active">
                            <Input is_required="true" type="text" name="first_name" label="First Name" errors={this.state.errors} value={this.state.user_data.first_name}/>
                            <Input is_required="true" type="text" name="last_name" label="Last Name" errors={this.state.errors} value={this.state.user_data.last_name}/>
                            <Input is_required="true" type="text" name="email" label="Email" errors={this.state.errors} value={this.state.user_data.email}/>
                            <Input type="text" name="phone_number" label="Phone Number" errors={this.state.errors} value={this.state.user_data.phone_number}/>
                            <Input type="password" name="password" label="Password" errors={this.state.errors}/>
                            <Input is_required="true" value={this.state.user_data.role} type="select" name="role" label="Role"
                                   options={[{id: 'customer', label: 'Customer'}, {id: 'admin', label: 'Administrator'}]}
                                   errors={this.state.errors}/>

                            <Input is_required="true" value={this.state.user_data.status} type="select" name="status" label="Status"
                                   options={[{id: 'unverified', label: 'Unverified'}, 
                                            {id: 'verified', label: 'Verified'}, {id: 'banned', label: 'Banned'}]}
                                   errors={this.state.errors}/>

                            <Input type="checkbox" name="is_person" label="Is Person?" value={this.state.user_data.is_person}/>
                            <Input type="checkbox" name="is_fraudulent" label="Is Fraudulent?" value={this.state.user_data.is_fraudulent}/>
                            <Input type="checkbox" name="is_inactive" label="Is Inactive?" value={this.state.user_data.is_inactive}/>
                        </div>
                        <div id="billing_address" className="tab-pane fade">
                            <Input is_required="true" type="text" name="first_name" label="First Name" error_key="billing_address.first_name" errors={this.state.errors} value={this.state.user_data.billing_address.first_name}/>
                            <Input is_required="true" type="text" name="last_name" label="Last Name" error_key="billing_address.last_name" errors={this.state.errors} value={this.state.user_data.billing_address.last_name}/>
                            <Input is_required="true" type="text" name="line_1" label="Address Line 1" error_key="billing_address.line_1" errors={this.state.errors} value={this.state.user_data.billing_address.line_1}/>
                            <Input type="text" name="line_2" label="Address Line 2" error_key="billing_address.line_2" errors={this.state.errors} value={this.state.user_data.billing_address.line_2}/>
                            <Input is_required="true" type="text" name="city" label="City" error_key="billing_address.city" errors={this.state.errors} value={this.state.user_data.billing_address.city}/>
                            <Input is_required="true" type="text" name="state" label="State" error_key="billing_address.state" errors={this.state.errors} value={this.state.user_data.billing_address.state}/>
                            <Input is_required="true" type="text" name="zip" label="Zip Code" error_key="billing_address.zip" errors={this.state.errors} value={this.state.user_data.billing_address.zip}/>
                        </div>
                        <div id="shipping_address" className="tab-pane fade">
                            <Input is_required="true" type="text" name="first_name" label="First Name" error_key="shipping_address.first_name" errors={this.state.errors} value={this.state.user_data.shipping_address.first_name}/>
                            <Input is_required="true" type="text" name="last_name" label="Last Name" error_key="shipping_address.last_name" errors={this.state.errors} value={this.state.user_data.shipping_address.last_name}/>
                            <Input is_required="true" type="text" name="line_1" label="Address Line 1" error_key="shipping_address.line_1" errors={this.state.errors} value={this.state.user_data.shipping_address.line_1}/>
                            <Input type="text" name="line_2" label="Address Line 2" error_key="shipping_address.line_2" errors={this.state.errors} value={this.state.user_data.shipping_address.line_2}/>
                            <Input is_required="true" type="text" name="city" label="City" error_key="shipping_address.city" errors={this.state.errors} value={this.state.user_data.shipping_address.city}/>
                            <Input is_required="true" type="text" name="state" label="State" error_key="shipping_address.state" errors={this.state.errors} value={this.state.user_data.shipping_address.state}/>
                            <Input is_required="true" type="text" name="zip" label="Zip Code" error_key="shipping_address.zip" errors={this.state.errors} value={this.state.user_data.shipping_address.zip}/>
                        </div>
                        <div className="form-group">
                            <button onClick={this.saveUser.bind(this)} type="button" className="btn btn-success"><i className="fa fa-save" /> Save User</button>
                            <button onClick={this.doDelete.bind(this)} type="button" className="btn btn-danger"><i className="fa fa-times" /> Delete User</button>
                        </div>
                    </div>
                </form>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('user_edit_form');
        if(element)
        {
            ReactDOM.render(<UserEditForm user_data={JSON.parse(element.dataset.userData)}/>, element);
        }
    }
}
