import React from 'react';
import ReactDOM from 'react-dom';
import ContactUsForm from '../components/contact_us_form/ContactUsForm';

export default class AboutUsPage
{
    static initialize()
    {
        let element = document.getElementById('contact_page_contact_form');
        if(element)
            ReactDOM.render(<ContactUsForm/>, element);
    }
}