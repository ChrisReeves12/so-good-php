import React from 'react';
import ReactDOM from 'react-dom';
import SignInForm from '../components/sign_in_form/SignInForm';

export default class SignIn extends React.Component
{
    static initialize()
    {
        let element = document.getElementById('sign_in_form');
        if(element)
            ReactDOM.render(<SignInForm/>, element);
    }
}
