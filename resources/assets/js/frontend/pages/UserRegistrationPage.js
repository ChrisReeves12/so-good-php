import React from 'react';
import ReactDOM from 'react-dom';
import UserRegistrationForm from '../components/user_registration_form/UserRegistrationForm';

export default class UserRegistrationPage
{
    static initialize()
    {
        let element = document.getElementById('user_registration_form');
        if(element)
            ReactDOM.render(<UserRegistrationForm/>, element);
    }
}