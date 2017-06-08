import React from 'react';
import Util from '../../../core/Util';
const numeral = require('numeral');

export default class MiniBuyBox extends React.Component
{
    render()
    {
      // Render product price
      let product_price = null;

      if(!this.props.data.list_price || this.props.data.list_price === 0)
      {
        product_price = <p className="product-price">{numeral(this.props.data.store_price).format('$0,0.00')}</p>
      }
      else
      {
        product_price = <p className="product_price">
          <span className="list-price">{numeral(this.props.data.list_price).format('$0,0.00')}</span>
          <span className="store-price">{numeral(this.props.data.store_price).format('$0,0.00')}</span>
        </p>
      }

      let image_url = (Util.get_use_cache() === 'true') ? `/imagecache/174x240-0${this.props.data.image}` : this.props.data.image;

      return(
          <div className="mini-buy-box">
              <a className="mini-buy-box-image-link" href={`/${this.props.data.slug}`}>
                <img src={image_url}/>
              </a>
              <a className="product-name-section" href={`/${this.props.data.slug}`}>
                <p className="brand-name">{this.props.data.brand}</p>
                <p className="product-name">{this.props.data.name}</p>
              </a>
              {this.props.display_mode !== 'recommendation' ?
                  <div className="product-price-section">{product_price}</div> : null}
              {(this.props.display_mode !== 'recommendation' && !this.props.hide_add_to_cart) ?
                  <a href={`/${this.props.data.slug}`} className="add-to-cart">Add To Cart</a> : null}
          </div>
      );
    }
}
