<!-- Desktop menu -->
@section('javascript-globals')
    <script>
        window.sogood.reactjs.header_shopping_cart_data = {!! !empty($current_cart) ? $current_cart->toJson() : '[]' !!};
    </script>
@endsection
<nav class="navbar navbar-light hidden-md-down">
    <div id="header_nav_menu">
        <ul data-reactroot="">
            <li class=""><p><a href="/category/wigs">Wigs</a><i class="fa fa-chevron-down"></i></p></li>
            <li class=""><p><a href="/category/lace-wigs">Lace Wigs</a><i class="fa fa-chevron-down"></i></p></li>
            <li class=""><p><a href="/category/weaves">Weaves</a></p></li>
            <li class=""><p><a href="/category/braids">Braids</a></p></li>
            <li class=""><p><a href="/category/hair-extension">Extensions</a><i class="fa fa-chevron-down"></i></p>
            </li>
            <li class=""><p><a href="/category/hair-care">Hair Care</a><i class="fa fa-chevron-down"></i></p></li>
            <li class=""><p><a href="/category/accessories">Cosmetics</a><i class="fa fa-chevron-down"></i></p></li>
            <li class=""><p><a href="/category/accessories">Accessories</a><i class="fa fa-chevron-down"></i></p></li>
            <li class=""><p><a href="/beauty-vloggers">Vloggers</a></p></li>
            <li class=""><p><a href="/category/sale">Sale</a><i class="fa fa-chevron-down"></i></p></li>
        </ul>
    </div>
</nav>
<div class="hidden-lg-up mobile-menu-toggle-container">
    <div class="row">
        <div class="col-xs-2">
            <a href="" id="main_mobile_menu_toggle"><i class="fa fa-bars"></i></a>
        </div>
        <div style="text-align: right;" class="col-xs-10">
            <div class="shopping_cart_display"> </div>
        </div>
    </div>
</div>
