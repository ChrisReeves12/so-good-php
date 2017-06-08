/**
 * Form that is used to create and edit products
 * @author Christopher Reeves <ChrisReeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';

import MenuBox from '../../components/menu_box/MenuBox';
import KeyValBox from '../../components/key_val_box/KeyValBox';
import Input from '../../forms/Input';
import ItemDialog from './item_dialog/ItemDialog';
import ImageDialog from './image_dialog/ImageDialog';
import Util from '../../../core/Util';
import random_string from 'randomstring';
import Popup from '../../../core/Popup';

export default class ProductEditForm extends React.Component
{
    constructor(props)
    {
        super(props);
        this.popup = new Popup();
    }

    doSaveProduct(e)
    {
        e.preventDefault();
        let product_data = Object.assign({}, this.state.product_data);
        let errors = [];

        let form_data = {};

        // Collect all the general form elements
        let general_form_elements = $("#general").find('.form-input');
        for(let general_form_element of general_form_elements)
        {
            if(!$(general_form_element).attr('name'))
                continue;

            if($(general_form_element).is('input[type="checkbox"]'))
                form_data[$(general_form_element).attr('name')] = $(general_form_element).is(':checked');
            else
                form_data[$(general_form_element).attr('name')] = $(general_form_element).val();
        }

        // Collect all the content form elements
        let content_form_elements = $("#content").find('.form-input');
        for(let content_form_element of content_form_elements)
        {
            if(!$(content_form_element).attr('name'))
                continue;

            form_data[$(content_form_element).attr('name')] = $(content_form_element).val();
        }

        form_data.specs = JSON.parse(form_data.product_specs);
        form_data.categories = JSON.parse(form_data.menu_box_category);
        form_data.default_item_stock_data = product_data.default_stock_location_items;
        form_data._token = Util.get_auth_token();

        $.ajax({
            url: '/admin/record/Product/' + ((typeof product_data.id !== 'undefined' && product_data.id !== null) ? product_data.id : ''),
            method: (typeof product_data.id !== 'undefined' && product_data.id !== null) ? 'PUT' : 'POST',
            data: form_data,
            dataType: 'json',
            timeout: 4000,
            complete: (res) => {
                if(res.status === 200)
                {
                    if(res.responseJSON.errors)
                    {
                        errors = res.responseJSON.errors;
                        this.setState({errors})
                    }
                    else
                    {
                        window.location = '/admin/product/' + res.responseJSON.id;
                    }
                }
                else if(res.status === 0)
                {
                    // Todo: Handle timeout
                }
            }
        });
    }

    componentWillMount()
    {
        let counter = 0;
        let props = Object.assign({}, this.props);
        let product_data = Object.assign({}, this.props.product_data);
        product_data.slug = product_data.slug || '';

        if(Array.isArray(product_data.specs))
        {
            product_data.specs = product_data.specs.map((s) => {
                counter++;
                s.id = counter;
                return s;
            });
        }
        else
        {
            product_data.specs = [];
        }

        product_data.non_default_items = product_data.non_default_items.map((i) => {
            i.saved = true;
            if(Array.isArray(i.details))
            {
                i.details.map((d) => {

                    let id = null;
                    while(id === null)
                    {
                        let test_id = random_string.generate(100);
                        if(i.details.filter((i) => (i.id === test_id)).length === 0)
                            id = test_id;
                    }

                    d.id = id;
                    return d;
                });
            }
            else
            {
                i.details = [];
            }

            return i;
        });

        props.product_data = product_data;
        props.auto_gen_sku = (product_data.id === null);

        this.setState(props);
    }

    selectImage(image)
    {
        this.setState({selected_image: image});
    }

    _resetMainImage(main_image, callback)
    {
        if(this.state.product_data.view_images.length > 0)
        {
            if(main_image)
            {
                $.ajax({
                    url: '/admin/product/update-default-image/' + ((this.state.product_data.id) ? this.state.product_data.id : ''),
                    method: 'PUT',
                    dataType: 'json',
                    data: {_token: Util.get_auth_token(), main_image: main_image},
                    timeout: 3000,
                    complete: callback
                });
            }
        }
    }

    deleteProductImage(idx, image_to_remove)
    {
        let product_data = Object.assign({}, this.state.product_data);
        product_data.view_images.splice(idx, 1);

        // If there is no main photo, use the first one
        let new_main_image = null;
        if(product_data.view_images.filter((i) => i.is_main).length === 0 && product_data.view_images.length > 0)
        {
            product_data.view_images[0].is_main = true;
            new_main_image = product_data.view_images[0].file_name;
        }

        $.ajax({
            url: '/admin/product/delete-image/' + this.state.product_data.id,
            method: 'DELETE',
            data: {
                _token: Util.get_auth_token(),
                removed_image: (image_to_remove) ? image_to_remove.props.file_name : null,
                new_main_image: new_main_image
            },
            dataType: 'json',
            timeout: 4000,
            complete: (res) => {
                if(res.status === 200)
                {
                    this.setState({product_data: product_data});
                }
                else if(res.status === 0)
                {
                    // Todo: Handle timeout
                }
            }
        });
    }

    uploadImage(form_data, product_id)
    {
        let product_data = Object.assign({}, this.state.product_data);

        if(form_data)
        {
            $.ajax({
                url: '/admin/product/upload/' + product_id,
                method: 'POST',
                dataType: 'json',
                data: form_data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 4000,
                complete: (res) => {
                    if(res.status === 200)
                    {
                        if(res.responseJSON.errors)
                        {
                            this.popup.show(res.responseJSON.errors);
                        }
                        else
                        {
                            product_data.view_images = res.responseJSON.images;
                            this.setState({product_data});
                        }
                    }
                    else if(res.status === 0)
                    {
                        // Todo: Handle timeout
                    }
                }
            });
        }
    }

    renderImagesSection()
    {
        let ret_val = null;

        if(this.state.product_data.id)
        {
            ret_val = <ImageDialog uploadImageHandler={this.uploadImage.bind(this)}
                                   changeMainPhoto={this.changeMainPhoto.bind(this)}
                                   product_id={this.state.product_data.id}
                                   selected_image={this.state.selected_image}
                                   selectImage={this.selectImage.bind(this)}
                                   deleteProductImage={this.deleteProductImage.bind(this)}
                                   images={this.state.product_data.view_images}/>

        }
        else
        {
            ret_val = (
                <p><i className="fa fa-info-circle"/> Please save your product first, then you can add images.</p>
            );
        }

        return ret_val;
    }

    updateSelectList(get_url, name)
    {
        $.ajax({
            url: get_url,
            data: {_token: Util.get_auth_token()},
            dataType: 'json',
            timeout: 3000,
            complete: (res) => {
                if(res.status == 200)
                {
                    if(res.responseJSON.system_error)
                    {
                        this.popup.show(res.responseJSON.system_error);
                    }
                    else
                    {
                        this.state[name] = res.responseJSON.records;
                        this.setState(this.state);
                    }
                }
                else if(res.status == 0)
                {
                    // Todo: handle timeout
                }
            }
        });
    }

    saveItem(item_properties, is_updating, item_element, idx)
    {
        let product_data = Object.assign({}, this.state.product_data);
        let errors = null;

        $.ajax({
            url: '/admin/record/Item/' + (is_updating ? item_properties.id : ''),
            method: (is_updating) ? 'PUT' : 'POST',
            dataType: 'json',
            timeout: 4000,
            data: {_token: Util.get_auth_token(), data: item_properties},
            complete: (res) => {
                if(res.status == 200)
                {
                    // Handle errors
                    if(res.responseJSON.errors)
                    {
                        errors = {item: item_element, errors: res.responseJSON.errors};
                    }
                    else if(res.responseJSON.system_error)
                    {
                        this.popup.show(res.responseJSON.system_error);
                    }
                    else
                    {
                        // Update the state with new data
                        item_properties.image = this.state.product_data.non_default_items[idx].image;
                        product_data.non_default_items[idx] = item_properties;
                        product_data.non_default_items[idx].saved = true;
                        product_data.non_default_items[idx].id = res.responseJSON.id;

                        this.popup.show("Item(s) saved successfully.");
                    }

                    this.setState({product_data, item_errors: errors});
                }
                else if(res.status == 0)
                {
                    // Todo: Handle timeout
                }
            }
        });
    }

    deleteItem(idx, item)
    {
        let can_delete = true;
        if(item.props.item_data.saved)
        {
            can_delete = (confirm('Delete item ' + item.props.item_data.id + '?'));
        }

        if(can_delete)
        {
            let product_data = Object.assign({}, this.state.product_data);

            if(item.props.item_data.saved)
            {
                $.ajax({
                    url: '/admin/record/Item/' + product_data.non_default_items[idx].id,
                    method: 'DELETE',
                    dataType: 'json',
                    timeout: 4000,
                    data: {_token: Util.get_auth_token()},
                    complete: (res) => {
                        if(res.status === 200)
                        {
                            if(!res.responseJSON.system_error)
                            {
                                product_data.non_default_items.splice(idx, 1);
                                this.setState({product_data: product_data});
                            }
                            else
                            {
                                this.popup.show(res.responseJSON.system_error);
                            }
                        }
                        else if(res.status == 0)
                        {
                            // Todo: Handle timeout
                        }
                    }
                });
            }
            else
            {
                product_data.non_default_items.splice(idx, 1);
                this.setState({product_data: product_data});
            }
        }
    }

    _generateItemId()
    {
        let id = null;
        while(id == null)
        {
            let test_id = random_string.generate(100);
            if(this.state.product_data.non_default_items.filter((i) => (i.id == test_id)).length == 0)
                id = test_id;
        }

        return id;
    }

    addItem(e)
    {
        e.preventDefault();
        let product_data = Object.assign({}, this.state.product_data);

        $.ajax({
            url: '/admin/product/item/generate-item/' + product_data.id,
            method: 'POST',
            dataType: 'json',
            data: {_token: Util.get_auth_token()},
            complete: (res) => {
                if(res.status === 200)
                {
                    if(!res.responseJSON.system_error)
                    {
                        let empty_item = {
                            id: this._generateItemId(),
                            saved: false,
                            stock_locations: product_data.default_stock_location_items,
                            sku: res.responseJSON.sku,
                            product_id: product_data.id,
                            is_inactive: true,
                            list_price: product_data.list_price,
                            store_price: product_data.store_price,
                            weight: product_data.weight,
                            calculated_stock_status: 'in_stock',
                            stock_status_override: 'none',
                            upc: '',
                            isbn: '',
                            ean: '',
                            image: false,
                            details: []
                        };

                        product_data.non_default_items.push(empty_item);
                        this.setState({product_data: product_data});
                    }
                    else
                    {
                        this.popup.show(res.responseJSON.system_error);
                    }
                }
                else if(res.status === 0)
                {
                    this.popup.show('Operation timed out while trying to generate sku number.');
                }
            }
        });
    }

    addAttribute(item)
    {
        let product_data = Object.assign({}, this.state.product_data);

        product_data.non_default_items.map((i) => {
            if(i.id == item.props.item_data.id)
            {
                let id = null;
                while(id == null)
                {
                    let test_id = random_string.generate(100);
                    if(product_data.non_default_items.filter((i) => (i.id == test_id)).length == 0)
                        id = test_id;
                }

                i.details.push({value: '', key: '', id: id});
            }

                return i;
        });


        this.setState({product_data: product_data});
    }

    removeItemAttribute(idx, item_idx)
    {
        let product_data = Object.assign({}, this.state.product_data);
        product_data.non_default_items[item_idx].details.splice(idx, 1);

        this.setState({product_data});
    }

    uploadItemImage(idx, files, is_saved)
    {
        let product_data = Object.assign({}, this.state.product_data);
        let form_data = new FormData();

        if(is_saved)
        {
            if(files)
            {
                if(files.length > 0)
                {
                    form_data.append('_token', Util.get_auth_token());
                    form_data.append('file', files[0]);

                    $.ajax({
                        url: '/admin/product/item/image/' + product_data.non_default_items[idx].id,
                        method: 'POST',
                        dataType: 'json',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        cache: false,
                        timeout: 4000,
                        complete: (res) => {
                            if(res.status == 200)
                            {
                                product_data.non_default_items[idx].view_image = {url: res.responseJSON.url};
                                this.setState({product_data});
                            }
                            else if(res.status == 0)
                            {
                                // Todo: Handle timeout
                            }
                        }
                    });
                }
            }
        }
        else
        {
            this.popup.show('Please save the item first, then you can upload a photo for it.');
        }

        this.setState({product_data});
    }

    deleteItemImage(idx)
    {
        let product_data = Object.assign({}, this.state.product_data);

        if(product_data.non_default_items[idx].view_image)
        {
            $.ajax({
                url: '/admin/product/item/image/' + product_data.non_default_items[idx].id,
                dataType: 'json',
                method: 'DELETE',
                timeout: 3000,
                data: {_token: Util.get_auth_token()},
                complete: (res) => {
                    if(res.status == 200)
                    {
                        product_data.non_default_items[idx].view_image = false;
                        this.setState({product_data});
                    }
                    else if(res.status == 0)
                    {
                        // Todo: Handle timeout
                    }
                }
            });
        }
    }

    createDuplicateItem(idx)
    {
        let model_object = Object.assign({}, this.state.product_data.non_default_items[idx]);

        model_object.id = this._generateItemId();
        model_object.image = null;
        model_object.saved = false;

        let product_data = Object.assign({}, this.state.product_data);
        product_data.non_default_items.push(model_object);
        this.setState({product_data});
    }

    renderItemSection()
    {
        let ret_val = null;

        if(this.state.product_data.id)
        {
            // If there are no items, then hide item dialog sections
            let dialog_display = null;
            let idx = -1;
            if(Array.isArray(this.state.product_data.non_default_items) && this.state.product_data.non_default_items.length > 0)
            {
                dialog_display = this.state.product_data.non_default_items.map((i) => {
                    idx++;
                    return(<ItemDialog idx={idx}
                                       deleteItemImage={this.deleteItemImage.bind(this)}
                                       uploadItemImage={this.uploadItemImage.bind(this)}
                                       removeItemAttribute={this.removeItemAttribute.bind(this)}
                                       addAttribute={this.addAttribute.bind(this)}
                                       deleteHandler={this.deleteItem.bind(this)}
                                       saveHandler={this.saveItem.bind(this)}
                                       createDuplicateHandler={this.createDuplicateItem.bind(this)}
                                       errors={this.state.item_errors} key={i.id}
                                       product_id={this.state.product_data.id}
                                       item_data={i}/>);
                });
            }
            else
            {
                dialog_display = (
                    <div className="row">
                        <div className="col-sm-4 col-sm-offset-3">
                            <i className="fa fa-info-circle"/> There are currently no items associated with this product.
                        </div>
                    </div>
                );
            }

            // Todo: Not implemented yet
            let save_all_button = (this.state.product_data.non_default_items.length > 0) ?
                <button className="btn btn-success"><i className="fa fa-save"/> Save All Items</button> : null;

            ret_val = (
                <div>
                    <div style={{marginBottom: '20px'}} className="row">
                        <div className="col-sm-2">
                            <button onClick={this.addItem.bind(this)} className="btn btn-secondary"><i className="fa fa-plus-circle"/> Add Item</button>
                        </div>
                    </div>
                    <div className="row">{dialog_display}</div>
                </div>
            );
        }
        else
        {
            ret_val = (
                <div class="row">
                    <div class="col-sm-4 col-sm-offset-3">
                        <i class="fa fa-info-circle"/> Please save your product first, then you can add items.
                    </div>
                </div>
            );
        }

        return ret_val;
    }

    changeMainPhoto(is_checked)
    {
        if(this.state.selected_image)
        {
            // Set the selected image as the main photo
            if(is_checked)
            {
                this.state.product_data.view_images = this.state.product_data.view_images.map((i) => {
                    if(this.state.selected_image.props.file_name == i.file_name)
                        i.is_main = is_checked;
                    else
                        i.is_main = false;

                    return i;
                });
            }
            else
            {
                this.state.product_data.view_images[0].is_main = true;
            }

            let main_image = this.state.product_data.view_images.find((i) => i.is_main);

            this._resetMainImage(main_image, res => {
                if(res.status == 200)
                {
                    // Update state
                    this.setState(this.state);
                }
                else if(res.status == 0)
                {
                    // Todo: Handle timeout
                }
            });
        }
    }


    renderContentSection()
    {
        return(
            <div>
                <Input type="textarea" name="description" label="Product Description" value={this.state.product_data.description}/>
                <div className="form-group">
                    <label>Product Specification</label>
                    <KeyValBox name="product_specs" values={this.state.product_data.specs}/>
                </div>
                <Input type="textarea" name="tags" label="Tags" value={this.state.product_data.tags}/>
            </div>
        );
    }

    addCategory(e)
    {
        e.preventDefault();
        let category_id = parseInt($('.product-category-section select').val());
        if(Number.isInteger(category_id))
        {
            let category = this.state.categories.find((cat) => cat.id == category_id);
            if(category)
            {
                // Check if category is already added
                if(this.state.product_data.view_categories.filter((cat) => cat.id == category_id).length == 0)
                {
                    // Add category
                    this.state.product_data.view_categories.push(category);
                    this.setState(this.state);
                }
            }
        }
    }

    removeCategory(idx)
    {
        let product_data = Object.assign({}, this.state.product_data);
        product_data.view_categories.splice(idx, 1);
        this.setState({product_data: product_data});
    }

    doDeleteProduct(e)
    {
        e.preventDefault();
        if(confirm('Delete product?'))
        {
            $.ajax({
                url: '/admin/record/Product/' + this.state.product_data.id,
                method: 'DELETE',
                data: {_token: Util.get_auth_token()},
                timeout: 3000,
                dataType: 'json',
                complete: (res) => {
                    if(res.status == 200)
                    {
                        window.location = '/admin/list/product';
                    }
                    else if(res.status == 0)
                    {
                        // Todo: handle timeout
                    }
                }
            });
        }
    }

    handleAutoGenChange(e)
    {
        this.setState({auto_gen_sku: $(e.target).is(':checked')});
    }

    updateNameAndSlug(target)
    {
        let product_data = Object.assign({}, this.state.product_data);
        product_data.name = target.value;
        product_data.slug = ProductEditForm._translateToSlugURL(target.value);
        this.setState({product_data: product_data});
    }

    updateProductStockInventory(e)
    {
        let target = e.target;
        let idx = parseInt(e.target.dataset.idx);
        let product_data = Object.assign({}, this.state.product_data);

        // Update if location is active
        if(target.name === 'is_active')
        {
            product_data.default_stock_location_items[idx].active = $(target).is(':checked');
        }
        else if(target.name === 'qty')
        {
            product_data.default_stock_location_items[idx].qty = target.value;
        }

        this.setState({product_data});
    }

    static _translateToSlugURL(text)
    {
        let result = text.toLowerCase();
        result = result
            .replace(new RegExp("[\\\"\\\']+", 'g'), '')
            .replace(new RegExp("\\&+", 'g'), 'and')
            .replace(new RegExp("\\%+", 'g'), '-percent')
            .replace(new RegExp("[\\@#\\$\\^\\*\\(\\)\\+\\=\\[\\]\\{\\}\\|\\\\\\'\\\"\\:\\;\\<\\>\\,\\.\\?\\`\\~]+", 'g'), '')
            .replace(new RegExp("[\\s\\-\\_\\\/]+", 'g'), '-')
            .replace(new RegExp("\\-+$"), '')
            .replace(new RegExp("^[\\-\\s]"), '');

        return result;
    }

    render()
    {
        // Render delete button
        let delete_product_button = null;
        if(this.state.product_data.id)
            delete_product_button = (<button onClick={this.doDeleteProduct.bind(this)} type="button" className="btn btn-danger"><i className="fa fa-times"/> Delete Product</button>);

        let has_extra_items = (Array.isArray(this.state.product_data.non_default_items) && this.state.product_data.non_default_items.length > 0);

        // Show inventory entries
        let default_inventory_entries = null;
        if(Array.isArray(this.state.product_data.default_stock_location_items) &&
            this.state.product_data.default_stock_location_items.length > 0)
        {
            let idx = -1;
            default_inventory_entries = this.state.product_data.default_stock_location_items.map((dsli) => {
                idx++;
                return(
                    <div key={idx} className="row">
                        <div className="col-xs-6" style={{paddingRight: 0}}>
                            <input name="is_active" onChange={this.updateProductStockInventory.bind(this)} data-idx={idx}
                                   data-location-id={dsli.id} checked={dsli.active} type="checkbox"/>
                            <input name="stock_location_name" data-idx={idx} data-location-id={dsli.id} disabled="true"
                                   value={dsli.name} className="form-control stock-name-field" type="text"/>
                        </div>
                        <div className="col-xs-6" style={{paddingLeft: 0}}>
                            <input name="qty" onChange={this.updateProductStockInventory.bind(this)}
                                   data-idx={idx} data-location-id={dsli.id} value={dsli.qty} className="form-control" type="text"/>
                        </div>
                    </div>
                );
            });
        }

        return(
            <div>
                <div style={{marginBottom: '32px'}}>
                    <a href="/admin/product" className="btn btn-secondary"><i className="fa fa-pencil-square"/> Create New</a>
                    <a href="/admin/list/product" className="btn btn-secondary"><i className="fa fa-list"/> List All Products</a>
                </div>
                <form onSubmit={this.doSaveProduct.bind(this)} method="post" action="" encType="multipart/form-data">
                    <ul className="nav nav-pills">
                        <li className="nav-item"><a className="nav-link active" data-toggle="tab" href="#general"><i className="fa fa-info-circle"/> General Information</a></li>
                        <li className="nav-item"><a className="nav-link" data-toggle="tab" href="#content"><i className="fa fa-file-text"/> Content & Specs</a></li>
                        <li className="nav-item"><a className="nav-link" data-toggle="tab" href="#images"><i className="fa fa-picture-o"/> Images</a></li>
                        <li className="nav-item"><a className="nav-link" data-toggle="tab" href="#items"><i className="fa fa-list"/> Items</a></li>
                    </ul>
                    <div className="tab-content">
                        <div id="general" className="tab-pane fade in active">
                            <div className="form-group">
                                <div className="row">
                                    <div className="col-sm-6">
                                        <Input updateValueHandler={this.updateNameAndSlug.bind(this)} name="name" errors={this.state.errors} value={this.state.product_data.name} label="Name" type="text" is_required="true"/>
                                        <Input read_only={this.state.auto_gen_sku} name="slug" errors={this.state.errors} value={this.state.product_data.slug} label="Slug" type="text" is_required="true"/>
                                        <div style={{marginBottom: 14, marginTop: -8}}>
                                            <input onChange={this.handleAutoGenChange.bind(this)}
                                                   checked={this.state.auto_gen_sku} type="checkbox"
                                                   name="generate-slug"/> <label>Auto-generate Slug</label>
                                        </div>
                                        <Input name="list_price" errors={this.state.errors} value={this.state.product_data.list_price} label="List Price" type="text"/>
                                        <Input name="cost" errors={this.state.errors} value={this.state.product_data.cost} label="Cost" type="text"/>
                                        <Input name="model_number" errors={this.state.errors} value={this.state.product_data.model_number} label="Model ID / MPN" type="text"/>
                                        <Input name="sku" errors={this.state.errors} value={this.state.product_data.sku} label="Sku Number" type="text"/>
                                        <Input name="upc" errors={this.state.errors} value={this.state.product_data.upc} label="UPC" type="text"/>
                                        <Input name="isbn" errors={this.state.errors} value={this.state.product_data.isbn} label="ISBN" type="text"/>
                                        <Input name="ean" errors={this.state.errors} value={this.state.product_data.ean} label="EAN" type="text"/>
                                        <Input name="store_price" errors={this.state.errors} value={this.state.product_data.store_price} label="Store Price" type="text"/>

                                        {!has_extra_items && <div className="form-group">
                                            <label>Inventory</label>
                                            <div className="product-inventory-section">
                                                {default_inventory_entries}
                                            </div>
                                        </div>}

                                        {!has_extra_items && <Input name="stock_status_override" errors={this.state.errors}
                                               updateListHandler={this.updateSelectList.bind(this)}
                                               type="select"
                                               options={[{id: 'in_stock', label: 'In Stock'}, {id: 'out_of_stock', label: 'Out Of Stock'}]}
                                               value={this.state.product_data.stock_status_override}
                                               label={"Stock Status Override"}/>}

                                        {!has_extra_items &&
                                        <div className="form-group">
                                            <label>Stock Status</label>
                                            <select disabled="disabled" value={this.state.product_data.calculated_stock_status} className="form-control">
                                                <option value="in_stock">In Stock</option>
                                                <option value="out_of_stock">Out Of Stock</option>
                                            </select>
                                        </div>}

                                        <Input name="weight" errors={this.state.errors} value={this.state.product_data.weight} label="Weight" type="text"/>
                                        <Input name="vendor_id" error_key="vendor" updateListHandler={this.updateSelectList.bind(this)} errors={this.state.errors} value={this.state.product_data.vendor_id} label="Vendor" options={this.state.vendors} type="select" create_links={{create_label: 'Add New Vendor', name: 'vendors', create_model: 'Vendor', create_url: '/admin/vendor'}}/>
                                        <Input name="is_inactive" errors={this.state.errors} type="checkbox" label="Is Inactive?" value={this.state.product_data.is_inactive}/>
                                        <Input name="ships_alone" errors={this.state.errors} type="checkbox" label="Ships Alone?" value={this.state.product_data.ships_alone}/>
                                        <Input name="affiliate_allowed" errors={this.state.errors} type="checkbox" label="Affiliate Allowed?" value={this.state.product_data.affiliate_allowed}/>
                                        <Input name="can_preorder" errors={this.state.errors} type="checkbox" label="Can Preorder?" value={this.state.product_data.can_preorder}/>
                                    </div>
                                    <div className="col-sm-6">
                                        <div className="row">
                                            <div className="col-sm-8 product-category-section">
                                                <Input name="categories" type="select" error_key="categories" updateListHandler={this.updateSelectList.bind(this)} errors={this.state.errors}
                                                       is_required="true" label="Categories" options={this.state.categories}
                                                       create_links={{create_url: '/admin/product-category', create_model: 'ProductCategory', create_label: 'Add New Category'}}/>
                                            </div>
                                            <div className="col-sm-4">
                                                <div className="form-group">
                                                    <button onClick={this.addCategory.bind(this)} className="btn btn-info"><i className="fa fa-plus-circle"/>Add Category</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="form-group">
                                            <MenuBox removeEntryHandler={this.removeCategory.bind(this)} entries={this.state.product_data.view_categories} name="category"/>
                                        </div>
                                        <div className="form-group">
                                            <button onClick={this.doSaveProduct.bind(this)} type="submit" className="btn btn-success"><i className="fa fa-save"/> Save Product</button>
                                            {delete_product_button}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="content" className="tab-pane fade">
                            {this.renderContentSection()}
                        </div>
                        <div id="images" className="tab-pane fade">
                            {this.renderImagesSection()}
                        </div>
                        <div id="items" className="tab-pane fade">
                            {this.renderItemSection()}
                        </div>
                    </div>
                </form>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('product_edit_form');
        if(element)
        {
            ReactDOM.render(<ProductEditForm
                categories={JSON.parse(element.dataset.categories)}
                vendors={JSON.parse(element.dataset.vendors)}
                stock_locations={JSON.parse(element.dataset.stocklocations)}
                product_data={JSON.parse(element.dataset.product)}/>, element);
        }
    }
}