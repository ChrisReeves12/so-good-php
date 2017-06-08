/**
 * GiftCardBalanceModal
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

import React from 'react';
import ReactDOM from 'react-dom';

export default class GiftCardBalanceModal extends React.Component
{
    constructor()
    {
        super();
        this.state = this.getInitialState();
    }

    componentDidMount()
    {
        $(document).on('gift-card-check-modal', () => {
            this.setState(this.getInitialState());
        });
    }

    getInitialState()
    {
        return {
            email_address: '',
            number: '',
            balance: null,
            error: null,
            loading: false,
            validation_errors: {}
        };
    }

    updateValue(e)
    {
        let state = Object.assign({}, this.state);
        state[e.target.name] = e.target.value;

        this.setState(state);
    }

    displayError()
    {
        if(this.state.error)
        {
            return (
                <div style={{color: '#ce0000', fontWeight: 'bold', fontSize: '14px', marginBottom: 10}}>
                    {this.state.error}
                </div>
            );
        }
    }

    displayBalance()
    {
        if(this.state.balance)
        {
            return (
                <div style={{color: '#4f804f', fontSize: '17px', marginBottom: 14}}>
                    Available Balance: <strong>${this.state.balance}</strong>
                </div>
            );
        }
        else if(this.state.loading)
        {
            return(
                <div style={{color: '#535353', fontSize: '17px', marginBottom: 14}}>
                    <i className="fa fa-clock-o"/> Loading...please wait
                </div>
            );
        }
    }

    doSubmit(e)
    {
        e.preventDefault();
        if($.active === 0)
        {
            this.setState({error: null, loading: true, validation_errors: {}});

            $.ajax({
                url: '/shopping-cart/ajax/gift-card/balance',
                method: 'GET',
                dataType: 'json',
                data: {email_address: this.state.email_address, number: this.state.number},
                timeout: 3000,
                complete: (res) => {
                    if(res.status === 200)
                    {
                        if(res.responseJSON.system_error)
                        {
                            this.setState({error: res.responseJSON.system_error});
                        }
                        else
                        {
                            this.setState({balance: res.responseJSON.balance});
                        }
                    }
                    else if(res.status === 422)
                    {
                        // Validation errors
                        this.setState({validation_errors: res.responseJSON});
                    }
                    else if(res.status === 0)
                    {
                        this.setState({error: 'The operation timed out, please try again.'});
                    }

                    this.setState({loading: false});
                }
            });
        }
    }

    displayErrors(errors)
    {
        if(errors.length > 0) {
            return (
                <ul style={{listStyle: 'none', color: '#af0202', padding: 0, margin: 0}} className="errors">
                    {(() => {
                        return errors.map(err => {
                            return(<li>{err}</li>);
                        });
                    })()}
                </ul>
            );
        }
    }

    render()
    {
        return(
            <div className="modal-dialog" role="document">
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title" id="modal_gift_card_balance_title"><i className="fa fa-credit-card-alt" /> Check Gift Card Balance</h5>
                    </div>
                    <div className="modal-body">
                        <form onSubmit={this.doSubmit.bind(this)}>
                            <div className="row">
                                <div className="col-xs-12">
                                    {this.displayError()}
                                    <div className={"form-group" + (Array.isArray(this.state.validation_errors['email_address']) ? ' has-danger' : '')}>
                                        <label>Email Address</label>
                                        <input onChange={this.updateValue.bind(this)} name="email_address" value={this.state.email_address} className="form-control" type="text" placeholder="Email Address" />
                                        {this.displayErrors(Array.isArray(this.state.validation_errors['email_address']) ? this.state.validation_errors['email_address'] : [])}
                                    </div>
                                    <div className={"form-group" + (Array.isArray(this.state.validation_errors['number']) ? ' has-danger' : '')}>
                                        <label>Gift Card Number</label>
                                        <input onChange={this.updateValue.bind(this)} name="number" value={this.state.number} className="form-control" type="text" placeholder="Gift Card Number" />
                                        {this.displayErrors(Array.isArray(this.state.validation_errors['number']) ? this.state.validation_errors['number'] : [])}
                                    </div>
                                    {this.displayBalance()}
                                    <div className="form-group">
                                        <button type="submit" className="btn btn-success"><i className="fa fa-dollar" /> Get Balance</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div className="modal-footer">
                        <button type="button" className="btn btn-secondary" style={{marginRight: 4}} data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('modal_gift_card_balance');
        if(element)
            ReactDOM.render(<GiftCardBalanceModal/>, element);
    }
}
