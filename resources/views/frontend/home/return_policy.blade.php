@extends('frontend.layout')
@section('content')
    <div class="container return-policy">
        <h1>Return Policy</h1>
        <p class="top-paragraph">
            Please note that when making an order with So Good BB, you are agreeing that you understand our Terms of Use as well as our Return Policy.
        </p>
        <div class="return-bullet-points">
            <ul>
                <li>
                    <strong>All sales are final. No returns or exchanges.</strong> We will only accept returns if you received an incorrect or defective item.
                    In the event that you receive an incorrect or defective item, please contact us at {{ business('store_email') }} within 48 hours of the item being delivered.
                    In the email, please provide your name, order number, and an image of the incorrect or defective item.
                </li>
                <li>
                    We do not accept any return requests after 48 hours of the package being delivered; you must contact us within this time period
                </li>
                <li>
                    Qualifying items must be in new condition and in the original packaging with no signs of being displayed or worn.
                    This includes smells of perfume, oils, pet odor or smoke.
                </li>
                <li>
                    We must receive the return items <span style="font-style: italic;">within 7 days of the return request</span>.
                </li>
                <li>
                    Any returned item that was not approved will be sent back to the customer and shipping will not be credited.
                </li>
                <li>
                    We will replace any return item with the same exact item. If the item is not available, we will provide a refund for the item.
                </li>
                <li>
                    In any situation, we shall not send a replacement until the return item arrives to the store and is approved.
                </li>
                <li>
                    Return policy is subject to change without notice.
                </li>
            </ul>
        </div>
        <h1>Shipping</h1>
        <div class="shipping-bullet-points">
            <ul>
                <li>
                    All orders will be shipped within 3 business days (Mon -Fri).
                </li>
                <li>
                    Please ensure your shipping address, phone number, and order information are correct.
                    So Good BB is not responsible for any incorrect information
                </li>
                <li>
                    In the event that the customer provides an incorrect address and the package is returned to So Good BB,
                    the customer will be responsible for the return shipping and redelivery costs.
                </li>
                <li>
                    So Good BB is not responsible for lost or damaged packages confirmed to be delivered
                    to the address listed under the order.
                </li>
            </ul>
        </div>
        <p class="last-paragraph">
            If you have any questions, please contact us at {{ business('store_email') }}.
        </p>
    </div>
@endsection