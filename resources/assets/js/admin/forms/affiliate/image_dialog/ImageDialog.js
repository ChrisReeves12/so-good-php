import React from 'react';
import Image from './Image';
import MainImage from './MainImage';
import Util from 'common/core/Util';


export default class ImageDialog extends React.Component
{
    constructor()
    {
        super();
        this.temp_form_data = false;
    }

    handleMainPhotoChange(e)
    {
        let checked = $(e.target).is(':checked');
        this.props.changeMainPhoto(checked);
    }

    prepareFileUpload(e)
    {
        e.preventDefault();
        let form_data = new FormData();

        if(e.target.files.length > 0)
        {
            let auth_token = Util.get_auth_token();
            form_data.append('_token', auth_token);
            form_data.append('file', e.target.files[0]);
            this.temp_form_data = form_data;
        }
    }

    handleUploadImage(e)
    {
        e.preventDefault();
        this.props.uploadImageHandler(this.temp_form_data, this.props.id);
        this.temp_form_data = false;
    }

    render()
    {
        // If there are no photos, then show notice for no photos
        let dialog_display = null;
        if(Array.isArray(this.props.images) && this.props.images.length > 0)
        {
            // Render each image
            let idx = -1;
            let images = this.props.images.map((i) => {
                let is_selected = false;

                if(this.props.selected_image)
                    is_selected = (this.props.selected_image === i);

                idx++;
                return(
                    <Image idx={idx} is_selected={is_selected} selectImage={this.props.selectImage}
                           removeImage={this.props.removeImage} key={i} file_name={i} url={this.props.imagePrefix + i}/>
                );
            });

            dialog_display = (
                <div className="row">
                    <MainImage imagePrefix={this.props.imagePrefix} image={this.props.mainImage}/>
                    <h4>Affiliate Images</h4>
                    <div className="col-sm-4 additional-image-container">
                        <ul>{images}</ul>
                    </div>
                    <div className="col-sm-4 image-options">
                        <div className="form-group">
                            <input
                                onClick={this.handleMainPhotoChange.bind(this)}
                                checked={(this.props.selected_image && this.props.mainImage === this.props.selected_image)}
                                type="checkbox" name="is_main_image"/> <label>Is main image?</label>
                        </div>
                    </div>
                </div>
            );
        }
        else
        {
            dialog_display = (
                <div className="row">
                    <div className="col-sm-4 col-sm-offset-3">
                        <strong><i class="fa fa-info-circle"/> No photos have been added.</strong>
                    </div>
                </div>
            );
        }

        return(
            <div>
                <div className="row">
                    <div className="col-xs-2">
                        <div className="form-group">
                            <label>Choose File</label>
                            <input onChange={this.prepareFileUpload.bind(this)} id="image_upload_input" type="file"/>
                            <button onClick={this.handleUploadImage.bind(this)} style={{marginTop: '10px'}} className="btn btn-success"><i className="fa fa-upload"/> Upload</button>
                        </div>
                    </div>
                </div>
                {dialog_display}
            </div>
        );
    }
}