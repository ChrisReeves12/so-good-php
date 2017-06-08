/**
 * Class definition of HeaderNavMenu
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';
import MenuItem from './MenuItem';
const menu_items = require('../../menu_items');

export default class HeaderNavMenu extends React.Component {
    constructor(props) {
        super(props);

        this.state = {menu_entries: menu_items}
    }

    expandMenuItem(idx)
    {
        this.state.menu_entries[idx].is_expanded = true;
        this.setState(this.state);
    }

    collapseMenuItem(idx)
    {
        this.state.menu_entries[idx].is_expanded = false;
        this.setState(this.state);
    }

    render() {
        let idx = -1;
        let menu_lines = this.state.menu_entries.map(menu_item => {
            if(!menu_item.mobile_only)
            {
                idx++;
                return (<MenuItem key={menu_item.href} collapseHandler={this.collapseMenuItem.bind(this)}
                                  expandHandler={this.expandMenuItem.bind(this)} idx={idx} data={menu_item}/>);
            }
        });

        return (
            <ul>{menu_lines}</ul>
        );
    }

    static initialize()
    {
        let element = document.getElementById('header_nav_menu');
        if(element)
            ReactDOM.render(<HeaderNavMenu/>, element);
    }
}