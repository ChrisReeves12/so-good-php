/**
 * An entry of the side bar menu
 * @author Christopher Reeves
 */

import React from 'react';
import Util from '../../../core/Util';

export default class SingleImageUpload extends React.Component
{
    render()
    {
        let image_element = null;
        if(!Util.objectIsEmpty(this.props.image))
            image_element = <img src={this.props.image.href} />;
        else
            image_element = <div className="no-image-notice">No Image</div>;

        return(
            <div className="single-photo-upload">
                <label>{this.props.label}</label>
                <div className="thumbnail-container">
                    {image_element}
                </div>
                <input onChange={(e) => { this.temp_files = e.target.files }} id="image_upload_input" type="file" name="image" />
                <button onClick={(e) => { e.preventDefault(); this.props.uploadHandler(this.temp_files) }} className="btn btn-success"><i className="fa fa-upload" /> Upload Photo</button>
                <button onClick={(e) => { e.preventDefault(); this.props.deleteHandler(this) }} className="btn btn-danger"><i className="fa fa-times" /> Delete Photo</button>
            </div>
        );
    }
}