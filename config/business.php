<?php

$dev_stripe_api_key = 'sk_test_b6XUv8IKUpQu2u9PauERcHeY';
$dev_stripe_public_key = 'pk_test_e0AhQY2600tqXZsl56xAUk63';
$dev_paypal_id = 'ATt6qHz-ufeGA2Ik-NW8czm_CeYQ9eBM4jxABoqzirEKB1ENt4K03C-03_i8F3mjA6DN6unmUuKqIdxZ';
$dev_paypal_key = 'EE9CV6aR2wK4S0pY3BPfPUh7gB7TRY4dBSyjt3B_r-uKdJWW-JspAQc3k9vxxUukytIccavUXnJDCYfz';

$store_address = [
  'line_1' => '3480 Steve Reynolds Blvd.',
  'city' => 'Duluth',
  'state' => 'GA',
  'zip' => '30096'
];

return [
  'store_name'                      => env('STORE_NAME', 'So Good Shop (Development)'),
  'site_url'                        => env('SITE_URL', 'http://sogoodbb.lo'),
  'site_root'                       => env('APP_ROOT', '/www'),
  'store_address'                   => $store_address,
  'store_phones'                    => ['678-580-2181', '678-580-2653'],
  'company_name'                    => 'So Good Shop, Inc.',
  'slogan'                          => 'Lace Wig, Weave and Hair Products',
  'store_email'                     => 'info@sogoodbb.com',
  'timezone'                        => 'America/New_York',
  'ups_license_number'              => env('UPS_LICENSE_NUMBER'),
  'ups_shipper_number'              => env('UPS_SHIPPER_NUMBER'),
  'ups_login_name'                  => env('UPS_LOGIN_NAME'),
  'ups_password'                    => env('UPS_PASSWORD'),
  'ups_shipping_city'               => 'Duluth',
  'static_resource_version'         => '2.2.9',
  'ups_shipping_state'              => 'GA',
  'ups_shipping_zip'                => 30096,
  'shipping_discount'               => 0.59999999999999998,
  'ups_shipping_country'            => 'US',
  'products_per_page'               => 500,
  'default_search_sort_method'      => 'relevance',
  'marketing_channel_query'         => 'channel',
  'default_category_listing_method' => 'newest',
  'shipping_carrier'                => 'none',
  'free_shipping_min'               => 50,
  'dev_stripe_api_key'              => $dev_stripe_api_key,
  'dev_stripe_public_key'           => $dev_stripe_public_key,
  'dev_paypal_id'                   => $dev_paypal_id,
  'dev_paypal_key'                  => $dev_paypal_key,
  'stripe_api_key'                  => env('STRIPE_API_KEY', $dev_stripe_api_key),
  'stripe_public_key'               => env('STRIPE_PUBLIC_KEY', $dev_stripe_public_key),
  'paypal_id'                       => env('PAYPAL_API_ID', $dev_paypal_id),
  'paypal_key'                      => env('PAYPAL_API_KEY', $dev_paypal_key),
  'bitbucket_user'                  => env('BITBUCKET_USER'),
  'bitbucket_pass'                  => env('BITBUCKET_PASS'),
  'avalara_api_key'                 => env('AVALARA_KEY'),
  'mad_mimi_api_key'                => env('MAD_MIMI_API_KEY'),
  'mad_mimi_user'                   => env('MAD_MIMI_USER'),
  'tax_states'                      => ['GA'],
  'meta_keywords'                   => 'Hair wigs, Lace front wigs, Full wigs, Half wigs, Weaving, Remi hair, Human hair, Wigs, Braids, Locs, Ponytail, Hair clip, Extensions, Human hair, Hairstyle, Trendy styles, Beauty, affordable hair products, black hair, synthetic wigs, crochet braids, dreadlocks, beauty blogger, hair products',
  'meta_description'                => 'Top quality hair, lace wig, weave and natural hair care products at prices impossible to beat.',
  'meta_title'                      => 'Beautiful Lace Front Wigs & Wigs at So Good prices',
  'logger_directory'                => env('APP_LOGGER_DIRECTORY', '/www/storage/logs'),
  'solr_config'                     => ['endpoint' => ['localhost' => ['host' => '127.0.0.1','port' => 8518, 'path' => '/solr/',]]],
  'admin_listings_count'            => 50
];