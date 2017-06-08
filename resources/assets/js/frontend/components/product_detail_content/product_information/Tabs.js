import React from 'react';

export default class Tabs extends React.Component
{
    handleTabChange(e)
    {
        e.preventDefault();
        this.props.tabChangeHandler(e.target.dataset.idx);
    }

    render()
    {
        let idx = -1;
        let tabs = this.props.tabs.map(tab => {
            idx++;
            return(
                <li key={tab.name.toLowerCase()}>
                    <a href="" data-name={tab.name} data-idx={idx} onClick={this.handleTabChange.bind(this)}
                       className={'tab ' + (tab.is_active ? 'selected' : '')}>
                        {tab.name}
                    </a>
                </li>
            );
        });

        return(
            <div className="tabs">
                <ul>
                {tabs}
                </ul>
            </div>
        );
    }
}