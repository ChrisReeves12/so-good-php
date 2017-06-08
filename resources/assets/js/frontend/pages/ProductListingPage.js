import React from 'react';
import ReactDOM from 'react-dom';
import ProductListingPageContent from '../components/product_listing_page_content/ProductListingPageContent';

export default class ProductListingPage extends React.Component
{
    static initialize()
    {
        let element = document.getElementById('product_listing_content');
        if(element)
            ReactDOM.render(<ProductListingPageContent initial_data={JSON.parse(element.dataset.initialData)}/>, element);
    }
}