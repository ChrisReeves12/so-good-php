<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => 'Admin'], function()
{
  Route::get('/admin', ['as' => 'admin_home', 'uses' => 'HomeController@index']);
  Route::get('/admin/db/migrate', ['as' => 'admin_home_migrate', 'uses' => 'HomeController@migrate']);
  Route::get('/admin/logs', ['uses' => 'HomeController@logs']);
  Route::get('/admin/gift-card/generate-card-number', ['uses' => 'GiftCardController@generate_card_number']);
  Route::post('/admin/affiliate/upload/{id}', ['uses' => 'AffiliateController@upload_image']);
  Route::delete('/admin/affiliate/upload/{id}', ['uses' => 'AffiliateController@delete_image']);
  Route::put('/admin/affiliate/update-main-image/{id}', ['uses' => 'AffiliateController@update_main_image']);
  Route::get('/admin/list/{type}', ['as' => 'admin_list', 'uses' => 'ListController@index']);
  Route::get('/admin/list/record/search', ['as' => 'admin_record_search', 'uses' => 'ListController@search']);
  Route::get('/admin/record/get-single-record/{type}', ['as' => 'admin_get_single_record', 'uses' => 'RecordController@get_single_record']);
  Route::get('/admin/ajax/record-search/{type}', ['as' => 'admin_record_search', 'uses' => 'RecordController@record_search']);
  Route::get('/admin/product-category/list', ['as' => 'admin_product_category_list', 'uses' => 'ProductCategoryController@list']);
  Route::get('/admin/reports/email-subscribers/{type}', ['as' => 'admin_email_sub_report', 'uses' => 'ReportsController@email_sub_report']);
  Route::get('/admin/sales-order/invoice/{sales_order_id}', ['as' => 'admin_order_invoice', 'uses' => 'SalesOrderController@invoice']);
  Route::post('/admin/product/upload/{id}', ['as' => 'product_upload_photos', 'uses' => 'ProductController@upload_photos']);
  Route::post('/admin/product-category/image/upload/{id}', ['as' => 'product_category_upload_image', 'uses' => 'ProductCategoryController@upload_image']);
  Route::post('/admin/product-category/banner/upload/{id}', ['as' => 'product_category_upload_banner', 'uses' => 'ProductCategoryController@upload_banner']);
  Route::post('/admin/vendor/image/upload/{id}', ['as' => 'vendor_upload_image', 'uses' => 'VendorController@upload_image']);
  Route::delete('/admin/product-category/delete-image/{id}', ['as' => 'product_category_delete_image', 'uses' => 'ProductCategoryController@delete_image']);
  Route::delete('/admin/vendor/delete-image/{id}', ['as' => 'vendor_delete_image', 'uses' => 'VendorController@delete_image']);
  Route::delete('/admin/product-category/delete-banner/{id}', ['as' => 'product_category_delete_banner', 'uses' => 'ProductCategoryController@delete_banner']);
  Route::post('/admin/product/item/generate-item/{id}', ['as' => 'product_generate_item_sku', 'uses' => 'ProductController@generate_sku']);
  Route::delete('/admin/product/delete-image/{id}', ['as' => 'product_delete_image', 'uses' => 'ProductController@delete_image']);
  Route::delete('/admin/product/item/image/{id}', ['as' => 'item_delete_image', 'uses' => 'ProductController@delete_item_image']);
  Route::get('/admin/record/update-data/{type}', ['as' => 'update_data_record', 'uses' => 'RecordController@update_data']);
  Route::put('/admin/product/update-default-image/{id}', ['as' => 'product_update_main_image', 'uses' => 'ProductController@update_main_image']);
  Route::post('/admin/product/item/image/{id}', ['as' => 'product_update_item_image', 'uses' => 'ProductController@upload_item_image']);
  Route::get('/admin/{record_type}/{id?}', ['as' => 'record_index', 'uses' => 'RecordController@index']);
  Route::delete('/admin/record/{record_type}/{id?}', ['as' => 'record_delete', 'uses' => 'RecordController@delete']);
  Route::post('admin/record/copy/{type}/{id}', ['as' => 'record_copy', 'uses' => 'ListController@copy_record']);
  Route::post('/admin/record/{type}', ['as' => 'record_index', 'uses' => 'RecordController@create_update']);
  Route::put('/admin/record/{type}/{id}', ['as' => 'record_index', 'uses' => 'RecordController@create_update']);
});

