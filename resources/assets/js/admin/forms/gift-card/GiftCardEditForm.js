/**
 * GiftCardEditForm
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

import React from 'react';
import ReactDOM from 'react-dom';
import Input from 'forms/Input';
import Util from 'common/core/Util';
import Popup from 'common/core/Popup';

export default class GiftCardEditForm extends React.Component
{
    constructor()
    {
        super();
        let state = window.sogood.reactjs.initial_data;
        state.errors = [];
        state.loading = false;
        this.state = state;
    }

    updateValue(element)
    {
        let state = Object.assign({}, this.state);

        if(element.type === 'checkbox')
        {
            state[element.name] = $(element).is(':checked');
        }
        else
        {
            state[element.name] = element.value;
        }

        this.setState(state);
    }

    doSubmit(e)
    {
        e.preventDefault();
        this.setState({errors: []});
        let data = Object.assign({}, this.state);

        $.ajax({
            url: '/admin/record/GiftCard/' + (this.state.id || ''),
            method: (this.state.id) ? 'PUT' : 'POST',
            dataType: 'json',
            data: {data, _token: Util.get_auth_token()},
            complete: (res) => {
                if(res.status === 200)
                {
                    if(res.responseJSON.errors)
                    {
                        data.errors = res.responseJSON.errors;
                    }
                    else if(res.responseJSON.system_error)
                    {
                        let popup = new Popup();
                        popup.show(res.responseJSON.system_error);
                    }
                    else
                    {
                        window.location = '/admin/gift-card/' + res.responseJSON.id;
                    }

                    this.setState(data);
                }
            }
        });
    }

    generateCardNumber(e)
    {
        e.preventDefault();

        if($.active === 0)
        {
            this.setState({loading: true});

            $.get('/admin/gift-card/generate-card-number')
                .then(res => {
                    this.setState({number: res.number, loading: false});
                });
        }
    }

    render()
    {
        return(
            <div>
                <div style={{marginBottom: '32px'}}>
                    <a href="/admin/gift-card" className="btn btn-secondary"><i className="fa fa-pencil-square"/> Create New</a>
                    <a href="/admin/list/giftCard" className="btn btn-secondary"><i className="fa fa-list"/> List All Gift Cards</a>
                </div>
                <form onSubmit={this.doSubmit.bind(this)}>
                    <Input is_required="true" updateValueHandler={this.updateValue.bind(this)} name="number" errors={this.state.errors} value={this.state.number} label="Number" type="text"/>

                    <button onClick={this.generateCardNumber.bind(this)}
                            style={{fontSize: 12, padding: '3px 9px', marginBottom: 15, marginTop: '-5px'}}
                            className="btn btn-info"><i className={"fa " + (this.state.loading ? 'fa-spinner' : 'fa-gear')}/> {this.state.loading ? 'Please wait...' : 'Generate Card Number'}</button>

                    <Input is_required="true" updateValueHandler={this.updateValue.bind(this)} name="balance" errors={this.state.errors} value={this.state.balance} label="Balance" type="text"/>
                    <Input is_required="true" updateValueHandler={this.updateValue.bind(this)} name="email" errors={this.state.errors} value={this.state.email} label="Email" type="text"/>
                    <Input is_required="true" updateValueHandler={this.updateValue.bind(this)} name="exp_date" errors={this.state.errors} value={this.state.exp_date} label="Exp. Date (MM/DD/YYYY)" type="text"/>
                    <Input updateValueHandler={this.updateValue.bind(this)} name="is_inactive" errors={this.state.errors} value={this.state.is_inactive} label="Is Inactive?" type="checkbox"/>
                    <div className="form-group">
                        <label>Last Reload Date</label>
                        <p>{this.state.last_reload_date}</p>
                    </div>
                    <div className="form-group">
                        <button className="btn btn-success" type="submit"><i className="fa fa-save"/> Save Gift Card</button>
                    </div>
                </form>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('gift_card_edit_form');

        if(element)
            ReactDOM.render(<GiftCardEditForm/>, element);
    }
}
