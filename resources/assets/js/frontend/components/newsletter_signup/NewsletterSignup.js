/**
 * Class definition of NewsletterSignup
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';
import Util from '../../../core/Util';
import Popup from '../../../core/Popup';

export default class NewsletterSignup extends React.Component {
    constructor(props) {
        super(props);
        this.state = {email: ''};
    }

    doSubmit(e)
    {
        e.preventDefault();
        $.ajax({
            url: '/subscription/add',
            dataType: 'json',
            method: 'POST',
            data: {_token: Util.get_auth_token(), email: this.state.email},
            complete: function(res) {
                if(res.status == 200)
                {
                    let popup = new Popup();
                    if(res.responseJSON.system_error)
                    {
                        popup.show(res.responseJSON.system_error);
                    }
                    else
                    {
                        popup.show('Thank you for subscribing to our newsletter!');
                    }
                }
            }
        });
    }

    handleUpdateEmail(e)
    {
        this.setState({email: e.target.value});
    }

    render() {
        return(
            <div className="newsletter-section">
                <form onSubmit={this.doSubmit.bind(this)} className="newsletter-form">
                    <div className="row hidden-lg-up">
                        <div className="col-xs-6 col-md-5">
                            <p className="notice">Sign Up for email & <span>Get 5% Off!</span></p>
                        </div>
                        <div className="form-group submit-button-group col-xs-6 col-md-7">
                            <input type="text" value={this.state.email} onChange={this.handleUpdateEmail.bind(this)} className="form-control" placeholder="Email Address"/>
                            <button type="submit" className="btn btn-success"><i className="fa fa-envelope-o"/> Join</button>
                        </div>
                    </div>
                    <p className="notice hidden-md-down">Sign Up for email & <br/><span>Get 5% Off!</span></p>
                    <div className="form-group submit-button-group hidden-md-down">
                        <input type="text" value={this.state.email} onChange={this.handleUpdateEmail.bind(this)} className="form-control" placeholder="Email Address"/>
                        <button type="submit" className="btn btn-success"><i className="fa fa-envelope-o"/> Join</button>
                    </div>
                </form>
            </div>
        );
    }

    static initialize()
    {
        let elements = document.querySelectorAll('.newsletter_signup');

        if(elements.length > 0)
        {
            for(let x = 0; x < elements.length; x++)
            {
                let element = elements[x];
                ReactDOM.render(<NewsletterSignup/>, element);
            }
        }
    }
}