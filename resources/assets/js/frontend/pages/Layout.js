import React from 'react';
import ReactDOM from 'react-dom';
import HeaderCartDisplay from '../components/header_cart_display/HeaderCartDisplay';
import ModalAddToCart from '../components/modal_add_to_cart/ModalAddToCart';
import HeaderSearchField from '../components/header_search_field/HeaderSearchField';
import HeaderNavMenuInitializer from '../components/header_nav_menu/HeaderNavMenu';
import '../components/collapse_menu/CollapseMenu';

export default class Layout
{
    static initialize()
    {
        let element;

        let elements = document.getElementsByClassName('shopping_cart_display');
        if(elements.length > 0)
        {
            for(let x = 0; x < elements.length; x++)
            {
                let element = elements[x];
                ReactDOM.render(<HeaderCartDisplay initial_data={JSON.parse(element.dataset.initialData)}/>, element);
            }
        }

        element = document.getElementById('modal_add_to_cart');
        if(element)
            ReactDOM.render(<ModalAddToCart/>, element);

        element = document.getElementById('header_search_field');
        if(element)
            ReactDOM.render(<HeaderSearchField/>, element);

        HeaderNavMenuInitializer.initialize();
    }
}