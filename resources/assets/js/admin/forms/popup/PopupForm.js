/**
 * PopupForm
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

import React from 'react';
import ReactDOM from 'react-dom';
import Input from 'forms/Input';
import Util from 'common/core/Util';
import Popup from 'common/core/Popup';

export default class PopupForm extends React.Component
{
    constructor()
    {
        super();
        this.state = window.sogood.reactjs.initial_data;
        this.state.is_inactive = this.state.is_inactive || false;
        this.state.errors = [];
    }

    componentDidMount()
    {
        this.popup_body_editor = window.ace.edit('popup_body_editor');
        this.popup_body_editor.getSession().setMode('ace/mode/html');
        this.popup_body_editor.setTheme("ace/theme/github");
        this.popup_body_editor.getSession().setUseWrapMode(true);

        this.popup_success_body_editor = window.ace.edit('popup_success_body_editor');
        this.popup_success_body_editor.getSession().setMode('ace/mode/html');
        this.popup_success_body_editor.setTheme("ace/theme/github");
        this.popup_success_body_editor.getSession().setUseWrapMode(true);
        
        this.close_button_css_editor = window.ace.edit('close_button_css_editor');
        this.close_button_css_editor.getSession().setMode('ace/mode/json');
        this.close_button_css_editor.setTheme("ace/theme/github");
        this.close_button_css_editor.getSession().setUseWrapMode(true);

        this.window_options_editor = window.ace.edit('window_options_editor');
        this.window_options_editor.getSession().setMode('ace/mode/json');
        this.window_options_editor.setTheme("ace/theme/github");
        this.window_options_editor.getSession().setUseWrapMode(true);

        this.server_actions_editor = window.ace.edit('server_actions_editor');
        this.server_actions_editor.getSession().setMode('ace/mode/json');
        this.server_actions_editor.setTheme("ace/theme/github");
        this.server_actions_editor.getSession().setUseWrapMode(true);
    }

    updateValue(target)
    {
        if(target.type === 'checkbox')
        {
            this.state[target.name] = $(target).is(':checked');
            this.setState(this.state);
        }
        else
        {
            this.state[target.name] = target.value;
            this.setState(this.state);
        }
    }

    doSubmit(e)
    {
        e.preventDefault();
        this.setState({errors: []});
        let data = Object.assign({}, this.state);

        let raw_exclude_urls = $('textarea[name="exclude_urls"]').val();
        let raw_exclude_pages = $('textarea[name="exclude_pages"]').val();

        data.exclude_pages = (raw_exclude_pages) ? raw_exclude_pages.split(/,\s*/) : [];
        data.exclude_urls = (raw_exclude_urls) ? raw_exclude_urls.split(/,\s*/) : [];
        data.body = this.popup_body_editor.getValue();
        data.success_body = this.popup_success_body_editor.getValue();
        data.close_button_css = JSON.parse(this.close_button_css_editor.getValue());
        data.window_options = JSON.parse(this.window_options_editor.getValue());
        data.server_actions = JSON.parse(this.server_actions_editor.getValue());

        $.ajax({
            url: '/admin/record/Popup/' + (this.state.id || ''),
            method: (this.state.id) ? 'PUT' : 'POST',
            dataType: 'json',
            data: {data, _token: Util.get_auth_token()},
            complete: (res) => {
                if(res.status === 200)
                {
                    if(res.responseJSON.errors)
                    {
                        data.errors = res.responseJSON.errors;
                    }
                    else if(res.responseJSON.system_error)
                    {
                        let popup = new Popup();
                        popup.show(res.responseJSON.system_error);
                    }
                    else
                    {
                        window.location = '/admin/popup/' + res.responseJSON.id;
                    }

                    this.setState(data);
                }
            }
        });
    }

    processListTypeValue(value)
    {
        let ret_val = '';

        if(Array.isArray(value) && value.length > 0)
        {
            for(let x = 0; x < value.length; x++)
            {
                ret_val += value[x];
                if(x < (value.length - 1))
                {
                    ret_val += ', ';
                }
            }
        }

        return ret_val;
    }

    doPreview(e)
    {
        e.preventDefault();
        let close_button_css, window_options;

        try { close_button_css = JSON.parse(this.close_button_css_editor.getValue()); } catch(e) { close_button_css = {}; }
        try { window_options = JSON.parse(this.window_options_editor.getValue()); } catch(e) { window_options = {}; }

        let first_slide = new Popup({
            overflow: 'none',
            useButtonBar: false,
            showCloseIcon: true,
            textAlign: !Util.objectIsEmpty(window_options) ? window_options.textAlign : Popup.get_defaults().textAlign,
            borderRadius: !Util.objectIsEmpty(window_options) ? window_options.borderRadius : Popup.get_defaults().borderRadius,
            padding: !Util.objectIsEmpty(window_options) ? window_options.padding : Popup.get_defaults().padding,
            height: !isNaN(this.state.height) ? parseInt(this.state.height) : 300,
            width: !isNaN(this.state.width) ? parseInt(this.state.width) : 400,
            closeIconCss: !Util.objectIsEmpty(close_button_css) ? close_button_css : Popup.get_defaults().closeIconCss
        });

        first_slide.show("<form name='"+this.state.internal_name+"' class='custom-popup-form'>" + this.popup_body_editor.getValue() + "</form>");
    }

    render()
    {
        return(
            <div>
                <div style={{marginBottom: '32px'}}>
                    <a href="/admin/popup" className="btn btn-secondary"><i className="fa fa-pencil-square"/> Create New</a>
                    <a href="/admin/list/popup" className="btn btn-secondary"><i className="fa fa-list"/> List All Popups</a>
                </div>
                <form onSubmit={this.doSubmit.bind(this)}>
                    <Input is_required="true" updateValueHandler={this.updateValue.bind(this)} name="name" errors={this.state.errors} value={this.state.name} label="Name" type="text"/>
                    <Input is_required="true" updateValueHandler={this.updateValue.bind(this)} name="internal_name" errors={this.state.errors} value={this.state.internal_name} label="Internal Name" type="text"/>
                    <Input is_required="true" updateValueHandler={this.updateValue.bind(this)} name="cookie_name" errors={this.state.errors} value={this.state.cookie_name} label="Cookie Name" type="text"/>
                    <Input is_required="true" updateValueHandler={this.updateValue.bind(this)} name="cookie_day_life" errors={this.state.errors} value={this.state.cookie_day_life} label="Cookie Day Life" type="text"/>
                    <Input is_required="true" updateValueHandler={this.updateValue.bind(this)} name="width" errors={this.state.errors} value={this.state.width} label="Width (pixels)" type="text"/>
                    <Input is_required="true" updateValueHandler={this.updateValue.bind(this)} name="height" errors={this.state.errors} value={this.state.height} label="Height (pixels)" type="text"/>

                    <div className="form-group">
                        <label>Excluded URLs</label>
                        <textarea defaultValue={this.processListTypeValue(this.state.exclude_urls)} name="exclude_urls" className="form-control"/>
                    </div>

                    <div className="form-group">
                        <label>Excluded Pages</label>
                        <textarea defaultValue={this.processListTypeValue(this.state.exclude_pages)} name="exclude_pages" className="form-control"/>
                    </div>

                    <div className="form-group">
                        <label>Close Button CSS</label>
                        <div style={{width: '100%', height: 300, border: '1px solid #bfbfbf', marginBottom: 15}}
                             id="close_button_css_editor">{(this.state.close_button_css) ? JSON.stringify(this.state.close_button_css, null, "\t") : ''}</div>
                    </div>

                    <div className="form-group">
                        <label>Server Actions</label>
                        <div style={{width: '100%', height: 300, border: '1px solid #bfbfbf', marginBottom: 15}}
                             id="server_actions_editor">{(this.state.server_actions) ? JSON.stringify(this.state.server_actions, null, "\t") : ''}</div>
                    </div>

                    <div className="form-group">
                        <label>Window Options</label>
                        <div style={{width: '100%', height: 300, border: '1px solid #bfbfbf', marginBottom: 15}}
                             id="window_options_editor">{(this.state.window_options) ? JSON.stringify(this.state.window_options, null, "\t") : ''}</div>
                    </div>

                    <div className="form-group">
                        <label>Body</label>
                        <div style={{width: '100%', height: 300, border: '1px solid #bfbfbf', marginBottom: 15}} id="popup_body_editor">{this.state.body}</div>
                    </div>

                    <div className="form-group">
                        <label>Success Body</label>
                        <div style={{width: '100%', height: 300, border: '1px solid #bfbfbf', marginBottom: 15}} id="popup_success_body_editor">{this.state.success_body}</div>
                    </div>

                    <Input name="exclude_newsletter_subs" errors={this.state.exclude_newsletter_subs}
                           updateValueHandler={this.updateValue.bind(this)}
                           value={this.state.exclude_newsletter_subs} label="Exclude Newsletter Subs?" type="checkbox"/>

                    <Input name="exclude_regged_users" errors={this.state.exclude_regged_users}
                           updateValueHandler={this.updateValue.bind(this)}
                           value={this.state.exclude_regged_users} label="Exclude Registered Users?" type="checkbox"/>

                    <Input name="is_inactive" errors={this.state.is_inactive}
                           updateValueHandler={this.updateValue.bind(this)}
                           value={this.state.is_inactive} label="Is Inactive?" type="checkbox"/>

                    <button className="btn btn-success"><i className="fa fa-save"/> Save Popup</button>
                    <button onClick={this.doPreview.bind(this)} className="btn btn-info"><i className="fa fa-eye"/> Preview Popup</button>
                </form>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('popup_form');

        if(element)
        {
            ReactDOM.render(<PopupForm/>, element);
        }
    }
}
