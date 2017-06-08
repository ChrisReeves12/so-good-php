import React from 'react'
import AdditionalPhoto from './AdditionalPhoto';

export default class AdditionalPhotos extends React.Component
{
    render()
    {
        // Render photo list
        let idx = -1;
        let images = this.props.product.images
            .map(img => {
                idx++;
                return(
                    <AdditionalPhoto cache_prefix={this.props.cache_prefix} selectPhotoHandler={this.props.selectPhotoHandler} idx={idx} key={idx} image={img}/>
                )
        });

        return (
            <div className="additional-photo-picker">
                {images}
            </div>
        )
    }
}
