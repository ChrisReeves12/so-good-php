import React from 'react';
import MiniBuyBox from '../../mini_buy_box/MiniBuyBox';

export default class RecommendedProducts extends React.Component
{
    render()
    {
        // Create lines for product buy boxes
         let idx = -1;
        let products =  this.props.products.map(p => {
            idx++;
             return(
                 <div key={p.id} className="col-md-2">
                    <MiniBuyBox display_mode="recommendation" idx={idx} data={p}/>
                 </div>
             );
        });

         return(
             <div className="product-rec-section">
                 <h4>You May Also Like</h4>
                 {products}
             </div>
         );
    }
}