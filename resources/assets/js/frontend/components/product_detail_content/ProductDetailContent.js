import React from 'react';
import ReactDOM from 'react-dom';
import Lightbox from 'react-image-lightbox';
import AdditionalPhotos from './additional_photos/AdditionalPhotos';
import ProductOptions from './product_options/ProductOptions';
import ProductInformation from './product_information/ProductInformation';
import Util from '../../../core/Util';
import RecommendedProducts from './recommended_products/RecommendedProducts';
import Popup from '../../../core/Popup';
const numeral = require('numeral');

export default class ProductDetailContent extends React.Component {

    constructor(props)
    {
        super(props);

        this.stock_status_map = {
            'in_stock': 'In Stock',
            'out_of_stock': 'Out Of Stock'
        };

        let state = window.sogood.reactjs.product_page_data;
        state.selected_options = {};
        state.loading = false;

        state.selected_item_id = Util.objectIsEmpty(state.product_options) ? state.product.default_item_id : null;
        state.errors = {};

        // Set default display values
        state.display_sku = state.default_sku;
        state.quantity = 1;
        state.original_main_image = Array.isArray(state.product.images) ? state.product.images.find(img => img.is_main) : null;
        state.lightbox_active = false;
        state.lightbox_image = '';
        state.active_item_photo = null;
        state.product_unavailable = false;

        Object.keys(state.product_options).map(option_name => {
            state.selected_options[option_name.toLowerCase()] = '';
        });

        // Set up product information tabs
        state.information_tabs = [];
        if(state.product.description !== '' || state.product.specs.length > 0)
        {
            if(state.product.description !== '')
                state.information_tabs.push({name: 'Description', content: state.product.description, render_method: 'text'});

            if(Array.isArray(state.product.specs) && state.product.specs.length > 0)
                state.information_tabs.push({name: 'Specifications', content: state.product.specs, render_method: 'list'});

            // The first tab will be default to active
            let idx = -1;
            state.information_tabs = state.information_tabs.map(tab => { idx++; tab.is_active = (idx == 0); return tab; });
        }

        this.state = state;
    }

    doSelectPhoto(index)
    {
        let product = Object.assign({}, this.state.product);
        product.images = product.images.map(img => {
            img.is_main = false;
            return img;
        });

        product.images[index].is_main = true;
        this.setState({product, active_item_photo: null});
    }

    doAddToCart(e)
    {
        e.preventDefault();
        let popup = new Popup();
        let state = Object.assign({}, this.state);
        state.errors = {};

        // Check form errors
        for(let option_name in state.product_options)
        {
            if(!state.product_options.hasOwnProperty(option_name))
                continue;

            let value = state.selected_options[option_name.toLowerCase()];
            if(value === '' || value === null)
                state.errors[option_name.toLowerCase()] = "Please select a value.";
        }

        // Check for valid item ID
        if(Util.objectIsEmpty(state.errors) && state.loading == false)
        {
            state.loading = true;
            this.setState(state);

            $.ajax({
                url: '/shopping-cart/add',
                method: 'POST',
                dataType: 'json',
                timeout: 7000,
                data: {
                    option_values: state.selected_options,
                    product_id: state.product.id,
                    _token: Util.get_auth_token(),
                    quantity: state.quantity
                },
                complete: (res) => {
                    if(res.status == 200) {
                        if(res.responseJSON.system_error) {
                            let popup = new Popup();
                            popup.show(res.responseJSON.system_error);
                        }
                        else {
                            // Send to google Analytics
                            if(Util.env() === 'production') {
                                dataLayer.push({
                                    'event': 'addToCart',
                                    'ecommerce': {
                                        'currencyCode': 'USD',
                                        'add': {
                                            'products': [{
                                                'name': state.product.name,
                                                'id': state.display_sku,
                                                'price': parseFloat(state.price_display.replace("\$", '')),
                                                'quantity': state.quantity,
                                                'brand': state.brand_name
                                            }]
                                        }
                                    }
                                });
                            }

                            // If we are on a small mobile device, do not show modal
                            let show_modal = ($(window).width() >= 768);
                            if(show_modal) {
                                $(document).trigger('cart-update');
                                let modal = $('#modal_add_to_cart');
                                modal.html(res.responseJSON.output);
                                modal.modal();
                            }
                            else {
                                // Go straight to cart
                                window.location = '/checkout';
                            }
                        }
                    }
                    else if(res.status == 0) {
                        popup.show('Operation timed out, please try adding the item to your cart again.');
                    }

                    this.setState({loading: false});
                }
            });
        }

        this.setState(state);
    }

    getMainPhoto()
    {
        let image = null;

        try {
            if(Array.isArray(this.state.product.images)) {
                image = this.state.product.images.find(img => img.is_main);
                if(!image)
                    image = this.state.product.images[0];
            }
            else {
                image = {url: this.state.product.default_image_url};
            }

            if(this.state.active_item_photo)
                image = this.state.active_item_photo;
        }
        catch(err)
        {
            window.console.error(err);
        }

        return image;
    }

