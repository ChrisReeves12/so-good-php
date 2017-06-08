import React from 'react';
import MenuBoxEntry from '../menu_box/MenuBoxEntry';

export default class MenuBox extends React.Component
{
    renderEntries()
    {
        let idx = -1;
        return this.props.entries.map((entry) => {
            idx++;
            return(<MenuBoxEntry idx={idx} removeEntryHandler={this.props.removeEntryHandler} key={entry.id} id={entry.id} label={entry.label}/>);
        });
    }

    render()
    {
        let menu_box_name = "menu_box_" + this.props.name;
        
        return(
            <div className="menu-box-frame" style={{border: '1px solid #CCCCCC', borderRadius: '3px', padding: '10px', backgroundColor: '#e8f4ff', minHeight: '200px'}}>
                {this.renderEntries()}
                <input name={menu_box_name} type="hidden" className="form-input" value={JSON.stringify(this.props.entries)}/>
            </div>
        );
    }
}