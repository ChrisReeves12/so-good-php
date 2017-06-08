/**
 * Class definition of MenuItem
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';

export default class MenuItem extends React.Component {
    constructor(props) {
        super(props);
    }

    handleShowChildren()
    {
        this.props.expandHandler(this.props.idx);
    }

    handleHideChildren()
    {
        this.props.collapseHandler(this.props.idx);
    }

    render() {
        let has_children = (Array.isArray((this.props.data.children)) && this.props.data.children.length > 0);
        let children_lines = null;
        if(has_children)
        {
            children_lines = this.props.data.children.map(child_item => {
                return(
                    <li key={child_item.href}>
                        <a href={child_item.href}>{child_item.label}</a>
                    </li>
                )
            });
        }

        return (
            <li onMouseLeave={this.handleHideChildren.bind(this)} className={this.props.data.is_expanded ? 'expanded' : ''}>
                <p>
                    <a href={this.props.data.href}>{this.props.data.label}</a>
                    {has_children && <i onMouseEnter={this.handleShowChildren.bind(this)} className="fa fa-chevron-down"/>}
                    {this.props.data.is_expanded &&
                    <div className="sub-menu-items">
                        <ul>
                            {children_lines}
                        </ul>
                    </div>}
                </p>
            </li>
        );
    }
}