import React from 'react';
import ReactDOM from 'react-dom';
import PasswordRecoveryForm from '../components/password_recovery_form/PasswordRecoveryForm';

export default class PasswordRecoveryPage
{
    static initialize()
    {
        let element = document.getElementById('password_recovery_form');

        if(element)
            ReactDOM.render(<PasswordRecoveryForm/>, element);
    }
}