import Util from '../core/Util';
import Popup from '../core/Popup';

// Initializers
$(document).ready(function() {

    // Add event listener to special links
    $('a[data-http-method]').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            method: $(this).data('http-method'),
            //url: $(this).attr('href') + '?_token=' + encodeURIComponent(Util.get_auth_token()),
            url: $(this).attr('href'),
            data: {_token: Util.get_auth_token()},
            dataType: 'json',
            success: function(msg) {
                if(!msg.system_error) {
                    window.location.reload();
                }
                else
                {
                    let popup = new Popup();
                    popup.show(msg.system_error);
                }
            }
        });
    });
});