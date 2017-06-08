import React from 'react';

export default class ProductOptions extends React.Component
{
    handleOptionChange(e)
    {
        let value = e.target.value;
        let option_name = e.target.name;
        this.props.optionChangeHandler(option_name, value);
    }

    render()
    {
        // Get the product option names
        let options_names = Object.keys(this.props.product_options);
        let options = options_names.map(option_name => {

            let option_value_lines = this.props.product_options[option_name].map(option_value => {
                return(<option key={option_value.toLowerCase()} value={option_value.toLowerCase()}>{option_value}</option>)
            });

            return (
                <div key={option_name} className={'form-group' + (this.props.errors[option_name.toLowerCase()] ? ' has-danger' : '')}>
                    <select name={option_name.toLowerCase()} onChange={this.handleOptionChange.bind(this)} value={this.props.selected_options[option_name.toLowerCase()]} className='form-control'>
                        <option value="">Select {option_name}</option>
                        {option_value_lines}
                    </select>
                    {this.props.errors[option_name.toLowerCase()] ? <span style={{color: '#bd0000', fontSize: 13}} className="form-text">{this.props.errors[option_name.toLowerCase()]}</span> : ''}
                </div>
            );
        });

        return(
            <div className="product-options-section">
                {options}
            </div>
        );
    }
}