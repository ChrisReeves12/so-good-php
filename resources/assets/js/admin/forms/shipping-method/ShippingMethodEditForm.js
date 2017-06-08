/**
 * Form used in Admin panel for creating and updating Shipping Method records.
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';
import Input from '../Input';
import Util from '../../../core/Util';
import Popup from '../../../core/Popup';

export default class ShippingMethodEditForm extends React.Component
{
    componentWillMount()
    {
        let state = {};
        state = this.props.initial_data;
        this.setState(state);
    }

    handleFieldUpdate(target)
    {
        let name = target.name;
        let state = Object.assign({}, this.state);
        if(['is_inactive', 'is_express'].indexOf(name) > -1)
            state[name] = $(target).is(':checked');
        else
            state[name] = target.value;

        this.setState(state);
    }

    doSubmitForm(e)
    {
        let form_data = Object.assign({}, this.state);

        e.preventDefault();
        $.ajax({
            url: '/admin/record/ShippingMethod/' + (this.state.id ? this.state.id : ''),
            method: (this.state.id) ? 'PUT' : 'POST',
            timeout: 4000,
            dataType: 'json',
            data: {_token: Util.get_auth_token(), data: form_data},
            complete: (res) => {
                if(res.status == 200)
                {
                    if(!Util.objectIsEmpty(res.responseJSON.errors))
                    {
                        this.setState({errors: res.responseJSON.errors});
                    }
                    else if(res.responseJSON.system_error && res.responseJSON.system_error !== '')
                    {
                        let popup = new Popup();
                        popup.show(res.responseJSON.system_error);
                    }
                    else
                    {
                        window.location = '/admin/shipping-method/' + res.responseJSON.id;
                    }
                }
                else if(res.statusCode == 0)
                {
                    // Todo: Handle timeout
                }
            }
        });
    }

    doDelete(e)
    {
        e.preventDefault();
        if(confirm('Delete shipping method?'))
        {
            $.ajax({
                url: '/admin/record/ShippingMethod/' + this.state.id,
                method: 'DELETE',
                data: {_token: Util.get_auth_token()},
                timeout: 3000,
                dataType: 'json',
                complete: (res) => {
                    if(res.status == 200)
                    {
                        window.location = '/admin/list/shippingMethod';
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
                    <a href="/admin/shipping-method" className="btn btn-secondary"><i className="fa fa-pencil-square" /> Create New</a>
                    <a href="/admin/list/shippingMethod" className="btn btn-secondary"><i className="fa fa-list" /> List All Shipping Methods</a>
                </div>
                <form onSubmit={this.doSubmitForm.bind(this)} method="post">
                    <Input name="name" value={this.state.name} is_required="true" type="text" label="Name" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors}/>
                    <Input name="api_identifier" value={this.state.api_identifier} is_required="true" type="text" label="API Identifier" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors}/>
                    <Input name="transit_time" value={this.state.transit_time} is_required="true" type="text" label="Transit Time" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors}/>
                    <Input name="carrier_name" value={this.state.carrier_name} is_required="true" type="select" label="Carrier" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors}
                        options={[
                            {id: 'fedex', label: 'FedEx'},
                            {id: 'ups', label: 'UPS'},
                            {id: 'usps', label: 'US Postal Service'},
                            {id: 'none', label: 'None'}
                        ]}/>
                    <Input name="calculation_method" value={this.state.calculation_method} is_required="true" type="select" label="Calculation Method" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors}
                           options={[
                               {id: 'api', label: 'API Rates'},
                               {id: 'flat_rate', label: 'Flat Rate'}
                           ]}/>
                    <Input name="flat_rate" value={this.state.flat_rate} type="text" label="Flat Rate" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors}/>
                    <Input name="is_express" value={this.state.is_express} type="checkbox" label="Is Express?" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors}/>
                    <Input name="is_inactive" value={this.state.is_inactive} type="checkbox" label="Is Inactive?" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors}/>
                    <div className="form-group">
                        <button type="submit" className="btn btn-success"><i className="fa fa-save"/> Save Shipping Method</button>
                        <button onClick={this.doDelete.bind(this)} type="button" className="btn btn-danger"><i className="fa fa-times"/> Delete Shipping Method</button>
                    </div>
                </form>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('shipping_method_form');
        if(element)
        {
            ReactDOM.render(<ShippingMethodEditForm initial_data={JSON.parse(element.dataset.initialData)}/>, element);
        }
    }
}
