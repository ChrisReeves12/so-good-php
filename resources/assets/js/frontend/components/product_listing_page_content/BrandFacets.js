import React from 'react';

export default class BrandFacets extends React.Component
{
  render()
  {
    let brand_section = null;
    if(this.props.brand_facets.length > 0)
    {
      let brands_listings = (<ul className="brand-facets">
          {(() => {
              let idx = -1;
              return this.props.brand_facets.map(bf => {
                  idx++;
                  let brand_name = Object.keys(bf)[0];
                  let brand_qty = bf[brand_name];
                  return(
                      <li key={idx}><a href={`/site-search?keyword=${encodeURIComponent(brand_name)}`}>{brand_name} ({brand_qty})</a></li>
                  );
              });
          })()}
      </ul>);

      brand_section = (
        <div className="facet-section-block brand-facets">
          <h4>Popular Brands</h4>
          {brands_listings}
        </div>
      )
    }

    return brand_section;
  }
}
