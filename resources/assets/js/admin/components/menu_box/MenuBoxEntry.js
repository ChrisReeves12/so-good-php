import React from 'react';

export default class MenuBoxEntry extends React.Component
{
    remove()
    {
        this.props.removeEntryHandler(this.props.idx);
    }

    render()
    {
        return(
            <div className="menu-box-entry" style={{border: '1px solid #3a3a3a',backgroundColor: '#819cb5', padding: '6px 14px', borderRadius: '5px', display: 'block', marginBottom: '10px', color: 'white', position: 'relative'}}>
                <div onClick={this.remove.bind(this)} style={{display: 'inline-block', marginRight: '7px', cursor: 'pointer', color: '#ececec', fontSize: '16px'}}>
                    <i className="fa fa-times-circle"/>
                </div>
                <div style={{display: 'inline-block', fontSize: '13px', fontWeight: 'bold'}}>{ this.props.label }</div>
            </div>
        );
    }
}