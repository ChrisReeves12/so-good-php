/**
 * Represents a single form input element in a form
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';

export default class Input extends React.Component
{
    getErrors()
    {
        let ret_val = null;

        if(typeof this.props.errors !== 'undefined' && Array.isArray(this.props.errors))
        {
            let key = 0;
            let error_key = (this.props.error_key) ? this.props.error_key : this.props.name;

            ret_val = this.props.errors
                .filter((err) => err[error_key]).map((err) => {
                    key++;
                    let error_messages = err[error_key];
                    if(error_messages)
                    {
                        let key = 0;
                        let error_lines = error_messages.map((el) => {
                            key++;
                            return(<li key={key}>{el}</li>);
                        });

                        return(<ul key={key} className="errors">{error_lines}</ul>);
                    }
                });

            if(typeof ret_val[0] == 'undefined')
                ret_val = null;
        }

        return ret_val;
    }

    handleUpdateList(e)
    {
        e.preventDefault();
        let get_url = `/admin/record/update-data/${this.props.create_links.create_model}`;
        let name = this.props.create_links.name || this.props.name;
        this.props.updateListHandler(get_url, name);
    }

    handleUpdateValue(e)
    {
        this.props.updateValueHandler(e.target);
    }

    render()
    {
        let errors = this.getErrors();

        if(this.props.type === 'text' || this.props.type === 'password')
        {
            let input = null;
            if(this.props.read_only === true)
                input = <input value={this.props.value} name={this.props.name} placeholder={this.props.label} className="form-control form-input" type={this.props.type}/>;
            else if(this.props.updateValueHandler)
                input = <input onChange={this.handleUpdateValue.bind(this)} value={this.props.value} name={this.props.name} placeholder={this.props.label} className="form-control form-input" type={this.props.type}/>;
            else
                input = <input defaultValue={this.props.value} name={this.props.name} placeholder={this.props.label} className="form-control form-input" type={this.props.type}/>;

            return (
                <div className={'form-group ' + ((errors) ? 'has-danger' : '')}>
                    <label className={this.props.is_required ? 'required' : ''}>{this.props.label}</label>
                    {input}
                    {errors}
                </div>
            )
        }
        else if(this.props.type === 'select')
        {
            // Render selections
            let options = null;
            if(Array.isArray(this.props.options))
            {
                if(this.props.options.length > 0)
                {
                    options = this.props.options.map((o) => {
                        return(<option key={o.id} value={o.id}>{o.label}</option>)
                    });
                }
            }
            
            // Get create links
            let create_links = null;
            if(this.props.create_links)
            {
                create_links = (
                    <div style={{marginTop: 10}}>
                        <a href='' onClick={this.handleUpdateList.bind(this)} style={{marginRight: 20}}><i className="fa fa-refresh" />Refresh List</a>
                        <a target="_blank" href={this.props.create_links.create_url}><i className="fa fa-plus-circle" />
                        {this.props.create_links.create_label}</a>
                    </div>
                );
            }

            let input = null;
            if(this.props.updateValueHandler)
            {
                input = (
                    <select onChange={this.handleUpdateValue.bind(this)} value={this.props.value} name={this.props.name} className="form-control form-input">
                        <option value=''>Select {this.props.label}</option>
                        {options}
                    </select>
                )
            }
            else
            {
                input = (
                    <select defaultValue={this.props.value} name={this.props.name} className="form-control form-input">
                        <option>Select {this.props.label}</option>
                        {options}
                    </select>
                )
            }

            return (
                <div className={'form-group ' + ((errors) ? 'has-danger' : '')}>
                    <label className={this.props.is_required ? 'required' : ''}>{this.props.label}</label>
                    {input}
                    {create_links}
                    {errors}
                </div>
            )
        }
        else if(this.props.type === 'checkbox')
        {
            let is_checked = (this.props.value === true) ? "checked" : '';

            let input = null;
            if(this.props.updateValueHandler)
            {
                input = <input type="checkbox" name={this.props.name} onChange={this.handleUpdateValue.bind(this)} checked={is_checked} className="form-input"/>
            }
            else
            {
                input = <input type="checkbox" name={this.props.name} defaultChecked={is_checked} className="form-input"/>
            }

            return(
                <div className={'form-group ' + ((errors) ? 'has-danger' : '')}>
                    {input}
                    <label>{this.props.label}</label>
                </div>
            )
        }
        else if(this.props.type === 'textarea')
        {
            let input = null;
            if(this.props.updateValueHandler)
                input = <textarea name={this.props.name} placeholder={this.props.label} onChange={this.handleUpdateValue.bind(this)} value={this.props.value} className="form-control form-input"/>;
            else
                input = <textarea name={this.props.name} placeholder={this.props.label} defaultValue={this.props.value} className="form-control form-input"/>;


            return(
                <div className={'form-group ' + ((errors) ? 'has-danger' : '')}>
                    <label>{this.props.label}</label>
                    {input}
                    {errors}
                </div>
            )
        }
    }
}