import Popup from '../../../../app/assets/javascript/core/Popup';

export default class DeleteLinkHandler
{
    // Initialize delete buttons
    static initialize()
    {
        $('a.do-delete').on('click', (e) => {
            e.preventDefault();
            $.ajax({
                url: e.target.href,
                method: 'DELETE',
                timeout: 4000,
                dataType: 'json',
                data: {authenticity_token: Util.get_auth_token()},
                complete: (res) => {
                    if(res.status == 200)
                    {
                        window.location.reload();
                    }
                    else if(res.status == 0)
                    {
                        let popup = new Popup();
                        popup.show('Timed out while performing operation, please try again.');
                    }
                }
            });
        });
    }
}