/**
 * GiftCardBalanceModal
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

export default class GiftCardBalanceModalInitializer
{
    static initialize()
    {
        $('.gift_card_balance_link').on('click', (e) => {
            e.preventDefault();
            $(document).trigger('gift-card-check-modal');
            $('#modal_gift_card_balance').modal('show');
        });
    }
}
