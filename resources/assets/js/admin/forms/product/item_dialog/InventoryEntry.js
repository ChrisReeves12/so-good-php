import React from 'react';

export default class InventoryEntry extends React.Component
{
    render()
    {
        return(
            <div className="inventory-entry">
                <input value={this.props.stock_location.id} defaultChecked={this.props.stock_location.active} type="checkbox"/>
                <input disabled="disabled" name="location_name" defaultValue={this.props.stock_location.name} type="text"/> <input name="quantity" defaultValue={this.props.stock_location.qty} type="text"/>
            </div>
        );
    }
}