    handleOptionUpdate(option_name, option_value)
    {
        try
        {
            let selected_options = Object.assign({}, this.state.selected_options);
            selected_options[option_name.toLowerCase()] = option_value.toLowerCase();
            this.setState({selected_options});

            // Check if all options have been given a value, and if so, find sku
            let do_get_new_sku = true;

            for(let option_name in selected_options)
            {
                if(selected_options.hasOwnProperty(option_name))
                {
                    if(selected_options[option_name] === '')
                        do_get_new_sku = false;
                }
            }

            if(do_get_new_sku)
            {
                $.get('/product-page/ajax/product-data', {option_values: selected_options, product_id: this.state.product.id}, 'json')
                    .then(res => {

                        let product = Object.assign({}, this.state.product);
                        product.quantity = res.item.quantity;
                        product.stock_status = res.item.stock_status;

                        this.setState({
                            product,
                            product_unavailable: Number.isNaN(res.item.id),
                            quantity: 1,
                            selected_item_id: res.item.id,
                            list_price_display: res.item.list_price ? numeral(res.item.list_price).format('$0,0.00') : null,
                            price_display: (res.item.store_price) ? numeral(res.item.store_price).format('$0,0.00') : null,
                            display_sku: res.item.sku,
                            active_item_photo: (res.item.image && res.item.image.url !== null) ? res.item.image : null
                        });
                    });
            }
        }
        catch(err)
        {
            window.console.error(err);
        }
    }

    changeProductInfoTabs(idx)
    {
        let information_tabs = this.state.information_tabs.slice();
        information_tabs = information_tabs.map(tab => {
            tab.is_active = false;
            return tab;
        });

        information_tabs[idx].is_active = true;
        this.setState({information_tabs});
    }

    activateLightbox(e)
    {
        e.preventDefault();
        if($(window).width() >= 576)
            this.setState({lightbox_active: true, lightbox_image: e.target.src});
    }

    render() {
        let main_photo = this.getMainPhoto();

        let qty_options = null;
        if(this.state.product.quantity > 0)
        {
            let qty = parseInt(this.state.product.quantity);

            qty = (qty > 10) ? 10 : qty;
            qty_options = Util.fillArray(qty).map(i => {
                return(<option key={i} value={i+1}>{i+1}</option>);
            });
        }

        let cache_prefix = '', additional_img_cache_prefix = '';
        if(Util.get_use_cache() == 'true')
        {
            cache_prefix = '/imagecache/330x460-0';
            additional_img_cache_prefix = '/imagecache/80x80-0';
        }

        return (
            <div>
                <div className='row'>
                    <div className='col-sm-5'>
                        <div className='main-photo-section'>
                            {main_photo && <a onClick={this.activateLightbox.bind(this)} href={main_photo.url}>
                                <img src={cache_prefix + main_photo.url}/>
                            </a>}
                        </div>
                    </div>
                    <div className='col-sm-7'>
                        <h1 className="product-name">{this.state.product.name}</h1>
                        <h4 className="brand-name">{this.state.brand_name}</h4>

                        {!this.state.product_unavailable && <div className='price-section'>
                            {this.state.list_price_display &&
                                <p>Market Price: <span className='list-price'>{this.state.list_price_display}</span></p>}
                            <p>{this.state.list_price_display ? 'Our ' : ''}Price: <span className={this.state.list_price_display ? 'store-price' : ''}>{this.state.price_display}</span></p>
                        </div>}

                        {!this.state.product_unavailable && <div className="sku-section">
                            <p className="sku">Sku: {this.state.display_sku}</p>
                        </div>}
                        {this.state.product.stock_status && <div className="stock-status-section">
                            <p className="stock-status">Stock Status: {this.stock_status_map[this.state.product.stock_status]}</p>
                        </div>}

                        {!Util.objectIsEmpty(this.state.product_options) &&
                        <ProductOptions
                            optionChangeHandler={this.handleOptionUpdate.bind(this)}
                            selected_options={this.state.selected_options}
                            errors={this.state.errors}
                            product_options={this.state.product_options}/>}

                        {qty_options && <div className='quantity-section'>
                            <label>Quantity:</label>
                            <select onChange={((e) => { this.setState({quantity: e.target.value}); })}
                                    value={this.state.quantity} className="qty-select">
                                {qty_options}
                            </select>
                        </div>}

                        {(!this.state.product_unavailable && this.state.product.stock_status == 'in_stock') &&
                            <a href='' onClick={this.doAddToCart.bind(this)} className='add-to-cart'>
                                {this.state.loading ? "Please wait..." : "Add To Cart"}</a>}
                    </div>
                </div>
                <div className='row'>
                    <div className='col-sm-5'>
                        {(Array.isArray(this.state.product.images) && this.state.product.images.length > 0) &&
                        <AdditionalPhotos cache_prefix={additional_img_cache_prefix} selectPhotoHandler={this.doSelectPhoto.bind(this)}
                                          product={this.state.product}/>}
                    </div>
                </div>
                {this.state.information_tabs.length > 0 &&
                <div className='row'>
                    <div className='col-sm-12 product-description-section'>
                        <ProductInformation tabChangeHandler={this.changeProductInfoTabs.bind(this)} tabs={this.state.information_tabs}/>
                    </div>
                </div>}
                {Array.isArray(this.state.recommended_products) && this.state.recommended_products.length > 0 ? <div className='row'>
                    <RecommendedProducts products={this.state.recommended_products}/>
                </div> : null}
                {this.state.lightbox_active &&
                    <Lightbox
                        mainSrc={this.state.lightbox_image}
                        onCloseRequest={() => { this.setState({lightbox_active: false, lightbox_image: ''}); }}
                    />}
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('product_detail_content');
        if(element)
        {
            ReactDOM.render(<ProductDetailContent/>, element);
        }
    }
}
