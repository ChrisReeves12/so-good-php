/**
 * Javascript widget to insert key values for user displays
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import KeyValEntry from './KeyValEntry';

const random_string = require('randomstring');

export default class KeyValBox extends React.Component
{
    constructor(props)
    {
        super(props);
        this.state = props;
    }

    updateEntry(idx, key_name, value)
    {
        let values = this.state.values.slice();
        values[idx].key = key_name;
        values[idx].value = value;
        this.setState({values});
    }

    removeKeyVal(idx)
    {
        let values = this.state.values.slice();
        values.splice(idx, 1);
        this.setState({values});
    }

    renderEntries()
    {
        if(Array.isArray(this.state.values) && this.state.values.length > 0)
        {
            let idx = -1;
            return this.state.values.map((v) => {
                idx++;
                return(<KeyValEntry key_choices={this.props.key_choices}
                                    idx={idx} valueChangeHandler={this.updateEntry.bind(this)}
                                    removeKeyVal={this.removeKeyVal.bind(this)} key={v.id} id={v.id}
                                    key_name={v.key} value={v.value}/>);
            });
        }
    }

    addKeyVal(e)
    {
        e.preventDefault();
        let values = this.state.values.slice();

        let id = null;
        while(id == null)
        {
            let test_id = random_string.generate(100);
            if(values.filter((v) => (v.id == test_id)).length == 0)
                id = test_id;
        }

        values.push({key: '', value: '', id: id});
        this.setState({values});
    }

    render()
    {
        let output_values = null;

        if(Array.isArray(this.state.values) && this.state.values.length > 0)
        {
            output_values = this.state.values.map((v) => {
                return {key: v.key, value: v.value};
            });
        }

        return(
            <div style={{border: '1px solid #CCCCCC', minHeight: '100px', padding: '4px', backgroundColor: '#f8f8f8'}}>
                {this.renderEntries()}
                <div onClick={this.addKeyVal.bind(this)} style={{backgroundColor: '#9fc1a0', cursor: 'pointer', padding: '3px', height: '50px', textAlign: 'center', color: 'white', fontWeight: 'bold', lineHeight: '41px', fontSize: '16px'}}>
                    <i className="fa fa-plus-circle"/> Add Item
                </div>
                <input name={this.props.name} className="form-input" type="hidden" value={JSON.stringify(output_values)}/>
            </div>
        );
    }
}