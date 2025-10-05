# WooCommerce Integration

This Laravel application has been integrated with WooCommerce API to enable bidirectional synchronization of products and orders.

## Features

-   **Bidirectional Sync**: Products and orders can be synchronized between Laravel and WooCommerce in both directions
-   **Real-time Updates**: Webhook support for real-time updates when data changes in WooCommerce
-   **Admin Interface**: Web-based interface for managing synchronization
-   **Command Line Tools**: Artisan commands for automated synchronization
-   **Status Mapping**: Automatic mapping between Laravel and WooCommerce statuses

## Configuration

### 1. Environment Variables

Add the following variables to your `.env` file:

```env
# WooCommerce API Configuration
WOOCOMMERCE_URL=https://watches.stg.webgee.space/wtf
WOOCOMMERCE_CONSUMER_KEY=ck_bed35f841995fdcba03b0f1ef03ba147491ffe8e
WOOCOMMERCE_CONSUMER_SECRET=cs_9d850cae429eefd4c14e56f8fc92317bbb657e9e
WOOCOMMERCE_VERSION=wc/v3
WOOCOMMERCE_VERIFY_SSL=false
WOOCOMMERCE_TIMEOUT=30

# Sync Configuration
WOOCOMMERCE_SYNC_PRODUCTS=true
WOOCOMMERCE_SYNC_ORDERS=true
WOOCOMMERCE_PRODUCTS_BATCH_SIZE=10
WOOCOMMERCE_ORDERS_BATCH_SIZE=10

# Webhook Configuration
WOOCOMMERCE_WEBHOOKS_ENABLED=true
WOOCOMMERCE_WEBHOOK_SECRET=your-webhook-secret-here
```

### 2. Database Migration

The WooCommerce integration requires additional fields in your products and orders tables. Run the migration:

```bash
php artisan migrate
```

This will add:

-   `woocommerce_id` - Stores the WooCommerce ID for synced items
-   `woocommerce_synced_at` - Timestamp of last synchronization

## Usage

### Web Interface

Access the WooCommerce management interface at:

```
/woocommerce/manage
```

Features:

-   Test WooCommerce connection
-   Sync products (both directions)
-   Sync orders (both directions)
-   Full synchronization
-   View sync history

### Command Line

#### Sync Products

```bash
# Sync products from WooCommerce to Laravel
php artisan woocommerce:sync products --direction=from-woo

# Sync products from Laravel to WooCommerce
php artisan woocommerce:sync products --direction=to-woo

# Sync products in both directions
php artisan woocommerce:sync products --direction=both
```

#### Sync Orders

```bash
# Sync orders from WooCommerce to Laravel
php artisan woocommerce:sync orders --direction=from-woo

# Sync orders from Laravel to WooCommerce
php artisan woocommerce:sync orders --direction=to-woo

# Sync orders in both directions
php artisan woocommerce:sync orders --direction=both
```

#### Full Sync

```bash
# Sync everything in both directions
php artisan woocommerce:sync all --direction=both
```

#### Pagination Options

```bash
# Sync with custom pagination
php artisan woocommerce:sync products --direction=from-woo --page=2 --per-page=20
```

### API Endpoints

#### Test Connection

```http
GET /woocommerce/test-connection
```

#### Sync Products

```http
POST /woocommerce/sync-products-from-woo
POST /woocommerce/sync-products-to-woo
```

#### Sync Orders

```http
POST /woocommerce/sync-orders-from-woo
POST /woocommerce/sync-orders-to-woo
```

#### Full Sync

```http
POST /woocommerce/full-sync
```

### Webhooks

The integration supports WooCommerce webhooks for real-time updates. Configure the following webhook endpoints in your WooCommerce store:

**Webhook URL**: `https://your-domain.com/webhook/woocommerce`

**Events to configure**:

-   `product.created`
-   `product.updated`
-   `product.deleted`
-   `order.created`
-   `order.updated`
-   `order.deleted`

## Status Mapping

### Order Statuses

| WooCommerce | Laravel | Description |
| ----------- | ------- | ----------- |
| pending     | 1       | Pending     |
| processing  | 2       | Processing  |
| on-hold     | 2       | Processing  |
| completed   | 4       | Delivered   |
| cancelled   | 5       | Cancelled   |
| refunded    | 6       | Returned    |
| failed      | 5       | Cancelled   |

### Product Statuses

| WooCommerce | Laravel | Description |
| ----------- | ------- | ----------- |
| draft       | draft   | Draft       |
| pending     | pending | Pending     |
| private     | private | Private     |
| publish     | publish | Published   |

## Data Synchronization

### Products

-   **Name**: Product name
-   **SKU**: Product SKU
-   **Description**: Product description
-   **Price**: Regular price and sale price
-   **Stock**: Stock quantity
-   **Images**: Product images (JSON format)
-   **Attributes**: Size and color extracted from WooCommerce attributes

### Orders

-   **Order Number**: WooCommerce order number
-   **Customer**: Customer information
-   **Status**: Mapped order status
-   **Total**: Order total amount
-   **Address**: Billing address
-   **Notes**: Customer notes
-   **Commission**: Calculated commission (10% by default)

## Error Handling

-   All synchronization operations are logged
-   Failed syncs are logged with error details
-   Webhook failures are logged and handled gracefully
-   API connection errors are properly handled

## Security

-   Webhook signature verification (when configured)
-   CSRF protection for web interface
-   Authentication required for management interface
-   Secure API credentials storage

## Troubleshooting

### Connection Issues

1. Verify WooCommerce URL is correct
2. Check consumer key and secret
3. Ensure SSL verification settings match your environment
4. Check firewall and network connectivity

### Sync Issues

1. Check Laravel logs for detailed error messages
2. Verify database migrations are run
3. Ensure WooCommerce API permissions are correct
4. Check for data format mismatches

### Webhook Issues

1. Verify webhook URL is accessible
2. Check webhook secret configuration
3. Ensure webhook events are properly configured in WooCommerce
4. Check Laravel logs for webhook processing errors

## Support

For issues or questions regarding the WooCommerce integration, check the Laravel logs and ensure all configuration is correct according to this documentation.
