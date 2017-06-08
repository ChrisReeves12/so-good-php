/**
 * Class definition of BannerImageUpload
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';

export default class BannerImageUpload extends React.Component {
    constructor(props) {
        super(props);
        this.file = null;
    }

    handleUpload(e)
    {
        e.preventDefault();
        this.props.uploadHandler(this.file);
    }

    handleDelete(e)
    {
        e.preventDefault();
        this.props.deleteHandler();
    }

    render() {
        return (
            <div className="banner-image-uploader">
                <h4>Banner Image</h4>
                <div className="image-display-area">
                    {!this.props.image ? <h5>No Image</h5> : <img src={this.props.image.href}/>}
                </div>
                <input onChange={(e => { this.file = e.target.files[0] })} type="file"/>
                <button onClick={this.handleUpload.bind(this)} className="btn btn-success"><i className="fa fa-upload"/> Upload Photo</button>
                <button onClick={this.handleDelete.bind(this)} className="btn btn-danger"><i className="fa fa-times"/> Delete Photo</button>
            </div>
        );
    }
}