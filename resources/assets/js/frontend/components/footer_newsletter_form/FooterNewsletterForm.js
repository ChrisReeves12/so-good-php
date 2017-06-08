/**
 * FooterNewsletterForm.js
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

import React from 'react';
import ReactDOM from 'react-dom';
import NewsletterSignup from '../newsletter_signup/NewsletterSignup';

export default class FooterNewsletterForm extends NewsletterSignup
{
    render()
    {
        return(
            <div className="newsletter-input">
                <form onSubmit={this.doSubmit.bind(this)}>
                    <input onChange={this.handleUpdateEmail.bind(this)} value={this.state.email} type="text"/>
                    <button type="submit"><i className="fa fa-user-plus"/><span className="hidden-md-down"> Join</span></button>
                </form>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('footer_newsletter_form');
        if(element)
        {
            ReactDOM.render(<FooterNewsletterForm/>, element);
        }
    }
}
