import React from 'react';
import ReactDOM from 'react-dom';
import NewPasswordForm from '../components/new_password_form/NewPasswordForm';

export default class NewPasswordPage
{
    static initialize()
    {
        let element = document.getElementById('new_password_form');
        if(element)
            ReactDOM.render(<NewPasswordForm user_id={element.dataset.userId}/>, element);
    }
}