Route::group(['namespace' => 'Frontend'], function()
{
  Route::get('/', ['as' => 'frontend_home', 'uses' => 'HomeController@index']);
  Route::get('/terms', ['as' => 'frontend_terms', 'uses' => 'HomeController@terms']);
  Route::get('/sweepstakes-rules', ['as' => 'frontend_sweepstakes_rules', 'uses' => 'HomeController@sweepstakes_rules']);
  Route::get('/account', ['as' => 'frontend_account', 'uses' => 'UserController@account']);
  Route::put('/account', ['as' => 'frontend_do_account', 'uses' => 'UserController@do_account']);
  Route::get('/forgot-password', ['as' => 'frontend_forgot_password', 'uses' => 'UserController@forgot_password']);
  Route::put('/recover-password', ['as' => 'frontend_reset_password', 'uses' => 'UserController@reset_password']);
  Route::get('/recover-password', ['as' => 'frontend_recover_password', 'uses' => 'UserController@recover_password']);
  Route::get('/checkout', ['as' => 'frontend_checkout', 'uses' => 'CartController@index']);
  Route::get('/popup/ajax/register', ['as' => 'register_popup', 'uses' => 'PopupController@register']);
  Route::get('/shopping-cart/ajax/inventory-check', ['as' => 'cart_inventory_check', 'uses' => 'CartController@ajax_inventory_check']);
  Route::get('/shopping-cart/ajax/gift-card/balance', ['as' => 'gift_card_balance_check', 'uses' => 'CartController@gift_card_balance_check']);
  Route::get('/shopping-cart/ajax/update-orderable-qty-on-lines', ['as' => 'cart_update_orderables', 'uses' => 'CartController@update_orderable_quantity_on_lines']);
  Route::get('/checkout/complete/receipt/{id}', ['as' => 'receipt_checkout', 'uses' => 'CartController@receipt']);
  Route::post('/checkout/send-paypal-purchase', ['as' => 'send_paypal_purchase', 'uses' => 'CartController@send_paypal_purchase']);
  Route::post('/checkout/execute-paypal-purchase', ['as' => 'send_paypal_purchase', 'uses' => 'CartController@execute_paypal_purchase']);
  Route::put('/checkout/gift-card/update', ['as' => 'cart_update_gift_cards', 'uses' => 'CartController@update_gift_card']);
  Route::post('/checkout/submit', ['as' => 'do_checkout', 'uses' => 'CartController@do_checkout']);
  Route::post('/forgot-password/send-email', ['as' => 'do_send_recovery_email', 'uses' => 'UserController@send_recovery_email']);
  Route::post('/shopping-cart/checkout/validate', ['as' => 'validate_checkout_form', 'uses' => 'CartController@validate_checkout_form']);
  Route::put('/shopping-cart/user-info', ['as' => 'update_user_data', 'uses' => 'CartController@update_user_info']);
  Route::put('/shopping-cart/discount-code/add', ['as' => 'update_discount_code_update', 'uses' => 'CartController@update_discount_code']);
  Route::get('/shopping-cart/ajax/update', ['as' => 'async_cart_update', 'uses' => 'CartController@async_get_cart_updates']);
  Route::put('/shopping-cart/ajax/line-item/quantity-change', ['as' => 'shopping_cart_qty_update', 'uses' => 'CartController@ajax_line_qty_update']);
  Route::put('/shopping-cart/ajax/address/update', ['as' => 'shopping_cart_update_addresses', 'uses' => 'CartController@ajax_update_addresses']);
  Route::put('/shopping-cart/ajax/shipping-method/update', ['as' => 'shopping_cart_update_shipping', 'uses' => 'CartController@ajax_update_shipping']);
  Route::post('/shopping-cart/add', ['as' => 'do_cart_add', 'uses' => 'CartController@add']);
  Route::delete('/shopping-cart/ajax/line-item', ['as' => 'do_line_item_delete', 'uses' => 'CartController@delete_line_item']);
  Route::get('/about-us', ['as' => 'frontend_about', 'uses' => 'HomeController@about']);
  Route::get('/beauty-vloggers', ['as' => 'frontend_vloggers', 'uses' => 'HomeController@vloggers']);
  Route::get('/contact-us', ['as' => 'frontend_contact', 'uses' => 'HomeController@contact_us']);
  Route::post('/contact-us', ['as' => 'frontend_contact', 'uses' => 'HomeController@do_contact']);
  Route::get('/return-policy', ['as' => 'frontend_return_policy', 'uses' => 'HomeController@return_policy']);
  Route::get('/sign-in', ['as' => 'login_form', 'uses' => 'AuthController@index']);
  Route::get('/sign-out', ['as' => 'sign_out', 'uses' => 'AuthController@do_sign_out']);
  Route::post('/sign-in', ['as' => 'do_login', 'uses' => 'AuthController@do_sign_in']);
  Route::get('/register', ['as' => 'user_registration', 'uses' => 'UserController@register']);
  Route::post('/register', ['as' => 'do_register', 'uses' => 'UserController@do_register']);
  Route::get('/ajax-search/products', ['as' => 'ajax_product_search', 'uses' => 'SearchController@ajax_product_search']);
  Route::get('/site-search', ['as' => 'product_listing_search', 'uses' => 'ProductListingController@index']);
  Route::post('/article/preview', ['as' => 'article_preview', 'uses' => 'ArticleController@preview']);
  Route::get('/product-page/ajax/product-data', ['as' => 'product_page_ajax_data', 'uses' => 'PageController@get_product_data']);
  Route::get('/articles/{slug}', ['as' => 'article_page', 'uses' => 'ArticleController@index']);
  Route::get('/article-category/{slug}', ['as' => 'article_category', 'uses' => 'ArticleController@article_category']);
  Route::get('/{slug}', ['as' => 'page', 'uses' => 'PageController@index']);
  Route::get('/{type}/{slug?}', ['as' => 'product_listing', 'uses' => 'ProductListingController@index']);
  Route::post('/subscription/add', ['as' => 'subscription_add', 'uses' => 'SubscriptionController@newsletter_add']);
  Route::post('/subscription/Popup', ['as' => 'subscription_popup', 'uses' => 'SubscriptionController@newsletter_popup']);
});
