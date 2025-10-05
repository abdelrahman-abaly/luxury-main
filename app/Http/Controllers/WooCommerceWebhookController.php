<?php

namespace App\Http\Controllers;

use App\Services\WooCommerceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class WooCommerceWebhookController extends Controller
{
    protected $wooCommerceService;

    public function __construct(WooCommerceService $wooCommerceService)
    {
        $this->wooCommerceService = $wooCommerceService;
    }

    /**
     * Handle WooCommerce webhooks
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            // Verify webhook signature if configured
            if (config('woocommerce.webhooks.enabled') && config('woocommerce.webhooks.secret')) {
                if (!$this->verifyWebhookSignature($request)) {
                    Log::warning('Invalid webhook signature received');
                    return response()->json(['error' => 'Invalid signature'], 401);
                }
            }

            $event = $request->header('X-WC-Webhook-Event');
            $resource = $request->header('X-WC-Webhook-Resource');

            Log::info("Received WooCommerce webhook: {$event} for {$resource}");

            switch ($event) {
                case 'created':
                    $this->handleCreated($resource, $request->all());
                    break;
                case 'updated':
                    $this->handleUpdated($resource, $request->all());
                    break;
                case 'deleted':
                    $this->handleDeleted($resource, $request->all());
                    break;
                default:
                    Log::warning("Unknown webhook event: {$event}");
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error handling WooCommerce webhook: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle product created webhook
     */
    protected function handleCreated($resource, $data)
    {
        if ($resource === 'product') {
            $this->handleProductCreated($data);
        } elseif ($resource === 'order') {
            $this->handleOrderCreated($data);
        }
    }

    /**
     * Handle product updated webhook
     */
    protected function handleUpdated($resource, $data)
    {
        if ($resource === 'product') {
            $this->handleProductUpdated($data);
        } elseif ($resource === 'order') {
            $this->handleOrderUpdated($data);
        }
    }

    /**
     * Handle product deleted webhook
     */
    protected function handleDeleted($resource, $data)
    {
        if ($resource === 'product') {
            $this->handleProductDeleted($data);
        } elseif ($resource === 'order') {
            $this->handleOrderDeleted($data);
        }
    }

    /**
     * Handle product created
     */
    protected function handleProductCreated($productData)
    {
        try {
            $this->wooCommerceService->createOrUpdateProductFromWooCommerce($productData);
            Log::info("Product created via webhook: {$productData['id']}");
        } catch (\Exception $e) {
            Log::error("Error handling product created webhook: " . $e->getMessage());
        }
    }

    /**
     * Handle product updated
     */
    protected function handleProductUpdated($productData)
    {
        try {
            $this->wooCommerceService->createOrUpdateProductFromWooCommerce($productData);
            Log::info("Product updated via webhook: {$productData['id']}");
        } catch (\Exception $e) {
            Log::error("Error handling product updated webhook: " . $e->getMessage());
        }
    }

    /**
     * Handle product deleted
     */
    protected function handleProductDeleted($productData)
    {
        try {
            \App\Models\Product::where('woocommerce_id', $productData['id'])->delete();
            Log::info("Product deleted via webhook: {$productData['id']}");
        } catch (\Exception $e) {
            Log::error("Error handling product deleted webhook: " . $e->getMessage());
        }
    }

    /**
     * Handle order created
     */
    protected function handleOrderCreated($orderData)
    {
        try {
            $this->wooCommerceService->createOrUpdateOrderFromWooCommerce($orderData);
            Log::info("Order created via webhook: {$orderData['id']}");
        } catch (\Exception $e) {
            Log::error("Error handling order created webhook: " . $e->getMessage());
        }
    }

    /**
     * Handle order updated
     */
    protected function handleOrderUpdated($orderData)
    {
        try {
            $this->wooCommerceService->createOrUpdateOrderFromWooCommerce($orderData);
            Log::info("Order updated via webhook: {$orderData['id']}");
        } catch (\Exception $e) {
            Log::error("Error handling order updated webhook: " . $e->getMessage());
        }
    }

    /**
     * Handle order deleted
     */
    protected function handleOrderDeleted($orderData)
    {
        try {
            \App\Models\Order::where('woocommerce_id', $orderData['id'])->delete();
            Log::info("Order deleted via webhook: {$orderData['id']}");
        } catch (\Exception $e) {
            Log::error("Error handling order deleted webhook: " . $e->getMessage());
        }
    }

    /**
     * Verify webhook signature
     */
    protected function verifyWebhookSignature(Request $request): bool
    {
        $signature = $request->header('X-WC-Webhook-Signature');
        $payload = $request->getContent();
        $secret = config('woocommerce.webhooks.secret');

        if (!$signature || !$secret) {
            return false;
        }

        $expectedSignature = base64_encode(hash_hmac('sha256', $payload, $secret, true));

        return hash_equals($expectedSignature, $signature);
    }
}
