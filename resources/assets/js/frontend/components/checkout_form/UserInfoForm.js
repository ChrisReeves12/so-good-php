/**
 * Class definition of UserInfoForm
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import Input from '../../../admin/forms/Input';

export default class UserInfoForm extends React.Component {
    constructor(props) {
        super(props);
    }

    handleSubmit(e)
    {
        e.preventDefault();
        this.props.submitHandler();
    }

    render() {
        return (
            <div className="user-info">
                <div className="row">
                    <h5 style={{fontSize: 16, fontWeight: 'bold'}}>Customer Information</h5>
                    <div className="col-md-6">
                        <Input value={this.props.user_info.first_name} errors={this.props.errors} updateValueHandler={this.props.updateFieldHandler} name="first_name" label="First Name" type="text"/>
                        <Input value={this.props.user_info.email} errors={this.props.errors} updateValueHandler={this.props.updateFieldHandler} name="email" label="Email" type="text"/>
                        <div className="hidden-sm-down">
                            <button onClick={this.handleSubmit.bind(this)} disabled={this.props.show_save_notice ? '' : 'disabled'} className="btn btn-info"><i className="fa fa-save"/> Save User Information</button>
                            {this.props.show_save_notice ? <div style={{color: '#3c763d', marginTop: 10, fontWeight: 'bold'}} className="change-notice">Make sure to save the updated user information.</div> : null}
                        </div>
                    </div>
                    <div className="col-md-6">
                        <Input value={this.props.user_info.last_name} errors={this.props.errors} updateValueHandler={this.props.updateFieldHandler} name="last_name" label="Last Name" type="text"/>
                        <Input value={this.props.user_info.phone_number} errors={this.props.errors} updateValueHandler={this.props.updateFieldHandler} name="phone_number" type="text" label="Phone Number"/>
                        <div className="hidden-md-up">
                            <button onClick={this.handleSubmit.bind(this)} disabled={this.props.show_save_notice ? '' : 'disabled'} className="btn btn-info"><i className="fa fa-save"/> Save User Information</button>
                            {this.props.show_save_notice ? <div style={{color: '#3c763d', marginTop: 10, fontWeight: 'bold'}} className="change-notice">Make sure to save the updated user information.</div> : null}
                        </div>
                    </div>
                </div>
                <div className="row">
                    <div className="col-xs-12">
                        <Input value={this.props.subscribe_to_newsletter} errors={this.props.errors} updateValueHandler={this.props.updateFieldHandler} name="subscribe_to_newsletter" label="Sign me up for the newsletter to receive exclusive discounts and specials!" type="checkbox"/>
                    </div>
                </div>
            </div>
        );
    }
}