/**
 * Form used in Admin panel for creating and updating stock location records.
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';
import Input from '../Input';
import Util from '../../../core/Util';
import Popup from '../../../core/Popup';

export default class StockLocationEditForm extends React.Component
{
    componentWillMount()
    {
        let state = Object.assign({}, this.props.initial_data);
        if(!state.address)
            state.address = {};

        this.setState(state);
    }

    doSubmitForm(e)
    {
        let form_data = Object.assign({}, this.state);

        e.preventDefault();
        $.ajax({
            url: '/admin/record/StockLocation/' + (this.state.id ? this.state.id : ''),
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
                        window.location = '/admin/stock-location/' + res.responseJSON.id;
                    }
                }
                else if(res.statusCode == 0)
                {
                    // Todo: Handle timeout
                }
            }
        });
    }

    handleFieldUpdate(target)
    {
        let name = target.name;
        let state = Object.assign({}, this.state);
        if(['line_1', 'line_2', 'city', 'state', 'zip', 'country'].indexOf(name) > -1)
            state.address[name] = target.value;
        else if(['is_dropship', 'is_main_location'].indexOf(name) > -1)
            state[name] = $(target).is(':checked');
        else
            state[name] = target.value;

        this.setState(state);
    }

    doDelete(e)
    {
        e.preventDefault();
        if(confirm('Delete stock location?'))
        {
            $.ajax({
                url: '/admin/record/StockLocation/' + this.state.id,
                method: 'DELETE',
                data: {_token: Util.get_auth_token()},
                timeout: 3000,
                dataType: 'json',
                complete: (res) => {
                    if(res.status == 200)
                    {
                        window.location = '/admin/list/stockLocation';
                    }
                    else if(res.status == 0)
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
                    <a href="/admin/stock-location" className="btn btn-secondary"><i className="fa fa-pencil-square" /> Create New</a>
                    <a href="/admin/list/stockLocation" className="btn btn-secondary"><i className="fa fa-list" /> List All Stock Locations</a>
                </div>
                <form className="stock-location-form" onSubmit={this.doSubmitForm.bind(this)} method="post">
                    <Input name="name" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors} label="Name" type="text" value={this.state.name} is_required="true"/>
                    <Input name="phone_number" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors} label="Phone Number" type="text" value={this.state.phone_number}/>
                    <Input name="line_1" error_key="address.line_1" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors} is_required="true" label="Address Line 1" type="text" value={this.state.address.line_1}/>
                    <Input name="line_2" error_key="address.line_2" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors} label="Address Line 2" type="text" value={this.state.address.line_2}/>
                    <Input name="city" error_key="address.city" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors} is_required="true" label="City" type="text" value={this.state.address.city}/>
                    <Input name="state" error_key="address.state" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors} is_required="true" label="State" type="text" value={this.state.address.state}/>
                    <Input name="zip" error_key="address.zip" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors} is_required="true" label="Zip Code" type="text" value={this.state.address.zip}/>
                    <Input name="country" error_key="address.country" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors} is_required="true" label="Country" type="text" value={this.state.address.country}/>
                    <Input name="is_dropship" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors} label="Is Dropship?" type="checkbox" value={this.state.is_dropship}/>
                    <Input name="is_main_location" updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors} label="Is Main Location?" type="checkbox" value={this.state.is_main_location}/>
                    <div className="form-group">
                        <button type="submit" className="btn btn-success"><i className="fa fa-save" /> Save Stock Location</button>
                        <button onClick={this.doDelete.bind(this)} type="button" className="btn btn-danger"><i className="fa fa-times" /> Delete Stock Location</button>
                    </div>
                </form>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('stock_location_form');
        if(element)
        {
            ReactDOM.render(<StockLocationEditForm initial_data={JSON.parse(element.dataset.initialData)}/>, element);
        }
    }
}
