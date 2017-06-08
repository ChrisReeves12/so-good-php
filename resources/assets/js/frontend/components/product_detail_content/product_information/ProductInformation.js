import React from 'react';
import Tabs from './Tabs';
import TabContentListRenderer from './TabContentListRenderer';
import TabContentTextRenderer from './TabContentTextRenderer';

export default class ProductInformation extends React.Component
{
    constructor()
    {
        super();

        // Map of various renderers to use for each tab content type
        this.renderers = {
            'list': TabContentListRenderer,
            'text': TabContentTextRenderer
        };
    }

    renderContent()
    {
        // Find the active tab
        let active_tab = this.props.tabs.find(tab => tab.is_active);
        let renderer = this.renderers[active_tab.render_method];
        return renderer.render(active_tab.content);
    }

    render()
    {
        return(
            <div className="product-information-section">
                <Tabs tabChangeHandler={this.props.tabChangeHandler} tabs={this.props.tabs}/>
                <div className="product-info-tab-content">
                    {this.renderContent()}
                </div>
            </div>
        );
    }
}