/**
 * Form used in Admin panel for modifying store settings
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import Input from './../Input';
import Util from '../../../core/Util';

export default class SettingsEditForm extends React.Component
{
    componentWillMount()
    {
        this.setState(this.props);
    }

    saveSettings(e)
    {
        e.preventDefault();
        let form_inputs = $('form.store-settings-form').find(':input');
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

        $.ajax({
            url: '/admin/store-settings',
            method: 'PUT',
            dataType: 'json',
            timeout: 4000,
            data: {_token: Util.get_auth_token(), data: form_data},
            complete: (res) => {
                if(res.status == 200)
                {
                    window.location.reload();
                }
                else if(res.status == 0)
                {
                    // Todo: handle timeout
                }
            }
        });
    }

    render()
    {
        return(
            <div>
                <p>Configure store settings.</p>
                <form className="store-settings-form" method="post">
                    <Input value={this.state.store_settings_data.store_name} is_required="true" name="store_name" label="Store Name" type="text"/>
                    <Input value={this.state.store_settings_data.site_title} is_required="true" name="site_title" label="Site Title" type="text"/>
                    <Input value={this.state.store_settings_data.store_email} is_required="true" name="store_email" label="Store Email" type="text"/>
                    <Input value={this.state.store_settings_data.shipping_carrier} is_required="true" name="shipping_carrier" label="Shipping Carrier" type="select"
                           options={[
                               {id: 'ups', label: 'UPS'},
                               {id: 'fedex', label: 'FedEx'},
                               {id: 'usps', label: 'USPS'},
                               {id: 'none', label: 'None'}
                               ]}/>

                    <Input value={this.state.store_settings_data.auto_inventory} type="checkbox" name="auto_inventory" label="Auto Inventory Update"/>
                    <Input value={this.state.store_settings_data.tax_line_items} type="checkbox" name="tax_line_items" label="Tax Line Items"/>
                    <div className="form-group">
                        <button onClick={this.saveSettings.bind(this)} type="submit" className="btn btn-success"><i className="fa fa-save" /> Save Store Settings</button>
                    </div>
                </form>
            </div>
        );
    }
}
