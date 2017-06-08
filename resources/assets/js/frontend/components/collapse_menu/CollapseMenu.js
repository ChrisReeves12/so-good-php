/**
 * Class definition of CollapseMenu
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';
import MenuEntry from './MenuEntry';
import Util from '../../../core/Util';

const menu_items = require('../../menu_items');

export default class CollapseMenu extends React.Component {
    constructor(props) {
        super(props);

        this.state = {menu_items, user_data: props.user_data};
    }

    expandHandler(idx)
    {
        this.state.menu_items[idx].is_expanded = true;
        this.setState(this.state);
    }

    contractHandler(idx)
    {
        this.state.menu_items[idx].is_expanded = false;
        this.setState(this.state);
    }

    render() {

        let idx = -1;
        let menu_entries = this.state.menu_items.map(entry => {
            if(!entry.desktop_only)
            {
                idx++;
                return (
                    <MenuEntry contractHandler={this.contractHandler.bind(this)}
                               expandHandler={this.expandHandler.bind(this)} idx={idx} data={entry}/>
                );
            }
        });

        let is_logged_in = !Util.objectIsEmpty(this.state.user_data);

        return(
            <div className="collapse-menu-parent">
                <h2><i className="fa fa-user-circle"/> My Account</h2>
                {!is_logged_in && <div>
                    <div className="collapse-menu-entry">
                        <a className="link" href="/register"><i className="fa fa-edit"/> Register</a>
                    </div>
                    <div className="collapse-menu-entry">
                        <a className="link" href="/sign-in"><i className="fa fa-sign-in"/> Sign In</a>
                    </div>
                </div>}

                {is_logged_in && <div>
                    <div className="collapse-menu-entry">
                        <a className="link" href="/account"><i className="fa fa-user"/> My Account</a>
                    </div>
                    <div className="collapse-menu-entry">
                        <a className="link do-delete" href="/sign-out"><i className="fa fa-sign-out"/> Sign Out</a>
                    </div>
                </div>}
                <div className="collapse-menu-entry">
                    <a className="link gift_card_balance_link" href=""><i className="fa fa-credit-card-alt"/> Check Gift Card Balance</a>
                </div>
                <h2><i className="fa fa-tags"/> Categories</h2>
                {menu_entries}
                <h2><i className="fa fa-phone"/> Customer Service</h2>
                <div className="collapse-menu-entry">
                    <a className="link" href="/contact-us"><i className="fa fa-envelope"/> Contact Us</a>
                </div>
                <div className="collapse-menu-entry">
                    <a className="link" href="/return-policy"><i className="fa fa-truck"/> Shipping &amp; Return Policy</a>
                </div>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('side_mobile_menu');
        if(element)
            ReactDOM.render(<CollapseMenu user_data={JSON.parse(element.dataset.userData)}/>, element);
    }
}

