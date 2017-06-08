// Side bar links in admin panel

module.exports = [
    {
        label: 'Article Categories',
        icon: 'articlecategory',
        children: [
            {icon: 'create', label: 'Create New', url: '/admin/article-category'},
            {icon: 'list', label: 'List All Categories', url: '/admin/list/articleCategory'}
        ]
    },

    {
        label: 'Affiliates',
        icon: 'affiliate',
        children: [
            {icon: 'create', label: 'Create New', url: '/admin/affiliate'},
            {icon: 'list', label: 'List All Affiliates', url: '/admin/list/affiliate'}
        ]
    },

    {
        label: 'Content',
        icon: 'article',
        children: [
            {icon: 'create', label: 'Create New Article', url: '/admin/article'},
            {icon: 'list', label: 'List All Articles', url: '/admin/list/article'},
            {icon: 'create', label: 'Create New Popup', url: '/admin/popup'},
            {icon: 'list', label: 'List All Popups', url: '/admin/list/popup'},
        ]
    },

    {
        label: 'Users',
        icon: 'user',
        children: [
            {icon: 'create', label: 'Create New Entity', url: '/admin/entity'},
            {icon: 'list', label: 'List All Entities', url: '/admin/list/entity'},
            {icon: 'create', label: 'Create New Subscriber', url: '/admin/subscription'},
            {icon: 'list', label: 'List All Subscribers', url: '/admin/list/subscription'}
        ]
    },

    {
        label: 'Products',
        icon: 'product',
        children: [
            {icon: 'create', label: 'Create New', url: '/admin/product'},
            {icon: 'list', label: 'List All Products', url: '/admin/list/product'}
        ]
    },

    {
        label: 'Product Categories',
        icon: 'category',
        children: [
            {icon: 'create', label: 'Create New', url: '/admin/product-category'},
            {icon: 'list', label: 'List All Categories', url: '/admin/product-category/list'}
        ]
    },

    {
        label: 'Vendors',
        icon: 'vendor',
        children: [
            {icon: 'create', label: 'Create New', url: '/admin/vendor'},
            {icon: 'list', label: 'List All Vendors', url: '/admin/list/vendor'}
        ]
    },

    {
        label: 'Orders',
        icon: 'order',
        children: [
            {icon: 'create', label: 'Create New', url: '/admin/sales-order'},
            {icon: 'list', label: 'List All Orders', url: '/admin/list/salesOrder'}
        ]
    },

    {
        label: 'Gift Cards',
        icon: 'gift_card',
        children: [
            {icon: 'create', label: 'Create New', url: '/admin/gift-card'},
            {icon: 'list', label: 'List All Gift Cards', url: '/admin/list/giftCard'}
        ]
    },

    {
        label: 'Stock Locations',
        icon: 'stock_location',
        children: [
            {icon: 'create', label: 'Create New', url: '/admin/stock-location'},
            {icon: 'list', label: 'List All Users', url: '/admin/list/stockLocation'}
        ]
    },

    {
        label: 'Shipping Methods',
        icon: 'shipping_method',
        children: [
            {icon: 'create', label: 'Create New', url: '/admin/shipping-method'},
            {icon: 'list', label: 'List All Shipping Methods', url: '/admin/list/shippingMethod'}
        ]
    }
];