import React from 'react';

export default class Image extends React.Component
{
    handleRemoval()
    {
        this.props.removeImage(this.props.idx, this);
    }

    handleSelect()
    {
        this.props.selectImage(this);
    }

    render()
    {
        return(
            <li>
                <div onClick={this.handleSelect.bind(this)} className={"additional-image " + (this.props.is_selected ? 'selected' : '')}>
                    <div onClick={this.handleRemoval.bind(this)} className="close-button">
                        <i className="fa fa-times"/>
                    </div>
                    <img src={this.props.url}/>
                </div>
                <p style={{margin: 0, padding: 0, fontSize: 11}}>{this.props.file_name}</p>
            </li>
        );
    }
}