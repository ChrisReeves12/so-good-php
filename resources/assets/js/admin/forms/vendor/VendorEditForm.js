/**
 * Form used in Admin panel for creating and updating vendor records.
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';
import Input from '../../forms/Input';
import SingleImageUpload from '../../components/single_image_upload/SingleImageUpload';
import Popup from '../../../core/Popup';
import Util from '../../../core/Util';

export default class VendorEditForm extends React.Component
{
    constructor()
    {
        super();
        this.popup = new Popup();
    }

    componentWillMount()
    {
        let state = Object.assign({}, this.props);
        state.errors = {};
        state.vendor_data.address = state.vendor_data.address || {};
        this.setState(state);
    }

    uploadImage(files)
    {
        if(!this.state.vendor_data.id)
        {
            this.popup.show('Please save the vendor first, then you can upload a photo.');
        }
        else
        {
            let vendor_data = Object.assign({}, this.state.vendor_data);

            let form_data = new FormData();
            form_data.append('file', files[0]);
            form_data.append('_token', Util.get_auth_token());

            $.ajax({
                url: '/admin/vendor/image/upload/' + vendor_data.id,
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
                        vendor_data.view_image = {href: res.responseJSON.href, file_name: res.responseJSON.fil_name};
                        this.setState({vendor_data});
                    }
                    else if(res.status == 0)
                    {
                        // Todo: Handle timeout
                    }
                }
            });
        }
    }

    deleteImage()
    {
        if(this.state.vendor_data.id)
        {
            $.ajax({
                url: '/admin/vendor/delete-image/' + this.state.vendor_data.id,
                method: 'DELETE',
                dataType: 'json',
                timeout: 3000,
                data: {_token: Util.get_auth_token()},
                complete: (res) => {
                    if(res.status == 200)
                    {
                        if(res.responseJSON.system_error)
                        {
                            let popup = new Popup();
                            popup.show(res.responseJSON.system_error);
                        }
                        else
                        {
                            let vendor_data = Object.assign({}, this.state.vendor_data);
                            vendor_data.view_image = null;
                            this.setState({vendor_data});
                        }
                    }
                }
            });
        }
    }

    saveVendor(e)
    {
        e.preventDefault();
        let vendor_data = Object.assign({}, this.state.vendor_data);

        // Collect general data from inputs
        let form_data = {address: {}};
        let general_form_inputs = $('.general-data').find(':input');
        for(let form_input of general_form_inputs)
        {
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

        // Get address information
        let address_form_inputs = $('.address-section').find(':input');
        for(let form_input of address_form_inputs)
        {
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
                form_data.address[form_input.name] = value;
        }

        $.ajax({
            url: '/admin/record/Vendor/' + (vendor_data.id ? vendor_data.id : ''),
            method: (vendor_data.id) ? 'PUT' : 'POST',
            timeout: 4000,
            dataType: 'json',
            data: {_token: Util.get_auth_token(), data: form_data},
            complete: (res) => {
                if(res.status == 200)
                {
                    if(!Util.objectIsEmpty(res.responseJSON.errors) && (res.responseJSON.system_error === '' || !res.responseJSON.system_error))
                    {
                        this.setState({errors: res.responseJSON.errors});
                    }
                    else if(res.responseJSON.system_error !== '' && res.responseJSON.system_error)
                    {
                        this.popup.show(res.responseJSON.system_error);
                    }
                    else
                    {
                        window.location = '/admin/vendor/' + res.responseJSON.id;
                    }
                }
                else if(res.statusCode == 0)
                {
                    // Todo: Handle timeout
                }
            }
        });
    }

    doDelete(e)
    {
        e.preventDefault();
        if(confirm('Delete vendor?'))
        {
            $.ajax({
                url: '/admin/record/Vendor/' + this.state.vendor_data.id,
                method: 'DELETE',
                data: {_token: Util.get_auth_token()},
                timeout: 3000,
                dataType: 'json',
                complete: (res) => {
                    if(res.status == 200)
                    {
                        window.location = '/admin/list/vendor';
                    }
                    else if(res. status == 0)
                    {
                        // Todo: handle timeout
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
                    <a href="/admin/vendor" className="btn btn-secondary"><i className="fa fa-pencil-square" /> Create New</a>
                    <a href="/admin/list/vendor" className="btn btn-secondary"><i className="fa fa-list" /> List All Vendors</a>
                </div>
                <div className="row">
                    <div className="col-sm-9">
                        <form className="vendor-edit-form" method="post">
                            <span className="general-data">
                                <Input errors={this.state.errors} is_required="true" name="name" type="text" label="Name" value={this.state.vendor_data.name}/>
                                <Input errors={this.state.errors} name="website" type="text" label="Website" value={this.state.vendor_data.website}/>
                                <Input errors={this.state.errors} name="email" type="text" label="Email" value={this.state.vendor_data.email}/>
                                <Input errors={this.state.errors} name="phone_number" type="text" label="Phone Number" value={this.state.vendor_data.phone_number}/>
                            </span>

                            <h4>Address</h4>
                            <div className="address-section">
                                <Input errors={this.state.errors} error_key="address.line_1" name="line_1" type="text" label="Address line 1" value={this.state.vendor_data.address.line_1}/>
                                <Input errors={this.state.errors} error_key="address.line_2" name="line_2" type="text" label="Address line 2" value={this.state.vendor_data.address.line_2}/>
                                <Input errors={this.state.errors} error_key="address.city" name="city" type="text" label="City" value={this.state.vendor_data.address.city}/>
                                <Input errors={this.state.errors} error_key="address.state" name="state" type="text" label="State" value={this.state.vendor_data.address.state}/>
                                <Input errors={this.state.errors} error_key="address.zip" name="zip" type="text" label="Zip Code" value={this.state.vendor_data.address.zip}/>
                                <Input errors={this.state.errors} error_key="address.country" name="country" type="text" label="Country" value={this.state.vendor_data.address.country}/>
                            </div>

                            <span className="general-data">
                                <Input name="is_inactive" type="checkbox" label="Is Inactive?" value={this.state.vendor_data.is_inactive}/>
                                <Input name="is_dropshipper" type="checkbox" label="Is Dropshipper?" value={this.state.vendor_data.is_dropshipper}/>
                            </span>

                            <div className="form-group">
                                <button onClick={this.saveVendor.bind(this)} className="btn btn-success"><i className="fa fa-save" /> Save Vendor</button>
                                <button onClick={this.doDelete.bind(this)} className="btn btn-danger"><i className="fa fa-times" /> Delete Vendor</button>
                            </div>
                        </form>
                    </div>
                    <div className="col-sm-3 photo-upload-section">
                        <SingleImageUpload image={this.state.vendor_data.view_image} deleteHandler={this.deleteImage.bind(this)} uploadHandler={this.uploadImage.bind(this)} label="Thumbnail Image"/>
                    </div>
                </div>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('vendor_edit_form');
        if(element)
        {
            ReactDOM.render(<VendorEditForm vendor_data={JSON.parse(element.dataset.vendorData)}/>, element);
        }
    }
}
