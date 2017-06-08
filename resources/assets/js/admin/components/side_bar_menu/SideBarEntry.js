/**
 * An entry of the side bar menu
 * @author Christopher Reeves
 */

import React from 'react';

export default class SideBarEntry extends React.Component
{
    handleExpandEntry()
    {
        this.props.doExpandEntry(this);
    }

    handleCollapse()
    {
        this.props.doCollapseEntry();
    }

    renderInnerLinks()
    {
        let inner_links = this.props.children.map((c) => {
            
            let icon = '/assets/img/layout/admin/' + c.icon + '_icon.png';
            return(
                <li key={c.label}>
                    <img src={icon}/> <a href={c.url}>{c.label}</a>
                </li>
            )
        });
        
        if(this.props.children.length > 0 && this.props.expanded)
        {
            return(
                <div class="side-bar-entry-links">
                    <ul>{inner_links}</ul>
                </div>
            );
        }
    }

    render()
    {
        let icon = '/assets/img/layout/admin/' + this.props.icon + '_icon.png';
        let expand_arrow = (!this.props.expanded && this.props.children.length > 0) ?
            <div onMouseEnter={this.handleExpandEntry.bind(this)} className="expand-arrow"><img src="/assets/img/layout/admin/right_arrow.png"/></div>
            : null;

        return(
            <div onMouseLeave={this.handleCollapse.bind(this)} className="side-bar-entry">
                <img className="icon" src={icon}/>
                <div className="label">{this.props.label}</div>
                {expand_arrow}
                {this.renderInnerLinks()}
            </div>
        );
    }
}