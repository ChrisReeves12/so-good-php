/**
 * Form that is used to create and edit sales orders
 * @author Christopher Reeves <ChrisReeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';
import Input from '../../forms/Input';
import SingleImageUpload from '../../components/single_image_upload/SingleImageUpload';
import Util from '../../../core/Util';
import Popup from '../../../core/Popup';
import BannerImageUpload from '../../components/banner_image_upload/BannerImageUpload';

export default class ProductCategoryEditForm extends React.Component
{
    constructor(props)
    {
        super(props);
        this.state = props;
        this.popup = new Popup();
    }

    uploadImage(files)
    {
        if(!this.state.product_category_data.id)
        {
            this.popup.show('Please save the category first, then you can upload a photo.');
        }
        else
        {
            let product_category_data = Object.assign({}, this.state.product_category_data);

            let form_data = new FormData();
            form_data.append('file', files[0]);
            form_data.append('_token', Util.get_auth_token());

            $.ajax({
                url: '/admin/product-category/image/upload/' + product_category_data.id,
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
                        if(res.responseJSON.system_error)
                        {
                            this.popup.show(res.responseJSON.system_error);
                        }
                        else
                        {
                            product_category_data.view_image = res.responseJSON;
                            this.setState({product_category_data});
                        }
                    }
                    else if(res.status == 0)
                    {
                        // Todo: Handle timeout
                    }
                }
            });
        }
    }

    saveCategory(e)
    {
        let product_category_data = Object.assign({}, this.state.product_category_data);

        e.preventDefault();
        let form_data = {};
        let form_inputs = $('form.product-category-form').find(':input');
        for(let form_input of form_inputs)
        {
            if(form_input.name == 'parent_category_id' && form_input.value == 'Select Parent Category')
                continue;

            let value = null;
            if($(form_input).is(':checkbox'))
            {
                value = $(form_input).is(':checked');
            }
            else
            {
                value = form_input.value;
            }

            if(form_input.name != null && form_input.name != '')
                form_data[form_input.name] = value;
        }

        this.setState({errors: null});

        $.ajax({
            url: '/admin/record/ProductCategory/' + (product_category_data.id ? product_category_data.id : ''),
            method: (product_category_data.id) ? 'PUT' : 'POST',
            timeout: 4000,
            dataType: 'json',
            data: {_token: Util.get_auth_token(), data: form_data},
            complete: (res) => {
                if(res.status == 200)
                {
                    if(!Util.objectIsEmpty(res.responseJSON.errors))
                    {
                        this.setState({errors: res.responseJSON.errors});
                    }
                    else
                    {
                        window.location = '/admin/product-category/' + res.responseJSON.id;
                    }
                }
                else if(res.statusCode == 0)
                {
                    // Todo: Handle timeout
                }
            }
        });
    }

    deleteImage()
    {
        if(this.state.product_category_data.id)
        {
            $.ajax({
                url: '/admin/product-category/delete-image/' + this.state.product_category_data.id,
                method: 'DELETE',
                dataType: 'json',
                timeout: 3000,
                data: {_token: Util.get_auth_token()},
                complete: (res) => {
                    if(res.status == 200)
                    {
                        if(res.responseJSON.system_error)
                        {
                            this.popup.show(res.responseJSON.system_error);
                        }
                        else
                        {
                            let product_category_data = Object.assign({}, this.state.product_category_data);
                            product_category_data.view_image = null;
                            this.setState({product_category_data});
                        }
                    }
                }
            });
        }
    }

    deleteBanner()
    {
        if(this.state.product_category_data.id)
        {
            $.ajax({
                url: '/admin/product-category/delete-banner/' + this.state.product_category_data.id,
                method: 'DELETE',
                dataType: 'json',
                timeout: 3000,
                data: {_token: Util.get_auth_token()},
                complete: (res) => {
                    if(res.status == 200)
                    {
                        if(res.responseJSON.system_error)
                        {
                            this.popup.show(res.responseJSON.system_error);
                        }
                        else
                        {
                            let product_category_data = Object.assign({}, this.state.product_category_data);
                            product_category_data.view_banner = null;
                            this.setState({product_category_data});
                        }
                    }
                }
            });
        }
    }

    doDelete(e)
    {
        e.preventDefault();
        if(confirm('Delete this product category?'))
        {
            $.ajax({
                url: '/admin/record/ProductCategory/' + this.state.product_category_data.id,
                method: 'DELETE',
                data: {_token: Util.get_auth_token()},
                timeout: 3000,
                dataType: 'json',
                complete: (res) => {
                    if(res.status == 200)
                    {
                        window.location = '/admin/product-category/list';
                    }
                    else if(res. status == 0)
                    {
                        // Todo: handle timeout
                    }
                }
            });
        }
    }

    uploadBanner(file)
    {
        if(!this.state.product_category_data.id)
        {
            this.popup.show('Please save the category first, then you can upload a banner.');
        }
        else
        {
            let product_category_data = Object.assign({}, this.state.product_category_data);

            let form_data = new FormData();
            form_data.append('file', file);
            form_data.append('_token', Util.get_auth_token());

            $.ajax({
                url: '/admin/product-category/banner/upload/' + product_category_data.id,
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
                        if(res.responseJSON.system_error)
                        {
                            this.popup.show(res.responseJSON.system_error);
                        }
                        else
                        {
                            product_category_data.view_banner = res.responseJSON;
                            this.setState({product_category_data});
                        }
                    }
                    else if(res.status == 0)
                    {
                        // Todo: Handle timeout
                    }
                }
            });
        }
    }

    render()
    {
        return(
            <div>
                <div style={{marginBottom: 32}}>
                    <a href="/admin/product-category" className="btn btn-secondary"><i className="fa fa-pencil-square" /> Create New</a>
                    <a href="/admin/product-category/list" className="btn btn-secondary"><i className="fa fa-list" /> List All Product Categories</a>
                </div>
                <div className="row">
                    <div className="col-sm-9">
                        <form className="product-category-form" method="post">
                            <Input errors={this.state.errors} value={this.state.product_category_data.name} is_required="true" name="name" label="Name" type="text"/>
                            <Input errors={this.state.errors} value={this.state.product_category_data.slug} is_required="true" name="slug" label="Slug" type="text"/>
                            <Input errors={this.state.errors} name="parent_category_id" options={this.state.product_categories} label="Parent Category" type="select"/>
                            <Input errors={this.state.errors} value={this.state.product_category_data.description} name="description" label="Description" type="textarea"/>
                            <Input errors={this.state.errors} value={this.state.product_category_data.tags} name="tags" label="Tags" type="textarea"/>
                            <Input value={this.state.product_category_data.is_inactive} name="is_inactive" label="Is Inactive?" type="checkbox"/>
                            <BannerImageUpload deleteHandler={this.deleteBanner.bind(this)} image={this.state.product_category_data.view_banner} uploadHandler={this.uploadBanner.bind(this)}/>
                            <div className="form-group">
                                <button className="btn btn-success" onClick={this.saveCategory.bind(this)}><i className="fa fa-save" /> Save Category</button>
                                <button className="btn btn-danger" onClick={this.doDelete.bind(this)}><i className="fa fa-times" /> Delete Category</button>
                            </div>
                        </form>
                    </div>
                    <div className="col-sm-3 photo-upload-section">
                        <SingleImageUpload image={this.state.product_category_data.view_image} deleteHandler={this.deleteImage.bind(this)} uploadHandler={this.uploadImage.bind(this)} label="Thumbnail Image"/>
                    </div>
                </div>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('product_category_edit_form');
        if(element)
        {
            ReactDOM.render(<ProductCategoryEditForm
                product_categories={JSON.parse(element.dataset.productCategories)}
                product_category_data={JSON.parse(element.dataset.productCategoryData)}/>, element)
        }
    }
}