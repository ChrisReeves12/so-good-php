import React from 'react';

export default class AttributeEntry extends React.Component
{
    handleRemove(e)
    {
        e.preventDefault();
        this.props.removeItemAttribute(this.props.idx, this.props.item_idx);
    }

    render()
    {
        return(
            <div className="attribute-entry">
                <a onClick={this.handleRemove.bind(this)} href=""><i href="" className="fa fa-times-circle"/></a> <input defaultValue={this.props.detail.key} name="attribute_name" type="text"/> <input defaultValue={this.props.detail.value} name="attribute_value" type="text"/>
            </div>
        );
    }
}