import React from 'react';

export default class MainImage extends React.Component
{
    render()
    {
        return(
            <div className="col-sm-4">
                <h4>Main Image</h4>
                <div className="main-image-container">
                    {this.props.image && <a href={this.props.image.url}>
                        <img src={this.props.image.url}/>
                    </a>}
                </div>
            </div>
        );
    }
}