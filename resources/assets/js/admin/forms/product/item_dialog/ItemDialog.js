import React from 'react';
import InventoryEntry from './InventoryEntry';
import AttributeEntry from './AttributeEntry';
import Util from '../../../../core/Util';

export default class ItemDialog extends React.Component
{
    preparePhotoUpload(e)
    {
        this.temp_files = e.target.files;
    }

    handleSavingItem(e)
    {
        e.preventDefault();
        let item_element = $(e.target).parents('.item');
        let properties_element = $(item_element).find('.item-attributes');
        let property_inputs = properties_element.find(':input');
        let properties = {is_default: false, image: {}, product_id: this.props.product_id};

        // Get general properties
        for(let property_input of property_inputs)
        {
            let name = $(property_input).attr('name');
            if(name)
            {
                let value = null;
                if($(property_input).is(':checkbox'))
                    value = $(property_input).is(':checked');
                else
                    value = $(property_input).val();

                properties[name] = value;
            }
        }

        // Get inventory section information
        let inventory_data = [];
        let inventory_section_element = $(e.target).parents('.item').find('.inventory-section');
        let inventory_entries = inventory_section_element.find('.inventory-entry');

        for(let inventory_entry of inventory_entries)
        {
            let checkbox = $(inventory_entry).find(':checkbox');
            let stock_location_id = checkbox.attr('value');
            let is_checked = checkbox.is(':checked');
            let quantity = $(inventory_entry).find('input[name="quantity"]').val();
            let name = $(inventory_entry).find('input[name="location_name"]').val();
            inventory_data.push({active: is_checked, qty: quantity, name: name, id: stock_location_id});
        }

        properties.stock_locations = inventory_data;

        // Get item attributes (details)
        let attribute_data = [];
        let attribute_section_element = $(e.target).parents('.item').find('.attributes-section');
        let attribute_entries = attribute_section_element.find('.attribute-entry');
        for(let attribute_entry of attribute_entries)
        {
            let attribute_name = $(attribute_entry).find('input[name="attribute_name"]').val();
            attribute_data.push({key: attribute_name, value: $(attribute_entry).find('input[name="attribute_value"]').val()});
        }

        properties.details = attribute_data;
        let is_updating = this.props.item_data.saved;

        this.props.saveHandler(properties, is_updating, this, this.props.idx);
    }

    handleUploadImage(e)
    {
        e.preventDefault();
        this.props.uploadItemImage(this.props.idx, this.temp_files, this.props.item_data.saved);
    }

    renderFieldError(error_array)
    {
        if(Array.isArray(error_array))
        {
            let error_lines = null;
            if(error_array.length > 0)
            {
                error_lines = error_array.map((err) => { return(<li>{err}</li>); });
                return(<ul className="field-error">{error_lines}</ul>);
            }
        }
    }

    handleDelete(e)
    {
        e.preventDefault();
        this.props.deleteHandler(this.props.idx, this);
    }

    handleAddAttribute(e)
    {
        e.preventDefault();
        this.props.addAttribute(this);
    }

    handleDeleteImage(e)
    {
        e.preventDefault();
        this.props.deleteItemImage(this.props.idx);
    }

    handleCreateDuplicate(e)
    {
        e.preventDefault();
        this.props.createDuplicateHandler(this.props.idx);
    }

