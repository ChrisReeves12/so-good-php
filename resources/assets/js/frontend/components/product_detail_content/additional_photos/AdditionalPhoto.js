import React from 'react';

export default class AdditionalPhoto extends React.Component
{
    handleSelect(e)
    {
        e.preventDefault();
        this.props.selectPhotoHandler(this.props.idx);
    }

    render()
    {
        return(
            <a onClick={this.handleSelect.bind(this)} className={'additional-photo ' + (this.props.image.is_main ? 'selected' : '')} href={this.props.image.url}>
                <img src={this.props.cache_prefix + this.props.image.url}/>
            </a>
        );
    }
}