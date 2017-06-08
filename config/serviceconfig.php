<?php
use App\Services\Contracts\IEntityService;
use App\Services\Contracts\IPopupService;
use App\Services\Contracts\IProductService;
use App\Services\Contracts\ISalesOrderService;
use App\Services\Contracts\IStockLocationService;
use App\Services\Contracts\IVendorService;
use App\Entity;
use App\Popup;
use App\Product;
use App\SalesOrder;
use App\StockLocation;
use App\Vendor;

return [
  'record_service_crud_overrides' => [
    Product::class => IProductService::class,
    SalesOrder::class => ISalesOrderService::class,
    Vendor::class => IVendorService::class,
    Entity::class => IEntityService::class,
    StockLocation::class => IStockLocationService::class
  ],

  'record_service_list_search_overrides' => [
    Product::class => IProductService::class,
    SalesOrder::class => ISalesOrderService::class
  ],

  'record_copy_overrides' => [
    Product::class => IProductService::class,
    Popup::class => IPopupService::class
  ]
];