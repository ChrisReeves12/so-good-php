import React from 'react';
import ReactDOM from 'react-dom';
import ProductDetailContent from '../components/product_detail_content/ProductDetailContent';

export default class ProductDetailPage
{
    static initialize()
    {
        let element = document.getElementById('product_detail_content');
        if (element)
        {
            let product_data = JSON.parse(element.dataset.initialData);

            if(Util.env() === 'production')
            {
                // Send data to Google analytics
                dataLayer.push({
                    'event': 'gtm.product_detail_load',
                    'ecommerce': {
                        'detail': {
                            'products': [{
                                'name': product_data.product.name,
                                'id': product_data.default_sku,
                                'brand': product_data.brand_name
                            }]
                        }
                    }
                });
            }

            ReactDOM.render(<ProductDetailContent initial_data={product_data}/>, element);
        }
    }
}
