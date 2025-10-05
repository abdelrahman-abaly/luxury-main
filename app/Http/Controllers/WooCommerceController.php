<?php

namespace App\Http\Controllers;

use App\Services\WooCommerceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WooCommerceController extends Controller
{
    protected $wooCommerceService;

    public function __construct(WooCommerceService $wooCommerceService)
    {
        $this->wooCommerceService = $wooCommerceService;
    }

    /**
     * Test WooCommerce connection
     */
    public function testConnection(): JsonResponse
    {
        try {
            $result = $this->wooCommerceService->testConnection();
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync products from WooCommerce to Laravel
     */
    public function syncProductsFromWooCommerce(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);

            $syncedCount = $this->wooCommerceService->syncProductsFromWooCommerce($page, $perPage);

            return response()->json([
                'success' => true,
                'message' => "Successfully synced {$syncedCount} products from WooCommerce",
                'synced_count' => $syncedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error in syncProductsFromWooCommerce: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync products from Laravel to WooCommerce
     */
    public function syncProductsToWooCommerce(Request $request): JsonResponse
    {
        try {
            $syncedCount = $this->wooCommerceService->syncProductsToWooCommerce();

            return response()->json([
                'success' => true,
                'message' => "Successfully synced {$syncedCount} products to WooCommerce",
                'synced_count' => $syncedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error in syncProductsToWooCommerce: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync orders from WooCommerce to Laravel
     */
    public function syncOrdersFromWooCommerce(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);

            $syncedCount = $this->wooCommerceService->syncOrdersFromWooCommerce($page, $perPage);

            return response()->json([
                'success' => true,
                'message' => "Successfully synced {$syncedCount} orders from WooCommerce",
                'synced_count' => $syncedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error in syncOrdersFromWooCommerce: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync orders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync orders from Laravel to WooCommerce
     */
    public function syncOrdersToWooCommerce(Request $request): JsonResponse
    {
        try {
            $syncedCount = $this->wooCommerceService->syncOrdersToWooCommerce();

            return response()->json([
                'success' => true,
                'message' => "Successfully synced {$syncedCount} orders to WooCommerce",
                'synced_count' => $syncedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error in syncOrdersToWooCommerce: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync orders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Full synchronization (both directions)
     */
    public function fullSync(Request $request): JsonResponse
    {
        try {
            $results = [];

            // Sync products both ways
            $productsFromWoo = $this->wooCommerceService->syncProductsFromWooCommerce();
            $productsToWoo = $this->wooCommerceService->syncProductsToWooCommerce();

            // Sync orders both ways
            $ordersFromWoo = $this->wooCommerceService->syncOrdersFromWooCommerce();
            $ordersToWoo = $this->wooCommerceService->syncOrdersToWooCommerce();

            $results = [
                'products_from_woocommerce' => $productsFromWoo,
                'products_to_woocommerce' => $productsToWoo,
                'orders_from_woocommerce' => $ordersFromWoo,
                'orders_to_woocommerce' => $ordersToWoo,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Full synchronization completed successfully',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Error in fullSync: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Full synchronization failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
