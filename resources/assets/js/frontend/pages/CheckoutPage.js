import React from 'react';
import ReactDOM from 'react-dom';
import CheckoutForm from '../components/checkout_form/CheckoutForm';

export default class CheckoutPage extends React.Component
{
    static initialize()
    {
        let element = document.getElementById('checkout_form');
        if(element)
            ReactDOM.render(<CheckoutForm initial_data={JSON.parse(element.dataset.initialData)}/>, element);
    }
}