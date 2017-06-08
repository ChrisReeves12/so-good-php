/**
 * GiftCardForm
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

import React from 'react';

export default class GiftCardForm extends React.Component
{
    render()
    {
        return(
            <div style={{border: '1px solid #bfbfbf', borderRadius: 6, padding: 18}}>
                <h6><i className="fa fa-credit-card-alt"/> Use Gift Card</h6>
                <div className="form-group form-inline">
                    <input onChange={this.props.updateGiftCardNumber}
                           value={this.props.gift_card_number}
                           className="form-control mr-1 mb-1"
                           type="text"
                           placeholder="Gift Card Number"/>

                    <input onChange={this.props.updateGiftCardAmount}
                           value={this.props.gift_card_amount}
                           className="form-control mb-1"
                           type="text"
                           placeholder="$ Amount"/>
                </div>
                <div>
                    <button onClick={this.props.doAddGiftCard} className="btn btn-success mr-1">
                        <i className="fa fa-credit-card-alt"/> Apply Gift Card
                    </button>
                    <button onClick={this.props.doAddTotalToGiftCard} className="btn btn-success">
                        <i className="fa fa-dollar"/> Set To Order Total
                    </button>
                </div>
                <p style={{fontSize: 14, marginBottom: 0, marginTop: 16}}><a className="gift_card_balance_link" href=""><i className="fa fa-wrench"/> Check Gift Card Balance</a></p>
            </div>
        );
    }
}