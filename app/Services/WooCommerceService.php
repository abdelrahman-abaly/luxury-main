<?php

namespace App\Services;

use Automattic\WooCommerce\Client;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class WooCommerceService
{
    protected $woocommerce;

    public function __construct()
    {
        $this->woocommerce = new Client(
            config('services.woocommerce.url'),
            config('services.woocommerce.key'),
            config('services.woocommerce.secret'),
            [
                'version' => 'wc/v3',
                'verify_ssl' => false,
                'timeout' => 30,
            ]
        );
    }

    /**
     * Sync products from WooCommerce to Laravel
     */
    public function syncProductsFromWooCommerce($page = 1, $perPage = 10)
    {
        try {
            Log::info("Starting product sync from WooCommerce - Page: {$page}, Per Page: {$perPage}");

            $products = $this->woocommerce->get('products', [
                'page' => $page,
                'per_page' => $perPage,
                'status' => 'publish'
            ]);

            Log::info("Retrieved " . (is_array($products) ? count($products) : 0) . " products from WooCommerce API");

            $syncedCount = 0;
            foreach ($products as $wcProduct) {
                try {
                    Log::info("Processing product: {$wcProduct->name} (ID: {$wcProduct->id})");
                    $this->createOrUpdateProductFromWooCommerce($wcProduct);
                    $syncedCount++;
                    Log::info("Successfully synced product: {$wcProduct->name} (ID: {$wcProduct->id})");
                } catch (\Exception $e) {
                    Log::error("Failed to sync product {$wcProduct->id}: " . $e->getMessage());
                    Log::error("Product data: " . json_encode($wcProduct));
                }
            }

            Log::info("Successfully synced {$syncedCount} products from WooCommerce");
            return $syncedCount;
        } catch (\Exception $e) {
            Log::error('Error syncing products from WooCommerce: ' . $e->getMessage());
            Log::error('Error details: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Sync products from Laravel to WooCommerce
     */
    public function syncProductsToWooCommerce($products = null)
    {
        if (!$products) {
            $products = Product::whereNull('woocommerce_id')->get();
        }

        $syncedCount = 0;
        foreach ($products as $product) {
            try {
                $this->createOrUpdateProductInWooCommerce($product);
                $syncedCount++;
            } catch (\Exception $e) {
                Log::error("Error syncing product {$product->id} to WooCommerce: " . $e->getMessage());
            }
        }

        Log::info("Synced {$syncedCount} products to WooCommerce");
        return $syncedCount;
    }

    /**
     * Sync orders from WooCommerce to Laravel
     */
    public function syncOrdersFromWooCommerce($page = 1, $perPage = 10)
    {
        try {
            $orders = $this->woocommerce->get('orders', [
                'page' => $page,
                'per_page' => $perPage,
                'status' => 'any'
            ]);

            $syncedCount = 0;
            foreach ($orders as $wcOrder) {
                $this->createOrUpdateOrderFromWooCommerce($wcOrder);
                $syncedCount++;
            }

            Log::info("Synced {$syncedCount} orders from WooCommerce");
            return $syncedCount;
        } catch (\Exception $e) {
            Log::error('Error syncing orders from WooCommerce: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sync orders from Laravel to WooCommerce
     */
    public function syncOrdersToWooCommerce($orders = null)
    {
        if (!$orders) {
            $orders = Order::whereNull('woocommerce_id')->get();
        }

        $syncedCount = 0;
        foreach ($orders as $order) {
            try {
                $this->createOrUpdateOrderInWooCommerce($order);
                $syncedCount++;
            } catch (\Exception $e) {
                Log::error("Error syncing order {$order->id} to WooCommerce: " . $e->getMessage());
            }
        }

        Log::info("Synced {$syncedCount} orders to WooCommerce");
        return $syncedCount;
    }

    /**
     * Create or update product from WooCommerce data
     */
    public function createOrUpdateProductFromWooCommerce($wcProduct)
    {
        try {
            Log::info("Processing WooCommerce product: " . ($wcProduct->name ?? 'Unknown') . " (ID: " . ($wcProduct->id ?? 'Unknown') . ")");

            $product = Product::where('woocommerce_id', $wcProduct->id)->first();

            if (!$product) {
                $product = new Product();
            }

            // Safely assign values with proper validation
            $product->woocommerce_id = $wcProduct->id ?? null;
            $product->name = $wcProduct->name ?? 'Unnamed Product';
            $product->sku = $wcProduct->sku ?? '';

            // Clean and process description
            $product->description = $this->cleanProductDescription($wcProduct->description ?? '');

            $product->normal_price = $wcProduct->regular_price ?? '0';
            $product->sale_price = $wcProduct->sale_price ?? '0';
            $product->status = $this->mapWooCommerceProductStatus($wcProduct->status ?? 'publish');
            $product->stock_quantity = $wcProduct->stock_quantity ?? '1';

            // Process and clean images
            $product->images = $this->processProductImages($wcProduct->images ?? []);

            $product->woocommerce_synced_at = now();

            // Extract size and color from attributes if available
            if (isset($wcProduct->attributes) && is_array($wcProduct->attributes)) {
                foreach ($wcProduct->attributes as $attribute) {
                    if (isset($attribute->name) && isset($attribute->options)) {
                        if (strtolower($attribute->name) === 'size' && is_array($attribute->options) && count($attribute->options) > 0) {
                            $product->size = $attribute->options[0] ?? '';
                        }
                        if (strtolower($attribute->name) === 'color' && is_array($attribute->options) && count($attribute->options) > 0) {
                            $product->color = $attribute->options[0] ?? '';
                        }
                    }
                }
            }

            $product->save();
            Log::info("Successfully saved product: {$product->name} (Laravel ID: {$product->id})");
            return $product;
        } catch (\Exception $e) {
            Log::error("Failed to process WooCommerce product {$wcProduct->id}: " . $e->getMessage());
            Log::error("Product data: " . json_encode($wcProduct));
            throw $e;
        }
    }

    /**
     * Create or update product in WooCommerce
     */
    protected function createOrUpdateProductInWooCommerce($product)
    {
        $wcProductData = [
            'name' => $product->name,
            'type' => 'simple',
            'regular_price' => $product->normal_price,
            'sale_price' => $product->sale_price,
            'description' => $product->description,
            'short_description' => $product->description,
            'sku' => $product->sku,
            'manage_stock' => true,
            'stock_quantity' => (int)$product->stock_quantity,
            'status' => $this->mapLaravelProductStatus($product->status),
            'images' => $this->prepareImagesForWooCommerce($product->images),
        ];

        if ($product->woocommerce_id) {
            // Update existing product
            $this->woocommerce->put("products/{$product->woocommerce_id}", $wcProductData);
        } else {
            // Create new product
            $wcProduct = $this->woocommerce->post('products', $wcProductData);
            $product->woocommerce_id = $wcProduct->id;
            $product->woocommerce_synced_at = now();
            $product->save();
        }

        return $product;
    }

    /**
     * Create or update order from WooCommerce data
     */
    public function createOrUpdateOrderFromWooCommerce($wcOrder)
    {
        $order = Order::where('woocommerce_id', $wcOrder->id)->first();

        if (!$order) {
            $order = new Order();
        }

        $order->woocommerce_id = $wcOrder->id;
        $order->order_number = $wcOrder->number;
        $order->customer_id = $wcOrder->customer_id ?? '';
        $order->status = $this->mapWooCommerceOrderStatus($wcOrder->status);
        $order->total = $wcOrder->total;
        $order->address = $this->formatAddressFromWooCommerce($wcOrder->billing);
        $order->notes = $wcOrder->customer_note ?? '';
        $order->woocommerce_synced_at = now();

        // Set default values for required fields
        $order->latitude = null;
        $order->longitude = null;
        $order->governorate = null;
        $order->coupon_code = null;
        $order->delivery_agent_id = null;
        $order->employee_id = null;

        // Calculate commission (you may need to adjust this based on your business logic)
        $order->employee_commission = $this->calculateCommission($wcOrder->total);

        $order->save();
        return $order;
    }

    /**
     * Create or update order in WooCommerce
     */
    protected function createOrUpdateOrderInWooCommerce($order)
    {
        $wcOrderData = [
            'payment_method' => 'bacs',
            'payment_method_title' => 'Direct Bank Transfer',
            'set_paid' => false,
            'billing' => [
                'first_name' => 'Customer',
                'last_name' => '',
                'address_1' => $order->address,
                'city' => $order->governorate ?? '',
                'state' => '',
                'postcode' => '',
                'country' => 'EG',
                'email' => 'customer@example.com',
                'phone' => ''
            ],
            'line_items' => $this->prepareLineItemsForWooCommerce($order),
            'shipping' => [
                'first_name' => 'Customer',
                'last_name' => '',
                'address_1' => $order->address,
                'city' => $order->governorate ?? '',
                'state' => '',
                'postcode' => '',
                'country' => 'EG'
            ],
            'status' => $this->mapLaravelOrderStatus($order->status),
            'customer_note' => $order->notes ?? '',
        ];

        if ($order->woocommerce_id) {
            // Update existing order
            $this->woocommerce->put("orders/{$order->woocommerce_id}", $wcOrderData);
        } else {
            // Create new order
            $wcOrder = $this->woocommerce->post('orders', $wcOrderData);
            $order->woocommerce_id = $wcOrder->id;
            $order->woocommerce_synced_at = now();
            $order->save();
        }

        return $order;
    }

    /**
     * Map WooCommerce product status to Laravel status
     */
    protected function mapWooCommerceProductStatus($wcStatus)
    {
        $mapping = config('woocommerce.status_mapping.products', []);
        return $mapping[$wcStatus] ?? $wcStatus;
    }

    /**
     * Map Laravel product status to WooCommerce status
     */
    protected function mapLaravelProductStatus($laravelStatus)
    {
        $mapping = array_flip(config('woocommerce.status_mapping.products', []));
        return $mapping[$laravelStatus] ?? 'publish';
    }

    /**
     * Map WooCommerce order status to Laravel status
     */
    protected function mapWooCommerceOrderStatus($wcStatus)
    {
        $mapping = config('woocommerce.status_mapping.orders', []);
        return $mapping[$wcStatus] ?? '1'; // Default to pending
    }

    /**
     * Map Laravel order status to WooCommerce status
     */
    protected function mapLaravelOrderStatus($laravelStatus)
    {
        $mapping = array_flip(config('woocommerce.status_mapping.orders', []));
        return $mapping[$laravelStatus] ?? 'pending';
    }

    /**
     * Format address from WooCommerce billing data
     */
    protected function formatAddressFromWooCommerce($billing)
    {
        $address = [];
        if (isset($billing->address_1)) $address[] = $billing->address_1;
        if (isset($billing->address_2)) $address[] = $billing->address_2;
        if (isset($billing->city)) $address[] = $billing->city;
        if (isset($billing->state)) $address[] = $billing->state;
        if (isset($billing->postcode)) $address[] = $billing->postcode;
        if (isset($billing->country)) $address[] = $billing->country;

        return implode(', ', $address);
    }

    /**
     * Prepare images for WooCommerce
     */
    protected function prepareImagesForWooCommerce($images)
    {
        if (is_string($images)) {
            $images = json_decode($images, true);
        }

        if (!is_array($images)) {
            return [];
        }

        $wcImages = [];
        foreach ($images as $image) {
            if (is_string($image)) {
                $wcImages[] = ['src' => $image];
            } elseif (is_array($image) && isset($image['src'])) {
                $wcImages[] = $image;
            }
        }

        return $wcImages;
    }

    /**
     * Prepare line items for WooCommerce order
     */
    protected function prepareLineItemsForWooCommerce($order)
    {
        // This is a simplified version - you may need to adjust based on your order structure
        return [
            [
                'product_id' => 1, // You may need to map this to actual product IDs
                'quantity' => 1,
                'total' => $order->total
            ]
        ];
    }

    /**
     * Calculate commission based on order total
     */
    protected function calculateCommission($total)
    {
        // Simple 10% commission - adjust based on your business logic
        return number_format($total * 0.1, 2);
    }

    /**
     * Clean product description by removing HTML tags and extracting useful information
     */
    protected function cleanProductDescription($description)
    {
        if (empty($description)) {
            return '';
        }

        // Remove all HTML tags completely
        $cleanDescription = strip_tags($description);

        // Convert HTML entities
        $cleanDescription = html_entity_decode($cleanDescription, ENT_QUOTES, 'UTF-8');

        // Remove extra whitespace and line breaks
        $cleanDescription = preg_replace('/\s+/', ' ', $cleanDescription);
        $cleanDescription = trim($cleanDescription);

        // Extract table data if present
        $tableData = $this->extractTableData($description);
        if (!empty($tableData)) {
            $cleanDescription = $tableData;
        }

        // Limit description length to 200 characters
        if (strlen($cleanDescription) > 200) {
            $cleanDescription = substr($cleanDescription, 0, 200) . '...';
        }

        return $cleanDescription;
    }

    /**
     * Extract table data from HTML description
     */
    protected function extractTableData($html)
    {
        $tableData = '';

        // Look for table structure
        if (preg_match('/<table[^>]*>(.*?)<\/table>/s', $html, $matches)) {
            $tableContent = $matches[1];

            // Extract table rows
            if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/s', $tableContent, $rowMatches)) {
                foreach ($rowMatches[1] as $row) {
                    // Extract th and td content
                    if (
                        preg_match('/<th[^>]*>(.*?)<\/th>/s', $row, $thMatch) &&
                        preg_match('/<td[^>]*>(.*?)<\/td>/s', $row, $tdMatch)
                    ) {
                        $label = strip_tags($thMatch[1]);
                        $value = strip_tags($tdMatch[1]);

                        if (!empty($label) && !empty($value) && $value !== '———-') {
                            $tableData .= "{$label}: {$value}, ";
                        }
                    }
                }
            }
        }

        // Remove trailing comma and space
        $tableData = rtrim($tableData, ', ');

        return trim($tableData);
    }

    /**
     * Process product images and extract clean URLs
     */
    protected function processProductImages($images)
    {
        if (empty($images)) {
            return json_encode([]);
        }

        $processedImages = [];

        if (is_array($images) || is_object($images)) {
            foreach ($images as $image) {
                if (is_object($image) && isset($image->src)) {
                    $processedImages[] = [
                        'url' => $this->cleanImageUrl($image->src),
                        'alt' => $image->alt ?? '',
                        'name' => $image->name ?? ''
                    ];
                } elseif (is_string($image)) {
                    $processedImages[] = [
                        'url' => $this->cleanImageUrl($image),
                        'alt' => '',
                        'name' => ''
                    ];
                }
            }
        }

        return json_encode($processedImages);
    }

    /**
     * Clean image URL by fixing double slashes and other issues
     */
    protected function cleanImageUrl($url)
    {
        if (empty($url)) {
            return '';
        }

        // Fix double slashes in URL
        $url = preg_replace('#([^:])//+#', '$1/', $url);

        // Ensure proper protocol
        if (strpos($url, 'http') !== 0) {
            $url = 'https://' . ltrim($url, '/');
        }

        return $url;
    }

    /**
     * Test WooCommerce connection
     */
    public function testConnection()
    {
        try {
            $response = $this->woocommerce->get('system_status');
            return [
                'success' => true,
                'message' => 'Connection successful',
                'data' => $response
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }
}
