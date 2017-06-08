/**
 * SubscriberForm
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

import React from 'react';
import ReactDOM from 'react-dom';
import Input from 'forms/Input';
import Popup from 'common/core/Popup';
import Util from 'common/core/Util';

export default class SubscriberForm extends React.Component
{
    constructor()
    {
        super();
        this.state = window.sogood.reactjs.initial_data;
        this.state.synced = this.state.synced || false;
        this.state.is_inactive = this.state.is_inactive || false;
        this.state.errors = [];
    }

    updateValueHandler(input) {
        let state = Object.assign({}, this.state);
        let value;
        let name = input.name;

        if(input.type !== 'checkbox') {
            value = input.value;
        }
        else {
            value = $(input).is(':checked');
        }

        state[name] = value;
        this.setState(state);
    }

    doSave(e)
    {
        e.preventDefault();
        this.setState({errors: []});
        let state = Object.assign({}, this.state);
        state.errors = [];

        $.ajax({
            url: '/admin/record/Subscription/' + (this.state.id || ''),
            method: (this.state.id) ? 'PUT' : 'POST',
            dataType: 'json',
            data: {data: state, _token: Util.get_auth_token()},
            complete: (res) => {
                if(res.status === 200)
                {
                    if(res.responseJSON.errors)
                    {
                        state.errors = res.responseJSON.errors;
                    }
                    else if(res.responseJSON.system_error)
                    {
                        let popup = new Popup();
                        popup.show(res.responseJSON.system_error);
                    }
                    else
                    {
                        window.location = '/admin/subscription/' + res.responseJSON.id;
                    }

                    this.setState(state);
                }
            }
        });
    }

    render()
    {
        return(
            <div>
                <div style={{marginBottom: '32px'}}>
                    <a href="/admin/subscription" className="btn btn-secondary"><i className="fa fa-pencil-square"/> Create New</a>
                    <a href="/admin/list/subscription" className="btn btn-secondary"><i className="fa fa-list"/> List All Newsletter Subscriptions</a>
                </div>
                <form onSubmit={this.doSave.bind(this)}>
                    <Input is_required="true" errors={this.state.errors}
                           updateValueHandler={this.updateValueHandler.bind(this)} value={this.state.email}
                           name="email" label="Email" type="text"/>

                    <Input errors={this.state.errors}
                           updateValueHandler={this.updateValueHandler.bind(this)} value={this.state.synced}
                           name="synced" label="Is Synced?" type="checkbox"/>

                    <Input errors={this.state.errors}
                           updateValueHandler={this.updateValueHandler.bind(this)} value={this.state.is_inactive}
                           name="is_inactive" label="Is Inactive?" type="checkbox"/>

                    <button type="submit" style={{marginTop: 15}} className="btn btn-primary"><i className="fa fa-save"/> Save
                        Subscription
                    </button>
                </form>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('subscriber_form');
        if(element)
        {
            ReactDOM.render(<SubscriberForm/>, element);
        }
    }
}