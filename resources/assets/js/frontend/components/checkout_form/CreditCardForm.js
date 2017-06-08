/**
 * Class definition of CreditCardForm
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import Util from '../../../core/Util';

export default class CreditCardForm extends React.Component {
    constructor(props) {
        super(props);
    }

    handleUpdateField(e)
    {
        this.props.updateFieldHandler(e.target);
    }

    handleSubmit(e)
    {
        e.preventDefault();
        this.props.orderSubmitHandler();
    }

    render() {
        return (
            <div style={{border: '1px solid #d4d4d4', padding: '20px 23px', borderRadius: '7px', backgroundColor: 'white', boxShadow: '1px 2px 2px rgba(0,0,0,0.2)'}}
                 className="credit-card-section">
                <div style={{marginBottom: 5}} className={'form-group' + (this.props.cc_info.error ? ' has-danger' : '')}>
                    <label className="form-control-label">Credit Card Number:</label>
                    <input onChange={this.handleUpdateField.bind(this)} value={this.props.cc_info.number} maxLength="16" type="text" placeholder="Credit Card Number" className="form-control" name="number"/>
                    {this.props.cc_info.error && <span className="form-control-feedback">{this.props.cc_info.error}</span>}
                </div>
                <img style={{display: 'block', maxWidth: 130, marginBottom: 7}}
                     src="/assets/img/layout/frontend/major_cards.png"/>
                <label>Exp. Date</label>
                <div style={{marginBottom: 20}} className="form-inline">
                    <select onChange={this.handleUpdateField.bind(this)} value={this.props.cc_info.exp_month} className="form-control" style={{width: 80, display: 'inline-block'}} name="exp_month">
                        {(() => { return Util.fillArray(12).map(i => { return(
                            <option key={i} value={i+1}>{i+1}</option>
                        )}); })()}
                    </select> /
                    <select onChange={this.handleUpdateField.bind(this)} value={this.props.cc_info.exp_year} style={{marginRight: 10, width: 80, display: 'inline-block'}} className="form-control" name="exp_year">
                        {(() => { return Util.fillArray(15).map(i => { return(
                            <option key={i} value={i+2017}>{i+2017}</option>
                        )}); })()}
                    </select>
                    <div>
                        <label>CVC: </label><br/>
                        <input onChange={this.handleUpdateField.bind(this)} value={this.props.cc_info.cvc} maxLength="4" style={{width: 90}} type="text" name="cvc" className="form-control"/>
                    </div>
                </div>
                <button onClick={this.handleSubmit.bind(this)} disabled={(this.props.cart_updating || this.props.submitting_order) ? 'disabled' : ''} className="btn btn-success">
                    <i style={{marginRight: 4}} className={(this.props.cart_updating || this.props.submitting_order) ? 'fa fa-hourglass' : 'fa fa-check-circle'}/>
                    {(this.props.cart_updating || this.props.submitting_order) ? 'Please Wait...' : 'Submit Order'}</button>
            </div>
        );
    }
}