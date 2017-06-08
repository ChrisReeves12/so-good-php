<?php

use Illuminate\Database\Seeder;
use App\RecordField;
use App\RecordType;

class DatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $this->_setup_record_types();
  }

  private function _setup_record_types()
  {
    // Create default record types
    $record_type_data = [
      ['name' => 'Product', 'formal_name' => 'Products', 'edit_url' => '/admin/product', 'model' => 'Product', 'fields' => [
        ['name' => 'Default Image', 'value_type' => 'image', 'formula' => 'default_image_display', 'searchable' => false, 'search_priority' => null, 'sort_order' => 0],
        ['name' => 'Name', 'value_type' => 'string', 'formula' => 'name', 'searchable' => true, 'search_priority' => 1, 'sort_order' => 1],
        ['name' => 'Store Price', 'value_type' => 'float', 'formula' => 'store_price', 'searchable' => false, 'search_priority' => null, 'sort_order' => 2],
        ['name' => 'List Price', 'value_type' => 'float', 'formula' => 'list_price', 'searchable' => false, 'search_priority' => null, 'sort_order' => 3],
        ['name' => 'Is Inactive?', 'value_type' => 'boolean', 'formula' => 'is_inactive_display', 'searchable' => false, 'search_priority' => null, 'sort_order' => 4]
      ]],

      // Item
      ['name' => 'Item', 'formal_name' => 'Items', 'edit_url' => '/admin/product/item', 'model' => 'Item', 'fields' => [
        ['name' => 'Sku', 'value_type' => 'string', 'formula' => 'sku', 'searchable' => true, 'search_priority' => 1, 'sort_order' => 0],
        ['name' => 'UPC', 'value_type' => 'string', 'formula' => 'upc', 'searchable' => true, 'search_priority' => 2, 'sort_order' => 1],
        ['name' => 'Product Name', 'value_type' => 'string', 'formula' => 'product_name', 'searchable' => true, 'search_priority' => 3, 'sort_order' => 2],
        ['name' => 'Store Price', 'value_type' => 'float', 'formula' => 'store_price', 'searchable' => true, 'search_priority' => 4, 'sort_order' => 3]
      ]],

      # Category
      ['name' => 'ProductCategory', 'formal_name' => 'Product Categories', 'edit_url' => '/admin/product-category', 'model' => 'ProductCategory', 'fields' => [
        ['name' => 'Image', 'value_type' => 'image', 'formula' => 'image.url', 'searchable' => false, 'search_priority' => null, 'sort_order' => 0],
        ['name' => 'Name', 'value_type' => 'string', 'formula' => 'name', 'searchable' => true, 'search_priority' => 1, 'sort_order' => 1],
        ['name' => 'Parent Category', 'value_type' => 'string', 'formula' => 'parent_category.name', 'searchable' => true, 'search_priority' => 2, 'sort_order' => 2],
        ['name' => 'Is Inactive?', 'value_type' => 'boolean', 'formula' => 'is_inactive_display', 'searchable' => false, 'search_priority' => null, 'sort_order' => 3]
      ]],

      # Stock Locations
      ['name' => 'StockLocation', 'formal_name' => 'Stock Locations', 'edit_url' => '/admin/stock-location', 'model' => 'StockLocation', 'fields' => [
        ['name' => 'Name', 'value_type' => 'string', 'formula' => 'name', 'searchable' => true, 'search_priority' => 1, 'sort_order' => 0],
        ['name' => 'Phone Number', 'value_type' => 'string', 'formula' => 'phone_number', 'searchable' => true, 'search_priority' => 2, 'sort_order' => 1],
        ['name' => 'Address', 'value_type' => 'string', 'formula' => 'address_line_1', 'searchable' => true, 'search_priority' => 3, 'sort_order' => 2],
        ['name' => 'Is Dropship?', 'value_type' => 'boolean', 'formula' => 'is_dropship', 'searchable' => false, 'search_priority' => null, 'sort_order' => 3],
        ['name' => 'Is Main Location?', 'value_type' => 'boolean', 'formula' => 'is_main_location', 'searchable' => false, 'search_priority' => null, 'sort_order' => 4]
      ]],

      # Vendor
      ['name' => 'Vendor', 'formal_name' => 'Vendors', 'edit_url' => '/admin/vendor', 'model' => 'Vendor', 'fields' => [
        ['name' => 'Image', 'value_type' => 'image', 'formula' => 'image.url', 'searchable' => false, 'search_priority' => null, 'sort_order' => 0],
        ['name' => 'Name', 'value_type' => 'string', 'formula' => 'name', 'searchable' => true, 'search_priority' => 1, 'sort_order' => 1],
        ['name' => 'Email', 'value_type' => 'float', 'formula' => 'email', 'searchable' => true, 'search_priority' => 2, 'sort_order' => 2],
        ['name' => 'Website', 'value_type' => 'float', 'formula' => 'website', 'searchable' => true, 'search_priority' => 3, 'sort_order' => 3],
        ['name' => 'Is Inactive?', 'value_type' => 'boolean', 'formula' => 'is_inactive_display', 'searchable' => false, 'search_priority' => null, 'sort_order' => 4]
      ]],

      # Shipping Method
      ['name' => 'ShippingMethod', 'formal_name' => 'Shipping Methods', 'edit_url' => '/admin/shipping-method', 'model' => 'ShippingMethod', 'fields' => [
        ['name' => 'Name', 'value_type' => 'string', 'formula' => 'name', 'searchable' => true, 'search_priority' => 1, 'sort_order' => 0],
        ['name' => 'Carrier', 'value_type' => 'string', 'formula' => 'carrier_name', 'searchable' => true, 'search_priority' => 2, 'sort_order' => 1],
        ['name' => 'API Identifier', 'value_type' => 'string', 'formula' => 'api_identifier', 'searchable' => true, 'search_priority' => 3, 'sort_order' => 2],
        ['name' => 'Transit Time', 'value_type' => 'integer', 'formula' => 'transit_time', 'searchable' => true, 'search_priority' => 4, 'sort_order' => 3],
        ['name' => 'Is Express?', 'value_type' => 'boolean', 'formula' => 'is_express', 'searchable' => false, 'search_priority' => null, 'sort_order' => 4],
        ['name' => 'Is Inactive?', 'value_type' => 'boolean', 'formula' => 'is_inactive_display', 'searchable' => false, 'search_priority' => null, 'sort_order' => 5]
      ]],

      # Entity
      ['name' => 'Entity', 'formal_name' => 'Users', 'edit_url' => '/admin/entity', 'model' => 'Entity', 'fields' => [
        ['name' => 'Name', 'value_type' => 'string', 'formula' => 'full_name', 'searchable' => true, 'search_priority' => 1, 'sort_order' => 0],
        ['name' => 'Email', 'value_type' => 'string', 'formula' => 'email', 'searchable' => true, 'search_priority' => 2, 'sort_order' => 1],
        ['name' => 'Phone Number', 'value_type' => 'string', 'formula' => 'phone_number', 'searchable' => true, 'search_priority' => 3, 'sort_order' => 2],
        ['name' => 'Role', 'value_type' => 'string', 'formula' => 'role', 'searchable' => true, 'search_priority' => 4, 'sort_order' => 3],
        ['name' => 'Status', 'value_type' => 'string', 'formula' => 'status', 'searchable' => false, 'search_priority' => null, 'sort_order' => 4],
        ['name' => 'Is Inactive?', 'value_type' => 'boolean', 'formula' => 'is_inactive_display', 'searchable' => false, 'search_priority' => null, 'sort_order' => 5]
      ]],

      # Sales Order
      ['name' => 'SalesOrder', 'formal_name' => 'Sales Orders', 'edit_url' => '/admin/sales-order', 'model' => 'SalesOrder', 'fields' => [
        ['name' => 'Customer', 'value_type' => 'string', 'formula' => 'customer_name', 'searchable' => true, 'search_priority' => 1, 'sort_order' => 0],
        ['name' => 'Status', 'value_type' => 'string', 'formula' => 'formatted_status', 'searchable' => true, 'search_priority' => 2, 'sort_order' => 1],
        ['name' => 'Shipping Method', 'value_type' => 'string', 'formula' => 'shipping_method_label', 'searchable' => false, 'search_priority' => null, 'sort_order' => 2],
        ['name' => 'Sub-Total', 'value_type' => 'string', 'formula' => 'sub_total', 'searchable' => false, 'search_priority' => null, 'sort_order' => 3],
        ['name' => 'Total', 'value_type' => 'string', 'formula' => 'total', 'searchable' => false, 'search_priority' => null, 'sort_order' => 4],
        ['name' => 'Marketing Channel', 'value_type' => 'string', 'formula' => 'formatted_marketing_channel', 'searchable' => false, 'search_priority' => null, 'sort_order' => 5],
        ['name' => 'Date Ordered', 'value_type' => 'string', 'formula' => 'order_time', 'searchable' => false, 'search_priority' => null, 'sort_order' => 6]
      ]],

      # Article
      ['name' => 'Article', 'formal_name' => 'Articles', 'edit_url' => '/admin/article', 'model' => 'Article', 'fields' => [
        ['name' => 'Title', 'value_type' => 'string', 'formula' => 'title', 'searchable' => true, 'search_priority' => 1, 'sort_order' => 0],
        ['name' => 'Category', 'value_type' => 'string', 'formula' => 'category_name', 'searchable' => true, 'search_priority' => 2, 'sort_order' => 1],
        ['name' => 'Slug', 'value_type' => 'string', 'formula' => 'slug', 'searchable' => true, 'search_priority' => 3, 'sort_order' => 2],
        ['name' => 'Is Published?', 'value_type' => 'boolean', 'formula' => 'is_published', 'searchable' => false, 'search_priority' => 4, 'sort_order' => 3]
      ]],

      # Article Category
      ['name' => 'ArticleCategory', 'formal_name' => 'ArticleCategories', 'edit_url' => '/admin/article-category', 'model' => 'ArticleCategory', 'fields' => [
        ['name' => 'Name', 'value_type' => 'string', 'formula' => 'name', 'searchable' => true, 'search_priority' => 1, 'sort_order' => 0],
        ['name' => 'Slug', 'value_type' => 'string', 'formula' => 'slug', 'searchable' => true, 'search_priority' => 2, 'sort_order' => 1]
      ]],

      # Subscriber
      ['name' => 'Subscription', 'formal_name' => 'Newsletter Subscriptions', 'edit_url' => '/admin/subscription', 'model' => 'Subscription', 'fields' => [
        ['name' => 'Email', 'value_type' => 'string', 'formula' => 'email', 'searchable' => true, 'search_priority' => 1, 'sort_order' => 0],
        ['name' => 'Date Added', 'value_type' => 'string', 'formula' => 'date_added', 'searchable' => true, 'search_priority' => 2, 'sort_order' => 1],
      ]],

      # Affiliate
      ['name' => 'Affiliate', 'formal_name' => 'Affiliates', 'edit_url' => '/admin/affiliate', 'model' => 'Affiliate', 'fields' => [
        ['name' => 'Name', 'value_type' => 'string', 'formula' => 'name', 'searchable' => true, 'search_priority' => 1, 'sort_order' => 0],
        ['name' => 'Affiliate Tag', 'value_type' => 'string', 'formula' => 'affiliate_tag', 'searchable' => true, 'search_priority' => 2, 'sort_order' => 1],
        ['name' => 'Type', 'value_type' => 'string', 'formula' => 'type', 'searchable' => true, 'search_priority' => 3, 'sort_order' => 2],
        ['name' => 'Slug', 'value_type' => 'string', 'formula' => 'slug', 'searchable' => true, 'search_priority' => 4, 'sort_order' => 3],
      ]],

      # Popup
      ['name' => 'Popup', 'formal_name' => 'Popups', 'edit_url' => '/admin/popup', 'model' => 'Popup', 'fields' => [
        ['name' => 'Name', 'value_type' => 'string', 'formula' => 'name', 'searchable' => true, 'search_priority' => 1, 'sort_order' => 0],
        ['name' => 'Internal Name', 'value_type' => 'string', 'formula' => 'internal_name', 'searchable' => true, 'search_priority' => 2, 'sort_order' => 1],
        ['name' => 'Cookie Name', 'value_type' => 'string', 'formula' => 'cookie_name', 'searchable' => true, 'search_priority' => 3, 'sort_order' => 2],
        ['name' => 'Is Inactive?', 'value_type' => 'string', 'formula' => 'is_inactive_display', 'searchable' => true, 'search_priority' => 4, 'sort_order' => 3],
      ]],

      // Gift Card
      ['name' => 'GiftCard', 'formal_name' => 'GiftCards', 'edit_url' => '/admin/gift-card', 'model' => 'GiftCard', 'fields' => [
        ['name' => 'Number', 'value_type' => 'float', 'formula' => 'number', 'searchable' => true, 'search_priority' => 1, 'sort_order' => 0],
        ['name' => 'Balance', 'value_type' => 'string', 'formula' => 'balance', 'searchable' => true, 'search_priority' => 2, 'sort_order' => 1],
        ['name' => 'Email', 'value_type' => 'string', 'formula' => 'email', 'searchable' => true, 'search_priority' => 3, 'sort_order' => 2],
        ['name' => 'Is Inactive?', 'value_type' => 'string', 'formula' => 'is_inactive_display', 'searchable' => true, 'search_priority' => 4, 'sort_order' => 3],
      ]]
    ];

    // Remove tables
    RecordField::getQuery()->delete();
    RecordType::getQuery()->delete();

    foreach($record_type_data as $rt)
    {
      $record_type = new RecordType();
      $record_type->setRawAttributes([
        'name'        => $rt['name'],
        'formal_name' => $rt['formal_name'],
        'edit_url'    => $rt['edit_url'],
        'model'       => $rt['model']
      ]);

      $record_type->save();

      foreach($rt['fields'] as $field)
      {
        $record_field = new RecordField();
        $record_field->setRawAttributes($field);
        $record_field->record_type_id = $record_type->id;
        $record_field->save();
      }
    }
  }
}