    render()
    {
        let has_error = false;
        if(this.props.errors)
        {
            has_error = (this.props.item_data.id == this.props.errors.item.props.item_data.id);
        }

        // Control rendering of photo
        let main_photo = null, id = null, inventory_entries = null, attribute_entries = null;

        if(this.props.item_data.view_image && !Util.objectIsEmpty(this.props.item_data.view_image))
            main_photo = (<a className="item-image" href={this.props.item_data.view_image.url}><img src={this.props.item_data.view_image.url}/></a>);
        else
            main_photo = (<div className="no-image-notice">No Image</div>);


        // Control showing of ID
        if(this.props.item_data.id && this.props.item_data.saved)
            id = <div style={{position: 'absolute', fontSize: '14px', top: '10px', right: '10px', zIndex: 1000}}>ID: {this.props.item_data.id}</div>;

        // Show inventory entries
        if(Array.isArray(this.props.item_data.stock_locations) && this.props.item_data.stock_locations.length > 0)
        {
            inventory_entries = this.props.item_data.stock_locations.map((sl) => {
                return(<InventoryEntry key={sl.id} stock_location={sl}/>);
            });
        }

        // Show attribute entries
        if(Array.isArray(this.props.item_data.details) && this.props.item_data.details.length > 0)
        {
            let idx = -1;
            attribute_entries = this.props.item_data.details.map((d) => {
                idx++;
                return(<AttributeEntry item_idx={this.props.idx} removeItemAttribute={this.props.removeItemAttribute} idx={idx} key={d.id} detail={d}/>);
            });
        }

        // Create errors object
        let error_data = {};
        if(has_error)
        {
            for(let error of this.props.errors.errors)
            {
                let error_field = Object.getOwnPropertyNames(error)[0];
                error_data[error_field] = error[error_field];
            }
        }

        return(
            <div className={"row item " + (has_error ? 'error-item' : '')}>
                {id}
                <a href="" onClick={this.handleDelete.bind(this)} className="close">
                    <i className="fa fa-times-circle"/>
                </a>
                <div className="col-xs-12">
                    <div className="row">
                        <div className="col-xs-2">
                            {main_photo}
                            <label>Browse Item Image</label>
                            <input onChange={this.preparePhotoUpload.bind(this)} type="file"/>
                            <button onClick={this.handleUploadImage.bind(this)} className="btn btn-success"><i className="fa fa-upload"/> Upload File</button>
                            {this.props.item_data.view_image && !Util.objectIsEmpty(this.props.item_data.view_image) ?
                                <button onClick={this.handleDeleteImage.bind(this)} className="btn btn-danger"><i className="fa fa-times"/> Delete Image</button>
                                : null}
                        </div>
                        <div className="col-xs-4">
                            <h4>Inventory</h4>
                            <div className="inventory-section">{inventory_entries}</div>
                            {this.renderFieldError(error_data['stock_locations'])}
                        </div>
                        <div className="col-xs-4">
                            <h4 style={{display: 'inline-block'}}>Attributes</h4> <button onClick={this.handleAddAttribute.bind(this)} style={{display: 'inline-block'}} className="btn btn-info"><i className="fa fa-plus-square"/> Add Attribute</button>
                            <div className="attributes-section">{attribute_entries}</div>
                            {this.renderFieldError(error_data['details'])}
                        </div>
                    </div>
                    <div className="row item-attributes">
                        <input name="id" defaultValue={this.props.item_data.id} type="hidden"/>
                        <div className="col-xs-3">
                            <label>List Price</label><br/>
                            <input name="list_price" defaultValue={this.props.item_data.list_price} type="text"/>
                            {this.renderFieldError(error_data['list_price'])}
                        </div>
                        <div className="col-xs-3">
                            <label>Cost</label><br/>
                            <input name="cost" defaultValue={this.props.item_data.cost} type="text"/>
                            {this.renderFieldError(error_data['cost'])}
                        </div>
                        <div className="col-xs-3">
                            <label>Store Price</label><br/>
                            <input name="store_price" defaultValue={this.props.item_data.store_price} type="text"/>
                            {this.renderFieldError(error_data['store_price'])}
                        </div>
                        <div className="col-xs-3">
                            <label>Sku Number</label><br/>
                            <input name="sku" defaultValue={this.props.item_data.sku} type="text"/>
                            {this.renderFieldError(error_data['sku'])}
                        </div>
                        <div className="col-xs-3">
                            <label>Weight</label><br/>
                            <input name="weight" defaultValue={this.props.item_data.weight} type="text"/>
                            {this.renderFieldError(error_data['weight'])}
                        </div>
                        <div className="col-xs-3">
                            <label>UPC</label><br/>
                            <input name="upc" defaultValue={this.props.item_data.upc} type="text"/>
                            {this.renderFieldError(error_data['upc'])}
                        </div>
                        <div className="col-xs-3">
                            <label>EAN</label><br/>
                            <input name="ean" defaultValue={this.props.item_data.ean} type="text"/>
                            {this.renderFieldError(error_data['ean'])}
                        </div>
                        <div className="col-xs-3">
                            <label>ISBN</label><br/>
                            <input name="isbn" defaultValue={this.props.item_data.isbn} type="text"/>
                            {this.renderFieldError(error_data['isbn'])}
                        </div>
                        <div className="col-xs-3">
                            <label>Stock Status</label><br/>
                            <select name="calculated_stock_status" defaultValue={this.props.item_data.calculated_stock_status} disabled="disabled">
                                <option value="in_stock">In Stock</option>
                                <option value="out_of_stock">Out Of Stock</option>
                            </select>
                            {this.renderFieldError(error_data['calculated_stock_status'])}
                        </div>
                        <div className="col-xs-3">
                            <label>Stock Status Override</label><br/>
                            <select name="stock_status_override" defaultValue={this.props.item_data.stock_status_override}>
                                <option value="none">None</option>
                                <option value="in_stock">In Stock</option>
                                <option value="out_of_stock">Out Of Stock</option>
                            </select>
                            {this.renderFieldError(error_data['stock_status_override'])}
                        </div>
                        <div className="col-xs-3">
                            <input name="is_inactive" defaultChecked={this.props.item_data.is_inactive} type="checkbox"/> Is inactive?
                            {this.renderFieldError(error_data['is_inactive'])}
                        </div>
                        <div className="col-xs-3">
                            <input name="ships_alone" defaultChecked={this.props.item_data.ships_alone} type="checkbox"/> Ships alone?
                            {this.renderFieldError(error_data['ships_alone'])}
                        </div>
                        <div className="col-xs-3">
                            <button onClick={this.handleSavingItem.bind(this)} className="btn btn-success"><i style={{marginRight: '4px'}} className="fa fa-save"/>
                                {this.props.item_data.saved ? 'Update Item' : 'Save Item'}
                            </button>
                        </div>
                        <div className="col-xs-3">
                            <button onClick={this.handleCreateDuplicate.bind(this)} className="btn btn-secondary"><i className="fa fa-copy"/> Duplicate</button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}