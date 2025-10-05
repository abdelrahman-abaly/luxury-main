<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WooCommerce API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WooCommerce REST API integration
    |
    */

    'url' => 'https://watches.stg.webgee.space',
    'consumer_key' => 'ck_bed35f841995fdcba03b0f1ef03ba147491ffe8e',
    'consumer_secret' => 'cs_9d850cae429eefd4c14e56f8fc92317bbb657e9c',
    'version' => env('WOOCOMMERCE_VERSION', 'wc/v3'),
    'verify_ssl' => env('WOOCOMMERCE_VERIFY_SSL', false),
    'timeout' => env('WOOCOMMERCE_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Sync Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for synchronization settings
    |
    */

    'sync' => [
        'products' => [
            'enabled' => env('WOOCOMMERCE_SYNC_PRODUCTS', true),
            'batch_size' => env('WOOCOMMERCE_PRODUCTS_BATCH_SIZE', 10),
            'auto_sync' => env('WOOCOMMERCE_AUTO_SYNC_PRODUCTS', false),
        ],
        'orders' => [
            'enabled' => env('WOOCOMMERCE_SYNC_ORDERS', true),
            'batch_size' => env('WOOCOMMERCE_ORDERS_BATCH_SIZE', 10),
            'auto_sync' => env('WOOCOMMERCE_AUTO_SYNC_ORDERS', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WooCommerce webhooks
    |
    */

    'webhooks' => [
        'enabled' => env('WOOCOMMERCE_WEBHOOKS_ENABLED', true),
        'secret' => env('WOOCOMMERCE_WEBHOOK_SECRET', 'your-webhook-secret-here'),
        'events' => [
            'product.created',
            'product.updated',
            'product.deleted',
            'order.created',
            'order.updated',
            'order.deleted',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Mapping
    |--------------------------------------------------------------------------
    |
    | Mapping between Laravel and WooCommerce statuses
    |
    */

    'status_mapping' => [
        'orders' => [
            'pending' => '1', // PENDING
            'processing' => '2', // PROCESSING
            'on-hold' => '2', // PROCESSING
            'completed' => '4', // DELIVERED
            'cancelled' => '5', // CANCELLED
            'refunded' => '6', // RETURNED
            'failed' => '5', // CANCELLED
        ],
        'products' => [
            'draft' => 'draft',
            'pending' => 'pending',
            'private' => 'private',
            'publish' => 'publish',
        ],
    ],
];
