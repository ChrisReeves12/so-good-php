/**
 * Class definition of PasswordRecoveryForm
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';
import Popup from '../../../core/Popup';
import Util from '../../../core/Util';
import Input from '../../../admin/forms/Input';

export default class PasswordRecoveryForm extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            email: '',
            errors: false
        };

        this.popup = new Popup();
    }

    updateEmail(e)
    {
        this.setState({email: e.target.value, has_been_sent: false});
    }

    doSubmit(e)
    {
        e.preventDefault();
        this.setState({errors: false});

        $.ajax({
            url: '/forgot-password/send-email',
            method: 'POST',
            dataType: 'json',
            data: {email: this.state.email, _token: Util.get_auth_token()},
            timeout: 3000,
            complete: (res) => {
                if(res.status == 200)
                {
                    if(res.responseJSON.errors)
                    {
                        this.setState({errors: res.responseJSON.errors});
                    }
                    else if(res.responseJSON.system_error)
                    {
                        this.popup.show(res.responseJSON.system_error);
                    }
                    else
                    {
                        this.popup.show('You will receive an email shortly with instructions on resetting your password.');
                        this.setState({has_been_sent: true});
                    }
                }
                else if(res.status == 0)
                {
                    popup.show('The operation timed out while attempting to send the email, please try again.');
                }
            }
        });
    }

    updateValueHandler(field)
    {
        this.setState({email: field.value});
    }

    render() {
        return (
            <form onSubmit={this.doSubmit.bind(this)} className="recover-password">
                <div className="form-group">
                    <Input value={this.state.email} is_required="true" type="text"
                           updateValueHandler={this.updateValueHandler.bind(this)}
                           name="email" errors={this.state.errors} label="Email Address"/>
                </div>
                <div className="form-group">
                    <button className="btn btn-success submit"><i className="fa fa-arrow-circle-right" /> {this.state.has_been_sent ? 'Resend Email' : 'Send Email'}</button>
                </div>
            </form>
        );
    }

    static initialize()
    {
        let element = document.getElementById('password_recovery_form');
        if(element)
        {
            ReactDOM.render(<PasswordRecoveryForm/>, element);
        }
    }
}