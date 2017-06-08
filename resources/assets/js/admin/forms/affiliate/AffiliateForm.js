/**
 * AffiliateForm
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

import React from 'react';
import ReactDOM from 'react-dom';
import Input from 'forms/Input';
import Util from 'common/core/Util';
import Popup from 'common/core/Popup';
import ImageDialog from 'forms/affiliate/image_dialog/ImageDialog';

export default class AffiliateForm extends React.Component {
    constructor() {
        super();
        this.popup = new Popup();
        this.state = window.sogood.reactjs.initial_data;
        this.state.is_inactive = this.state.is_inactive || false;
        this.state.selected_image = null;
        this.state.errors = [];
        this.social_editor = null;
        this.videos_editor = null;
    }

    componentDidMount()
    {
        this.social_editor = window.ace.edit('social_media_links_editor');
        this.social_editor.getSession().setMode('ace/mode/json');
        this.social_editor.setTheme("ace/theme/github");
        this.social_editor.getSession().setUseWrapMode(true);

        this.videos_editor = window.ace.edit('videos_editor');
        this.videos_editor.getSession().setMode('ace/mode/json');
        this.videos_editor.setTheme("ace/theme/github");
        this.videos_editor.getSession().setUseWrapMode(true);
    }

    doSave(e) {
        e.preventDefault();
        this.setState({errors: []});
        let state = Object.assign({}, this.state);
        state.errors = [];

        // Move data to correct variables
        let data = state;
        data.videos = JSON.parse(this.videos_editor.getValue());
        data.social_media_links = JSON.parse(this.social_editor.getValue());

        $.ajax({
            url: '/admin/record/Affiliate/' + (this.state.id || ''),
            method: (this.state.id) ? 'PUT' : 'POST',
            dataType: 'json',
            data: {data, _token: Util.get_auth_token()},
            complete: (res) => {
                if(res.status === 200) {
                    if(res.responseJSON.errors) {
                        state.errors = res.responseJSON.errors;
                    }
                    else if(res.responseJSON.system_error) {
                        let popup = new Popup();
                        popup.show(res.responseJSON.system_error);
                    }
                    else {
                        window.location = '/admin/affiliate/' + res.responseJSON.id;
                    }

                    this.setState(state);
                }
            }
        });
    }

    updateValueHandler(input) {
        let state = Object.assign({}, this.state);
        let value;
        let name = input.name;

        if(input.type !== 'checkbox') {
            value = input.value;
        }
        else {
            value = $(input).is(':checked');
        }

        state[name] = value;
        this.setState(state);
    }

    updateAffiliateType(e) {
        e.preventDefault();
        this.setState({type: e.target.value});
    }

    uploadImage(form_data, id)
    {
        if(form_data)
        {
            $.ajax({
                url: '/admin/affiliate/upload/' + id,
                method: 'POST',
                dataType: 'json',
                data: form_data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 10000,
                complete: (res) => {
                    if(res.status === 200)
                    {
                        if(res.responseJSON.errors)
                        {
                            this.popup.show(res.responseJSON.errors);
                        }
                        else
                        {
                            this.setState({images: res.responseJSON.images, main_image: res.responseJSON.main_image});
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

    changeMainPhoto(checked)
    {
        // Set selected photo as main
        let state = Object.assign({}, this.state);
        if(checked)
        {
            state.main_image = state.selected_image;
        }
        else
        {
            if(Array.isArray(state.images) && state.images.length > 1)
            {
                state.main_image = state.images.filter(i => (i !== state.selected_image))[0];
            }
        }

        if(Array.isArray(state.images) && state.images.length > 1)
        {
            $.ajax({
                url: '/admin/affiliate/update-main-image/' + this.state.id,
                method: 'PUT',
                dataType: 'json',
                data: {_token: Util.get_auth_token(), main_image: state.main_image},
                timeout: 3000,
                complete: (res) => {
                    if(res.status === 200)
                    {
                        this.setState(state);
                    }
                    else if(res.status === 0)
                    {
                        // TODO: Handle timeout
                    }
                }
            });
        }
    }

    selectImage(image)
    {
        this.setState({selected_image: image.props.file_name});
    }

    removeImage(idx, image_to_remove)
    {
        let state = Object.assign({}, this.state);

        // If there is no main photo, use the first one
        let new_main_image = null;
        if(!state.main_image && state.main_image !== image_to_remove)
        {
            new_main_image = state.images[0];
        }

        $.ajax({
            url: '/admin/affiliate/upload/' + this.state.id,
            method: 'DELETE',
            data: {
                _token: Util.get_auth_token(),
                removed_image: image_to_remove.props.file_name,
                new_main_image: new_main_image
            },
            dataType: 'json',
            timeout: 4000,
            complete: (res) => {
                if(res.status === 200)
                {
                    state.images = res.responseJSON.images;
                    state.main_image = res.responseJSON.main_image;

                    this.setState(state);
                }
                else if(res.status === 0)
                {
                    // Todo: Handle timeout
                }
            }
        });
    }

    render() {
        return (
            <div>
                <div style={{marginBottom: '32px'}}>
                    <a href="/admin/affiliate" className="btn btn-secondary"><i className="fa fa-pencil-square"/> Create New</a>
                    <a href="/admin/list/affiliate" className="btn btn-secondary"><i className="fa fa-list"/> List All Affiliates</a>
                </div>
                <ul className="nav nav-pills">
                    <li className="nav-item"><a className="nav-link active" data-toggle="tab" href="#general"><i
                        className="fa fa-info-circle"/> General Information</a></li>
                    <li className="nav-item"><a className="nav-link" data-toggle="tab" href="#images"><i
                        className="fa fa-picture-o"/> Images</a></li>
                </ul>
                <div className="tab-content">
                    <div id="general" className="tab-pane fade in active">
                        <form onSubmit={this.doSave.bind(this)}>
                            <Input is_required="true" errors={this.state.errors}
                                   updateValueHandler={this.updateValueHandler.bind(this)} value={this.state.name}
                                   name="name" label="Name" type="text"/>
                            <Input is_required="true" errors={this.state.errors}
                                   updateValueHandler={this.updateValueHandler.bind(this)} value={this.state.slug}
                                   name="slug" label="Slug" type="text"/>
                            <Input errors={this.state.errors}
                                   updateValueHandler={this.updateValueHandler.bind(this)} value={this.state.list_page_image}
                                   name="list_page_image" label="Landing Page Image" type="text"/>
                            <Input is_required="true" errors={this.state.errors}
                                   updateValueHandler={this.updateValueHandler.bind(this)}
                                   value={this.state.affiliate_tag} name="affiliate_tag"
                                   label="Affiliate Seller Channel" type="text"/>
                            <Input errors={this.state.errors} updateValueHandler={this.updateValueHandler.bind(this)}
                                   value={this.state.website} name="website" label="Website" type="text"/>
                            <Input errors={this.state.errors} updateValueHandler={this.updateValueHandler.bind(this)}
                                   value={this.state.short_bio} name="short_bio" label="Bio" type="textarea"/>

                            <div className="form-group">
                                <label>Affiliate Type</label>
                                <select onChange={this.updateAffiliateType.bind(this)} value={this.state.type}
                                        className="form-control">
                                    <option value="">Select Affiliate Type</option>
                                    <option value="vlogger">Vlogger</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <label>Videos</label>
                            <div style={{width: '100%', height: 300, marginBottom: 15, border: '1px solid #bfbfbf'}}
                                 id="videos_editor">{this.state.videos_json}</div>

                            <label>Social Media Links</label>
                            <div style={{width: '100%', height: 300, border: '1px solid #bfbfbf', marginBottom: 15}}
                                 id="social_media_links_editor">{this.state.social_media_links_json}</div>

                            <Input updateValueHandler={this.updateValueHandler.bind(this)}
                                   value={this.state.is_inactive} name="is_inactive" label="Is Inactive?"
                                   type="checkbox"/>

                            <button style={{marginTop: 15}} className="btn btn-primary"><i className="fa fa-save"/> Save
                                Affiliate
                            </button>
                        </form>
                    </div>
                    <div id="images" className="tab-pane fade">
                        {this.state.id ? <ImageDialog uploadImageHandler={this.uploadImage.bind(this)}
                                     changeMainPhoto={this.changeMainPhoto.bind(this)}
                                     id={this.state.id}
                                     imagePrefix={this.state.image_prefix}
                                     mainImage={this.state.main_image}
                                     selected_image={this.state.selected_image}
                                     selectImage={this.selectImage.bind(this)}
                                     removeImage={this.removeImage.bind(this)}
                                     images={this.state.images}/> : <p>Please save the affiliate and then add images.</p>}
                    </div>
                </div>
            </div>
        );
    }

    static initialize() {
        let element = document.getElementById('affiliate_form');
        if(element) {
            ReactDOM.render(<AffiliateForm/>, element);
        }
    }
}
