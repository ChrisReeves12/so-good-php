import React from 'react';
import ReactDOM from 'react-dom';
import Input from '../../../admin/forms/Input';
import Util from '../../../core/Util';
import Popup from '../../../core/Popup';

export default class    SignInForm extends React.Component
{
    constructor()
    {
        super();
        this.state = {email: '', password: '', errors: {}};
    }

    handleFieldUpdate(field)
    {
        let state = Object.assign({}, this.state);
        state[field.name] = field.value;
        this.setState(state);
    }

    doSubmitForm(e)
    {
        e.preventDefault();
        let data = Object.assign({}, this.state);
        let popup = new Popup();

        $.ajax({
            url: this.props.sign_in_link || '',
            method: 'POST',
            dataType: 'json',
            data: {email: data.email, password: data.password, _token: Util.get_auth_token()},
            complete: (res) => {
                if(res.status == 200)
                {
                    if(!res.responseJSON.errors || (Array.isArray(res.responseJSON.errors) && res.responseJSON.errors.length == 0))
                        window.location = res.responseJSON.whence ? res.responseJSON.whence : '/';
                    else
                        this.setState({errors: res.responseJSON.errors});
                }
                else if(res.status == 0)
                {
                    popup.show('The operation timed out after attempting to sign in, please try again.');
                }
            }
        });
    }

    render()
    {
        return(
            <form onSubmit={this.doSubmitForm.bind(this)} className="sign-in-form">
                <Input value={this.state.email} updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors} name="email" type="text" label="Email Address"/>
                <Input value={this.state.password} updateValueHandler={this.handleFieldUpdate.bind(this)} errors={this.state.errors} name="password" type="password" label="Password"/>
                <p><a href="/forgot-password">Forgot Password</a></p>
                <button type="submit" className="btn btn-success"><i className="fa fa-lock"/> Sign In</button>
            </form>
        );
    }

    static initialize()
    {
        let element = document.getElementById('sign_in_form');
        if(element)
            ReactDOM.render(<SignInForm/>, element);
    }
}