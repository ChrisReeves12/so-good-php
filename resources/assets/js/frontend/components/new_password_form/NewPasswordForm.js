/**
 * Class definition of NewPasswordForm
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';
import Util from '../../../core/Util';
import Popup from '../../../core/Popup';

export default class NewPasswordForm extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            password: '',
            confirm_password: '',
            errors: false
        };
        this.popup = new Popup();
    }

    updatePassword(e)
    {
        this.setState({password: e.target.value});
    }

    updateConfirmPassword(e)
    {
        this.setState({confirm_password: e.target.value});
    }

    doSubmit(e)
    {
        this.setState({errors: false});

        e.preventDefault();
        $.ajax({
            url: '/recover-password',
            method: 'PUT',
            dataType: 'json',
            timeout: 3000,
            data: {_token: Util.get_auth_token(), user_id: this.props.user_id, password: this.state.password, confirm_password: this.state.confirm_password},
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
                        window.location = '/sign-in';
                    }
                }
                else if(res.status == 0)
                {
                    this.popup.show('The operation timed out while resetting your password, please try again.');
                }
            }
        });
    }

    render() {
        return (
            <form onSubmit={this.doSubmit.bind(this)} className="password-reset">
                <div className={'form-group' + (this.state.errors ? ' has-danger' : '')}>
                    <label className="form-control-label">New Password</label>
                    <input value={this.state.password} onChange={this.updatePassword.bind(this)} name="password" placeholder="New Password" type="password" className="form-control" />
                    {this.state.errors &&
                        <span className="form-control-feedback">{this.state.errors}</span>}
                </div>
                <div className="form-group">
                    <label className="form-control-label">Repeat New Password</label>
                    <input value={this.state.confirm_password} onChange={this.updateConfirmPassword.bind(this)} name="confirm_password" placeholder="Confirm Password" type="password" className="form-control" />
                </div>
                <div className="form-group">
                    <button className="btn btn-success submit"><i className="fa fa-lock" /> Save New Password</button>
                </div>
            </form>
        );
    }

    static initialize()
    {
        let element = document.getElementById('new_password_form');
        if(element)
        {
            ReactDOM.render(<NewPasswordForm user_id={element.dataset.userId}/>, element)
        }
    }
}