/**
 * Class definition of MenuEntry
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';

export default class MenuEntry extends React.Component {
    constructor(props) {
        super(props);
    }

    handleExpand(e)
    {
        e.preventDefault();
        this.props.expandHandler(this.props.idx);
    }

    handleContract(e)
    {
        e.preventDefault();
        this.props.contractHandler(this.props.idx);
    }

    render()
    {
        let entry = this.props.data;
        let child_entries = [];

        if((Array.isArray(entry.children)) && entry.children.length > 0)
        {
            child_entries = entry.children.map(child => {
                return(
                    <div className="collapse-menu-entry-child">
                        <a className="link" href={child.href}>{child.label}</a>
                    </div>
                );
            });
        }

        return(
            <div>
                <div className="collapse-menu-entry">
                    <a className="link" href={entry.href}>{entry.label}</a>
                    {(Array.isArray(entry.children) && entry.children.length > 0 && !entry.is_expanded) &&
                        <a onClick={this.handleExpand.bind(this)} className="expand-contract" href=""><i className="fa fa-plus-circle"/></a>}
                    {(Array.isArray(entry.children) && entry.children.length > 0 && entry.is_expanded) &&
                    <a onClick={this.handleContract.bind(this)} className="expand-contract" href=""><i className="fa fa-minus-circle"/></a>}
                </div>
                {entry.is_expanded && child_entries}
            </div>
        );
    }
}