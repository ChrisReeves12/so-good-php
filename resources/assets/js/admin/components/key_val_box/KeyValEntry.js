import React from 'react';

export default class KeyValEntry extends React.Component
{
    remove()
    {
        this.props.removeKeyVal(this.props.idx);
    }

    handleChange(e)
    {
        let entry_element = $(e.target).parents('.key-val-entry');
        let key_name = entry_element.find(':input[name="key_name"]').val();
        let value = entry_element.find(':input[name="value"]').val();
        this.props.valueChangeHandler(this.props.idx, key_name, value);
    }

    render()
    {
        let key_options = [];
        if(Array.isArray(this.props.key_choices) && this.props.key_choices.length > 0)
        {
            key_options = this.props.key_choices.map(choice => {
                return(<option value={choice.key}>{choice.value}</option>);
            });
        }

        return(
            <div className="key-val-entry" style={{backgroundColor: '#dadada', height: '36px', padding: '4px 0 0 4px', marginBottom: '2px'}}>
                <div onClick={this.remove.bind(this)} style={{backgroundColor: '#fb8585', color: 'white', display: 'inline-block', cursor: 'pointer', padding: '3px', borderRadius: '3px'}}>
                    <i className="fa fa-times-circle"/>
                </div>
                <div style={{width: '45%', display: 'inline-block', verticalAlign: 'top'}}>
                    {key_options.length == 0 && <input name="key_name" type="text" onChange={this.handleChange.bind(this)} value={this.props.key_name}
                            style={{width: '100%', marginLeft: '3px', padding: '5px 9px', border: 'none', borderRadius: '3px', fontSize: '13px'}}/>}

                    {key_options.length > 0 && <select name="key_name" onChange={this.handleChange.bind(this)} value={this.props.key_name}
                                                       style={{width: '100%', marginLeft: '3px', padding: '5px', height: '28px', border: 'none', borderRadius: '3px', fontSize: '13px'}}>
                        {key_options}
                    </select>}
                </div>
                <div style={{width: '45%', display: 'inline-block', verticalAlign: 'top'}}>
                    <input name="value" type="text" onChange={this.handleChange.bind(this)} value={this.props.value} style={{width: '100%', marginLeft: '7px', padding: '5px 9px', border: 'none', borderRadius: '3px', fontSize: '13px'}}/>
                </div>
            </div>
        );
    }
}