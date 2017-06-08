/**
 * PopupManager
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

import Popup from 'common/core/Popup';
import Util from 'common/core/Util';

export default class PopupManager
{
    register(popup_name)
    {
        $.ajax({
            url: '/popup/ajax/register',
            method: 'GET',
            data: {
                internal_name: popup_name,
                page_data: {href: window.location.href, path: window.location.pathname},
                page_title: window.sogood.page_title
                },
            timeout: 4000,
            complete: (res) => {
                if(res.status === 200)
                {
                    if(res.responseJSON.popup)
                    {
                        this.showPopup(res.responseJSON.popup);
                    }
                }
            }
        });
    }

    /**
     * Show popup
     * @param popup_data
     */
    showPopup(popup_data)
    {
        let close_button_css, window_options, server_actions;

        this.popup_data = popup_data;
        try { close_button_css = popup_data.close_button_css; } catch(e) { close_button_css = {}; }
        try { window_options = popup_data.window_options; } catch(e) { window_options = {}; }
        try { server_actions = popup_data.server_actions; } catch(e) { server_actions = {}; }

        this.popup = new Popup({
            overflow: 'none',
            useButtonBar: false,
            showCloseIcon: true,
            textAlign: !Util.objectIsEmpty(window_options) ? window_options.textAlign : Popup.get_defaults().textAlign,
            borderRadius: !Util.objectIsEmpty(window_options) ? window_options.borderRadius : Popup.get_defaults().borderRadius,
            padding: !Util.objectIsEmpty(window_options) ? window_options.padding : Popup.get_defaults().padding,
            height: !isNaN(popup_data.height) ? parseInt(popup_data.height) : 300,
            width: !isNaN(popup_data.width) ? parseInt(popup_data.width) : 400,
            closeIconCss: !Util.objectIsEmpty(close_button_css) ? close_button_css : Popup.get_defaults().closeIconCss
        });

        this.popup.show("<form name='"+popup_data.internal_name+"' class='custom-popup-form'>" + popup_data.body + "</form>");

        // Check for submit event
        if(server_actions.onSubmit)
        {
            let _self = this;

            $('body').delegate('form.custom-popup-form[name="'+popup_data.internal_name+'"]', 'submit', (e) => {
                e.preventDefault();
                let form_data = {data: $(e.target).serializeArray(), _token: Util.get_auth_token()};

                $.ajax({
                    url: server_actions.onSubmit,
                    method: 'POST',
                    data: form_data,
                    timeout: 6000,
                    dataType: 'json',
                    complete: (res) => {
                        if(res.status === 200)
                        {
                            if(res.responseJSON.response_text)
                            {
                                // Handle response text
                                let raw_second_slide_output = _self.popup_data.success_body;
                                let second_slide_output = raw_second_slide_output
                                    .replace(new RegExp(/\{\{\s*server_message\s*\}\}/, 'g'), res.responseJSON.response_text);

                                _self.popup.set_content(second_slide_output);
                            }
                        }
                    }
                });
            });
        }
    }

    static initialize()
    {
        window.sogood.PopupManager = new PopupManager();

        // Initialize frontend popups here
        if($(window).width() < 560) {
            window.setTimeout(function() { window.sogood.PopupManager.register('newsletter_sub_giveaway_mobile'); }, 1000);
        }
        else
        {
            window.setTimeout(function() { window.sogood.PopupManager.register('newsletter_sub_giveaway'); }, 1000);
        }
    }
}