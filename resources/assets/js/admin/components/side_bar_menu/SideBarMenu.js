/**
 * The sidebar menu
 * @author Christopher Reeves
 */

import React from 'react';
import ReactDOM from 'react-dom';
import SideBarEntry from './SideBarEntry';
import SideBarLinkConfig from '../../config/SideBarLinkConfig';

export default class SideBarMenu extends React.Component
{
    componentWillMount()
    {
        this.setState({
            entries: SideBarLinkConfig
        });
    }

    doExpandEntry(entry)
    {
        this.setState({
            entries: this.state.entries.map((e) => {
                e.expanded = (e.label == entry.props.label);
                return e;
            })
        });
    }

    doCollapseEntry()
    {
        this.setState({
            entries: this.state.entries.map((e) => {
                e.expanded = false;
                return e;
            })
        });
    }

    renderEntries()
    {
        return(this.state.entries.map((entry) => {
            return(
                <SideBarEntry
                    key={entry.label}
                    icon={entry.icon}
                    children={entry.children}
                    label={entry.label}
                    expanded={entry.expanded}
                    doExpandEntry={this.doExpandEntry.bind(this)}
                    doCollapseEntry={this.doCollapseEntry.bind(this)}
                />
            );
        }));
    }

    render()
    {
        return(
            <aside className="admin-sidebar">
                {this.renderEntries()}
            </aside>
        );
    }

    static initialize()
    {
        let element = document.getElementById('side_menu_bar');
        if(element)
        {
            ReactDOM.render(<SideBarMenu/>, element);
        }
    }
}