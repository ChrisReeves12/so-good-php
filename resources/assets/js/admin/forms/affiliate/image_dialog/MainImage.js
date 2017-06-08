import React from 'react';

export default class MainImage extends React.Component
{
    render()
    {
        return(
            <div className="col-sm-4">
                <h4>Profile Image</h4>
                <div className="main-image-container">
                    {this.props.image && <a href={this.props.imagePrefix + this.props.image}>
                        <img src={this.props.imagePrefix + this.props.image}/>
                    </a>}
                </div>
                <p>{this.props.image}</p>
            </div>
        );
    }
}