/**
 * Class definition of ContactUsForm
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import Util from '../../../../../../app/assets/javascript/core/Util';
import Popup from '../../../../../../app/assets/javascript/core/Popup';

export default class ContactUsForm extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            name: '',
            email: '',
            message: ''
        };

        this.popup = new Popup();
    }

    doSubmit(e)
    {
        e.preventDefault();
        $.ajax({
            url: '/contact-us',
            method: 'POST',
            dataType: 'json',
            data: {authenticity_token: Util.get_auth_token(), ...this.state},
            complete: (res) => {
                if(res.status == 200)
                {
                    if(res.responseJSON.errors)
                    {
                        this.popup.show(res.responseJSON.errors);
                    }
                    else
                    {
                        this.popup.show('Your message has been sent.<br/>We will respond to your inquiry as soon as possible, thank you.');
                    }
                }
            }
        });
    }

    render() {
        return (
            <form onSubmit={this.doSubmit.bind(this)}>
                <div className="form-group">
                    <label>Name</label>
                    <input value={this.state.name} onChange={(e => { e.preventDefault(); this.setState({name: e.target.value}); })} type="text" className="form-control" placeholder="Name"/>
                </div>
                <div className="form-group">
                    <label>Email</label>
                    <input value={this.state.email} onChange={(e => { e.preventDefault(); this.setState({email: e.target.value}); })} type="text" className="form-control" placeholder="Email"/>
                </div>
                <div className="form-group">
                    <label>Message</label>
                    <textarea value={this.state.message} onChange={(e => { e.preventDefault(); this.setState({message: e.target.value}); })} className="form-control" placeholder="Message"/>
                </div>
                <div className="form-group">
                    <button type="submit" className="btn btn-success">SUBMIT</button>
                </div>
            </form>
        );
    }
}