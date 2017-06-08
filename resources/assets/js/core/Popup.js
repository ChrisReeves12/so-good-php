/**
 * Manages popups
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

/**
 * Popup class
 * @param options
 * @constructor
 */
export default class Popup
{
    constructor(options)
    {
        // Initialize settings
        this._defaults = Popup.get_defaults();

        this._initialized = false;
        this._popup_showing = false;
        this._button_bar = null;
        this._backdrop = null;
        this._popup_window = null;

        this._init(options);
    }

    static get_defaults()
    {
        return {
            width: 415,
            height: 120,
            padding: 32,
            overflow: 'scroll',
            topPosition: 100,
            backdropSpeed: 400,
            useButtonBar: true,
            color: 'black',
            popupSpeed: 300,
            borderRadius: 5,
            textAlign: 'center',
            fontSize: '15px',
            backgroundColor: 'white',
            fadeOutSpeed: 100,
            boxShadow: 'rgba(0,0,0,0.6) -2px 4px 5px',
            showCloseIcon: false,
            closeIconCss: {
                zIndex: 1500,
                top: '2px',
                right: '8px',
                fontSize: '20px',
                color: '#bfbfbf',
                position: 'absolute'
            },
            backdropOpacity: 0.5,
            buttonBarCloseButtonContents: "<i class='fa fa-times-circle'></i> Close"
        };
    }

    /**
     * @param name
     * @param value
     * @param reset_window
     */
    set_option(name, value, reset_window)
    {
        this._options[name] = value;

        if(reset_window)
        {
            this._initialized = false;

            if(this._backdrop)
                this._backdrop.remove();
            if(this._popup_window)
                this._popup_window.remove();
            if(this._button_bar)
                this._button_bar.remove();

            this._init(this._options);
        }
    }

    set_content(content)
    {
        if(this._popup_showing)
        {
            this._popup_window.find('.content').html(content);
        }
    }

    /**
     * Show the popup
     */
    show(content)
    {
        if(!this._popup_showing)
        {
            this._popup_showing = true;
            this._popup_window.find('.content').html(content);

            if(this._options.useButtonBar)
            {
                this._button_bar.find('button[data-button="close"]').click(e => {
                    e.preventDefault();
                    this.close();
                });
            }

            $('body').prepend(this._backdrop).prepend(this._popup_window);

            const popup_width = this._popup_window.width();
            const screen_width = $(document).width();

            const left_position = parseInt(screen_width / 2) - parseInt(popup_width / 2);
            this._popup_window.css('left', left_position);

            this._popup_window.css('opacity', 0);
            this._backdrop.css('opacity', 0);
            this._popup_window.css('display', 'block');
            this._backdrop.css('display', 'block');

            // Fade in backdrop
            $(this._backdrop).animate({opacity: this._options.backdropOpacity}, this._options.backdropSpeed)
                .promise()
                .then(() => {
                    $(this._popup_window).animate({opacity: 1}, this._options.popupSpeed);
                });
        }
    }

    /**
     * Closes the popup window
     */
    close()
    {
        if(this._popup_showing)
        {
            this._popup_window.fadeOut(this._options.fadeOutSpeed, () => {

                // Remove backdrop
                this._backdrop.fadeOut(this._options.fadeOutSpeed, () => {
                    this._popup_showing = false;
                });
            });
        }
    }

    /**
     * Initializes the popup
     * @param options
     * @private
     */
    _init(options)
    {
        if(!this._initialized)
        {
            this._options = $.extend(true, {}, this._defaults, options);
            this._build_backdrop();
            this._build_popup_window();
            this._initialized = true
        }
    }

    /**
     * Create build backdrop
     * @private
     */
    _build_backdrop()
    {
        this._backdrop = $("<div></div>");

        this._backdrop.css({
            backgroundColor: 'black',
            width: '100%',
            height: '100%',
            position: 'fixed',
            zIndex: 1000,
            opacity: 0.0,
            top: 0,
            left: 0
        });
    }

    /**
     * Create the popup window modal
     * @private
     */
    _build_popup_window()
    {
        // Build container to house buttons for popup
        if(this._options.useButtonBar)
        {
            this._button_bar = $("<div style='text-align: center; padding-top: 20px;' class='button_bar'></div>");
            this._button_bar.append("<button class='btn btn-success' data-button='close'>" + this._options.buttonBarCloseButtonContents + "</button>");
        }

        this._popup_window = $("<div class='_popup_window'><div class='content'></div></div>");

        this._popup_window
            .css({
                backgroundColor: this._options.backgroundColor,
                borderRadius: this._options.borderRadius,
                width: this._options.width,
                position: 'fixed',
                padding: this._options.padding,
                zIndex: 1400,
                opacity: 0,
                top: this._options.topPosition,
                left: 0,
                boxShadow: this._options.boxShadow
            });

        if(this._options.useButtonBar)
        {
            this._popup_window.append(this._button_bar);
        }

        if(this._options.showCloseIcon)
        {
            let close_button = $("<a href=''><i class='fa fa-times'></i></a>").css(this._options.closeIconCss);

            close_button.on('click', (e) => {
                e.preventDefault();
                this.close();
            });

            this._popup_window.append(close_button);
        }

        // Adjust CSS for content area
        this._popup_window.find('.content').css({
            minHeight: this._options.height,
            maxHeight: 750,
            overflow: this._options.overflow,
            color: this._options.color,
            fontSize: this._options.fontSize,
            textAlign: this._options.textAlign
        });
    }
}