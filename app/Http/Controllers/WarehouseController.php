<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Lead;
use App\Models\User;
use App\Models\DamagedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WarehouseController extends Controller
{
    /**
     * Warehouse Dashboard Home
     */
    public function home()
    {
        try {
            // Get warehouse statistics
            $stats = $this->getWarehouseStats();

            // Get recent orders
            $recentOrders = $this->getRecentOrders();

            // Get low stock products
            $lowStockProducts = $this->getLowStockProducts();

            // Get pending orders count
            $pendingOrdersCount = Order::where('status', config('constants.PENDING'))->count();

            // Get accepted orders count
            $acceptedOrdersCount = Order::where('status', config('constants.PROCESSING'))->count();

            // Get out of stock products count
            $outOfStockCount = Product::where('stock_quantity', '0')->count();

            // Get almost out of stock products (less than 5 items)
            $almostOutOfStockCount = Product::where('stock_quantity', '>', '0')
                ->where('stock_quantity', '<=', '5')
                ->count();

            // Get waiting orders count (pending orders without out of stock products)
            $waitingOrders = Order::where('status', config('constants.PENDING'))
                ->whereDoesntHave('products', function ($query) {
                    $query->where('stock_quantity', '0');
                })
                ->count();

            // Get waiting purchases count (PENDING orders with out of stock products)
            $waitingPurchases = Order::where('status', config('constants.PENDING'))
                ->whereHas('products', function ($query) {
                    $query->where('stock_quantity', '0');
                })
                ->count();

            // Get accepted orders count (same as processing orders)
            $acceptedOrders = $acceptedOrdersCount;

            // Get sent to manager count (orders ready for delivery)
            $sentToManager = Order::where('status', config('constants.OUT_FOR_DELIVERY'))->count();

            // Prepare chart data for last 30 days
            $chartLabels = [];
            $chartData = [];
            $chartValues = [];

            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $chartLabels[] = $date->format('M d');

                $dayOrders = Order::whereDate('created_at', $date->format('Y-m-d'))
                    ->where('status', config('constants.DELIVERED'))
                    ->get();

                $chartData[] = $dayOrders->count();
                $chartValues[] = $dayOrders->sum('total');
            }

            $chartData = [
                'labels' => $chartLabels,
                'data' => $chartData,
                'values' => $chartValues
            ];

            return view('warehouse.home', compact(
                'stats',
                'recentOrders',
                'lowStockProducts',
                'pendingOrdersCount',
                'acceptedOrdersCount',
                'outOfStockCount',
                'almostOutOfStockCount',
                'waitingOrders',
                'waitingPurchases',
                'acceptedOrders',
                'sentToManager',
                'chartData'
            ));
        } catch (\Exception $e) {
            Log::error('Warehouse dashboard error: ' . $e->getMessage());
            return view('warehouse.home', [
                'stats' => [],
                'recentOrders' => collect(),
                'lowStockProducts' => collect(),
                'pendingOrdersCount' => 0,
                'acceptedOrdersCount' => 0,
                'outOfStockCount' => 0,
                'almostOutOfStockCount' => 0,
                'waitingOrders' => 0,
                'waitingPurchases' => 0,
                'acceptedOrders' => 0,
                'sentToManager' => 0,
                'chartData' => ['labels' => [], 'data' => []]
            ]);
        }
    }

    /**
     * Get warehouse statistics
     */
    private function getWarehouseStats()
    {
        return [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', config('constants.PENDING'))->count(),
            'processing_orders' => Order::where('status', config('constants.PROCESSING'))->count(),
            'delivered_orders' => Order::where('status', config('constants.DELIVERED'))->count(),
            'cancelled_orders' => Order::where('status', config('constants.CANCELLED'))->count(),
            'total_products' => Product::count(),
            'in_stock_products' => Product::where('stock_quantity', '>', '0')->count(),
            'out_of_stock_products' => Product::where('stock_quantity', '0')->count(),
            'total_revenue' => Order::where('status', config('constants.DELIVERED'))->sum('total'),
            'today_orders' => Order::whereDate('created_at', today())->count(),
        ];
    }

    /**
     * Get recent orders
     */
    private function getRecentOrders()
    {
        return Order::with(['customer', 'employee'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer ? $order->customer->name : 'Unknown Customer',
                    'employee_name' => $order->employee ? $order->employee->name : 'Unknown Employee',
                    'status' => $order->status,
                    'total' => $order->total,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'status_text' => $this->getStatusText($order->status),
                ];
            });
    }

    /**
     * Get low stock products
     */
    private function getLowStockProducts()
    {
        return Product::where('stock_quantity', '>', '0')
            ->where('stock_quantity', '<=', '5')
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'stock_quantity' => $product->stock_quantity,
                    'normal_price' => $product->normal_price,
                ];
            });
    }

    /**
     * Get status text
     */
    private function getStatusText($status)
    {
        $statusMap = [
            config('constants.PENDING') => 'Pending',
            config('constants.PROCESSING') => 'Processing',
            config('constants.OUT_FOR_DELIVERY') => 'Out for Delivery',
            config('constants.DELIVERED') => 'Delivered',
            config('constants.CANCELLED') => 'Cancelled',
            config('constants.RETURNED') => 'Returned',
        ];

        return $statusMap[$status] ?? 'Unknown';
    }

    /**
     * Waiting Orders Page
     */
    public function waitingOrders()
    {
        $orders = Order::with(['customer', 'employee', 'products'])
            ->where('status', config('constants.PENDING'))
            ->whereDoesntHave('products', function ($query) {
                $query->where('stock_quantity', '0'); // استبعاد الطلبات التي تحتوي على منتجات نفدت من المخزون
            })
            ->latest()
            ->paginate(20);

        // Calculate summary data for all pending orders without out of stock products
        $allPendingOrders = Order::with('products')
            ->where('status', config('constants.PENDING'))
            ->whereDoesntHave('products', function ($query) {
                $query->where('stock_quantity', '0');
            })
            ->get();

        $totalItems = $allPendingOrders->sum(function ($order) {
            return $order->products->sum('pivot.quantity');
        });

        $totalPrice = $allPendingOrders->sum('total');

        return view('warehouse.waiting-orders', compact('orders', 'totalItems', 'totalPrice'));
    }

    /**
     * Accepted Orders Page
     */
    public function acceptedOrders()
    {
        $orders = Order::with(['customer', 'employee', 'products'])
            ->where('status', config('constants.PROCESSING'))
            ->latest()
            ->paginate(20);

        // Calculate summary data for all processing orders (not just current page)
        $allProcessingOrders = Order::with('products')
            ->where('status', config('constants.PROCESSING'))
            ->get();

        $totalItems = $allProcessingOrders->sum(function ($order) {
            return $order->products->sum('pivot.quantity');
        });

        $totalPrice = $allProcessingOrders->sum('total');

        return view('warehouse.accepted-orders', compact('orders', 'totalItems', 'totalPrice'));
    }

    /**
     * Waiting Purchases Page
     */
    public function waitingPurchases()
    {
        // Show only PENDING orders that contain products with zero stock (out of stock)
        $orders = Order::with(['customer', 'employee', 'products'])
            ->where('status', config('constants.PENDING'))
            ->whereHas('products', function ($query) {
                $query->where('stock_quantity', '0');
            })
            ->latest()
            ->paginate(20);

        // Calculate summary data for all PENDING orders with out of stock products
        $allOutOfStockOrders = Order::with('products')
            ->where('status', config('constants.PENDING'))
            ->whereHas('products', function ($query) {
                $query->where('stock_quantity', '0');
            })
            ->get();

        $totalItems = $allOutOfStockOrders->sum(function ($order) {
            return $order->products->where('stock_quantity', '0')->sum('pivot.quantity');
        });

        $totalPrice = $allOutOfStockOrders->sum('total');

        return view('warehouse.waiting-purchases', compact('orders', 'totalItems', 'totalPrice'));
    }

    /**
     * Accept Order
     */
    public function acceptOrder(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            if ($order->status != config('constants.PENDING')) {
                return back()->with('error', 'Order is not in pending status.');
            }

            $order->status = config('constants.PROCESSING');
            $order->save();

            Log::info("Order {$orderId} accepted by warehouse user");

            return back()->with('success', 'Order accepted successfully.');
        } catch (\Exception $e) {
            Log::error('Error accepting order: ' . $e->getMessage());
            return back()->with('error', 'Failed to accept order.');
        }
    }

    /**
     * Reject Order
     */
    public function rejectOrder(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            if ($order->status != config('constants.PENDING')) {
                return back()->with('error', 'Order is not in pending status.');
            }

            $order->status = config('constants.CANCELLED');
            $order->notes = ($order->notes ?? '') . "\nRejected by warehouse: " . ($request->reason ?? 'No reason provided');
            $order->save();

            Log::info("Order {$orderId} rejected by warehouse user");

            return back()->with('success', 'Order rejected successfully.');
        } catch (\Exception $e) {
            Log::error('Error rejecting order: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject order.');
        }
    }

    /**
     * Mark Order as Ready for Delivery
     */
    public function markReadyForDelivery(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            if ($order->status != config('constants.PROCESSING')) {
                return back()->with('error', 'Order is not in processing status.');
            }

            $order->status = config('constants.OUT_FOR_DELIVERY');
            $order->save();

            Log::info("Order {$orderId} marked as ready for delivery by warehouse user");

            return back()->with('success', 'Order marked as ready for delivery.');
        } catch (\Exception $e) {
            Log::error('Error marking order as ready for delivery: ' . $e->getMessage());
            return back()->with('error', 'Failed to mark order as ready for delivery.');
        }
    }

    /**
     * ========================================
     * STOCK MANAGEMENT METHODS
     * ========================================
     */

    /**
     * In Stock Products
     */
    public function inStock(Request $request)
    {
        try {
            $query = Product::where('stock_quantity', '>', '0');

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('warehouse_id')) {
                $query->where('warehouse_id', $request->warehouse_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('min_stock')) {
                $query->where('stock_quantity', '>=', $request->min_stock);
            }

            if ($request->filled('max_stock')) {
                $query->where('stock_quantity', '<=', $request->max_stock);
            }

            $products = $query->orderBy('stock_quantity', 'desc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'total_products' => Product::where('stock_quantity', '>', '0')->count(),
                'total_value' => Product::where('stock_quantity', '>', '0')
                    ->selectRaw('SUM(stock_quantity * normal_price) as total')
                    ->value('total') ?? 0,
                'average_stock' => Product::where('stock_quantity', '>', '0')
                    ->selectRaw('AVG(stock_quantity) as avg')
                    ->value('avg') ?? 0,
                'warehouses' => Product::where('stock_quantity', '>', '0')
                    ->selectRaw('warehouse_id, COUNT(*) as count')
                    ->groupBy('warehouse_id')
                    ->get()
            ];

            return view('warehouse.in-stock', compact('products', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading in-stock products: ' . $e->getMessage());
            return view('warehouse.in-stock', [
                'products' => collect(),
                'stats' => []
            ]);
        }
    }

    /**
     * Almost Out of Stock Products
     */
    public function almostOutOfStock(Request $request)
    {
        try {
            $query = Product::where('stock_quantity', '>', '0')
                ->where('stock_quantity', '<=', '5');

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('warehouse_id')) {
                $query->where('warehouse_id', $request->warehouse_id);
            }

            if ($request->filled('max_stock')) {
                $query->where('stock_quantity', '<=', $request->max_stock);
            }

            $products = $query->orderBy('stock_quantity', 'asc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'total_products' => Product::where('stock_quantity', '>', '0')
                    ->where('stock_quantity', '<=', '5')->count(),
                'critical_products' => Product::where('stock_quantity', '>', '0')
                    ->where('stock_quantity', '<=', '2')->count(),
                'total_value' => Product::where('stock_quantity', '>', '0')
                    ->where('stock_quantity', '<=', '5')
                    ->selectRaw('SUM(stock_quantity * normal_price) as total')
                    ->value('total') ?? 0,
                'warehouses' => Product::where('stock_quantity', '>', '0')
                    ->where('stock_quantity', '<=', '5')
                    ->selectRaw('warehouse_id, COUNT(*) as count')
                    ->groupBy('warehouse_id')
                    ->get()
            ];

            return view('warehouse.almost-out-stock', compact('products', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading almost-out-of-stock products: ' . $e->getMessage());
            return view('warehouse.almost-out-stock', [
                'products' => collect(),
                'stats' => []
            ]);
        }
    }

    /**
     * Out of Stock Products
     */
    public function outOfStock(Request $request)
    {
        try {
            $query = Product::where('stock_quantity', '0');

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('warehouse_id')) {
                $query->where('warehouse_id', $request->warehouse_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $products = $query->orderBy('updated_at', 'desc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'total_products' => Product::where('stock_quantity', '0')->count(),
                'total_value' => Product::where('stock_quantity', '0')
                    ->selectRaw('SUM(normal_price) as total')
                    ->value('total') ?? 0,
                'warehouses' => Product::where('stock_quantity', '0')
                    ->selectRaw('warehouse_id, COUNT(*) as count')
                    ->groupBy('warehouse_id')
                    ->get(),
                'last_updated' => Product::where('stock_quantity', '0')
                    ->orderBy('updated_at', 'desc')
                    ->value('updated_at')
            ];

            return view('warehouse.out-of-stock', compact('products', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading out-of-stock products: ' . $e->getMessage());
            return view('warehouse.out-of-stock', [
                'products' => collect(),
                'stats' => []
            ]);
        }
    }

    /**
     * Update Product Stock
     */
    public function updateStock(Request $request, $productId)
    {
        try {
            $request->validate([
                'stock_quantity' => 'required|integer|min:0',
                'reason' => 'nullable|string|max:255'
            ]);

            $product = Product::findOrFail($productId);
            $oldStock = $product->stock_quantity;

            $product->stock_quantity = $request->stock_quantity;
            $product->save();

            // Log the stock update
            Log::info("Product {$productId} stock updated from {$oldStock} to {$request->stock_quantity}. Reason: " . ($request->reason ?? 'No reason provided'));

            return back()->with('success', 'Stock updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating product stock: ' . $e->getMessage());
            return back()->with('error', 'Failed to update stock.');
        }
    }

    /**
     * Bulk Stock Update
     */
    public function bulkUpdateStock(Request $request)
    {
        try {
            $request->validate([
                'products' => 'required|array',
                'products.*.id' => 'required|exists:products,id',
                'products.*.stock_quantity' => 'required|integer|min:0',
                'reason' => 'nullable|string|max:255'
            ]);

            $updated = 0;
            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);
                if ($product) {
                    $oldStock = $product->stock_quantity;
                    $product->stock_quantity = $productData['stock_quantity'];
                    $product->save();
                    $updated++;

                    Log::info("Product {$product->id} stock updated from {$oldStock} to {$productData['stock_quantity']}. Reason: " . ($request->reason ?? 'Bulk update'));
                }
            }

            return back()->with('success', "Updated {$updated} products successfully.");
        } catch (\Exception $e) {
            Log::error('Error bulk updating stock: ' . $e->getMessage());
            return back()->with('error', 'Failed to update stock.');
        }
    }

    /**
     * ========================================
     * RETURNS MANAGEMENT METHODS
     * ========================================
     */

    /**
     * Waiting Returns
     */
    public function waitingReturns(Request $request)
    {
        try {
            // Get orders that are marked for return but not yet processed
            $query = Order::where('status', config('constants.RETURN_REQUESTED'))
                ->orWhere('status', config('constants.RETURN_PENDING'));

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('phone_numbers', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->filled('reason')) {
                $query->where('notes', 'like', "%{$request->reason}%");
            }

            $returns = $query->with(['customer', 'employee', 'products'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'total_returns' => Order::where('status', config('constants.RETURN_REQUESTED'))
                    ->orWhere('status', config('constants.RETURN_PENDING'))->count(),
                'urgent_returns' => Order::where('status', config('constants.RETURN_REQUESTED'))
                    ->where('created_at', '<=', now()->subDays(3))->count(),
                'total_value' => Order::where('status', config('constants.RETURN_REQUESTED'))
                    ->orWhere('status', config('constants.RETURN_PENDING'))
                    ->sum('total'),
                'avg_processing_time' => $this->getAverageProcessingTime('return')
            ];

            return view('warehouse.waiting-returns', compact('returns', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading waiting returns: ' . $e->getMessage());
            return view('warehouse.waiting-returns', [
                'returns' => collect(),
                'stats' => []
            ]);
        }
    }

    /**
     * Returns Requests
     */
    public function returnsRequests(Request $request)
    {
        try {
            // Get all return requests with their status
            $query = Order::whereIn('status', [
                config('constants.RETURN_REQUESTED'),
                config('constants.RETURN_PENDING'),
                config('constants.RETURN_APPROVED'),
                config('constants.RETURN_REJECTED')
            ]);

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('phone_numbers', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $returns = $query->with(['customer', 'employee', 'products'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'total_requests' => Order::whereIn('status', [
                    config('constants.RETURN_REQUESTED'),
                    config('constants.RETURN_PENDING'),
                    config('constants.RETURN_APPROVED'),
                    config('constants.RETURN_REJECTED')
                ])->count(),
                'pending_requests' => Order::where('status', config('constants.RETURN_REQUESTED'))->count(),
                'approved_requests' => Order::where('status', config('constants.RETURN_APPROVED'))->count(),
                'rejected_requests' => Order::where('status', config('constants.RETURN_REJECTED'))->count(),
                'total_value' => Order::whereIn('status', [
                    config('constants.RETURN_REQUESTED'),
                    config('constants.RETURN_PENDING'),
                    config('constants.RETURN_APPROVED'),
                    config('constants.RETURN_REJECTED')
                ])->sum('total')
            ];

            return view('warehouse.returns-requests', compact('returns', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading returns requests: ' . $e->getMessage());
            return view('warehouse.returns-requests', [
                'returns' => collect(),
                'stats' => []
            ]);
        }
    }

    /**
     * Accepted Returns
     */
    public function acceptedReturns(Request $request)
    {
        try {
            // Get accepted returns that are ready for processing
            $query = Order::where('status', config('constants.RETURN_APPROVED'));

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('phone_numbers', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('updated_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('updated_at', '<=', $request->date_to);
            }

            $returns = $query->with(['customer', 'employee', 'products'])
                ->orderBy('updated_at', 'desc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'total_accepted' => Order::where('status', config('constants.RETURN_APPROVED'))->count(),
                'ready_for_refund' => Order::where('status', config('constants.RETURN_APPROVED'))
                    ->where('updated_at', '<=', now()->subHours(24))->count(),
                'total_value' => Order::where('status', config('constants.RETURN_APPROVED'))->sum('total'),
                'avg_approval_time' => $this->getAverageProcessingTime('approval')
            ];

            return view('warehouse.accepted-returns', compact('returns', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading accepted returns: ' . $e->getMessage());
            return view('warehouse.accepted-returns', [
                'returns' => collect(),
                'stats' => []
            ]);
        }
    }

    /**
     * Process Return Request
     */
    public function processReturn(Request $request, $orderId)
    {
        try {
            $request->validate([
                'action' => 'required|in:approve,reject',
                'reason' => 'nullable|string|max:500',
                'refund_amount' => 'nullable|numeric|min:0'
            ]);

            $order = Order::findOrFail($orderId);

            if (!in_array($order->status, [config('constants.RETURN_REQUESTED'), config('constants.RETURN_PENDING')])) {
                return back()->with('error', 'Order is not in a valid return status.');
            }

            if ($request->action === 'approve') {
                $order->status = config('constants.RETURN_APPROVED');
                $order->notes = ($order->notes ?? '') . "\nReturn approved by warehouse: " . ($request->reason ?? 'No reason provided');

                if ($request->refund_amount) {
                    $order->refund_amount = $request->refund_amount;
                }
            } else {
                $order->status = config('constants.RETURN_REJECTED');
                $order->notes = ($order->notes ?? '') . "\nReturn rejected by warehouse: " . ($request->reason ?? 'No reason provided');
            }

            $order->save();

            Log::info("Return {$orderId} {$request->action}d by warehouse user");

            return back()->with('success', "Return {$request->action}d successfully.");
        } catch (\Exception $e) {
            Log::error('Error processing return: ' . $e->getMessage());
            return back()->with('error', 'Failed to process return.');
        }
    }

    /**
     * Complete Return Processing
     */
    public function completeReturn(Request $request, $orderId)
    {
        try {
            $request->validate([
                'refund_method' => 'required|in:cash,bank_transfer,store_credit',
                'refund_amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string|max:500'
            ]);

            $order = Order::findOrFail($orderId);

            if ($order->status !== config('constants.RETURN_APPROVED')) {
                return back()->with('error', 'Order is not approved for return.');
            }

            $order->status = config('constants.RETURN_COMPLETED');
            $order->refund_amount = $request->refund_amount;
            $order->refund_method = $request->refund_method;
            $order->notes = ($order->notes ?? '') . "\nReturn completed: " . ($request->notes ?? 'No additional notes');

            // Restore stock quantities
            foreach ($order->products as $product) {
                $product->stock_quantity += $product->pivot->quantity;
                $product->save();
            }

            $order->save();

            Log::info("Return {$orderId} completed by warehouse user");

            return back()->with('success', 'Return completed successfully.');
        } catch (\Exception $e) {
            Log::error('Error completing return: ' . $e->getMessage());
            return back()->with('error', 'Failed to complete return.');
        }
    }

    /**
     * Get Average Processing Time
     */
    private function getAverageProcessingTime($type)
    {
        try {
            if ($type === 'return') {
                $orders = Order::where('status', config('constants.RETURN_COMPLETED'))
                    ->whereNotNull('updated_at')
                    ->get();
            } else {
                $orders = Order::where('status', config('constants.RETURN_APPROVED'))
                    ->whereNotNull('updated_at')
                    ->get();
            }

            if ($orders->isEmpty()) {
                return 0;
            }

            $totalHours = $orders->sum(function ($order) {
                return $order->created_at->diffInHours($order->updated_at);
            });

            return round($totalHours / $orders->count(), 1);
        } catch (\Exception $e) {
            Log::error('Error calculating average processing time: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ========================================
     * MATERIALS MANAGEMENT METHODS
     * ========================================
     */

    /**
     * Boxes Management
     */
    public function boxes(Request $request)
    {
        try {
            // Get boxes from products where category is 'boxes' or similar
            $query = Product::where('category', 'like', '%box%')
                ->orWhere('name', 'like', '%box%')
                ->orWhere('sku', 'like', '%BOX%');

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('size')) {
                $query->where('name', 'like', "%{$request->size}%");
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $boxes = $query->orderBy('name', 'asc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'total_boxes' => Product::where('category', 'like', '%box%')
                    ->orWhere('name', 'like', '%box%')
                    ->orWhere('sku', 'like', '%BOX%')->count(),
                'in_stock' => Product::where('category', 'like', '%box%')
                    ->orWhere('name', 'like', '%box%')
                    ->orWhere('sku', 'like', '%BOX%')
                    ->where('stock_quantity', '>', 0)->count(),
                'out_of_stock' => Product::where('category', 'like', '%box%')
                    ->orWhere('name', 'like', '%box%')
                    ->orWhere('sku', 'like', '%BOX%')
                    ->where('stock_quantity', 0)->count(),
                'total_value' => Product::where('category', 'like', '%box%')
                    ->orWhere('name', 'like', '%box%')
                    ->orWhere('sku', 'like', '%BOX%')
                    ->selectRaw('SUM(stock_quantity * normal_price) as total')
                    ->value('total') ?? 0
            ];

            return view('warehouse.boxes', compact('boxes', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading boxes: ' . $e->getMessage());
            return view('warehouse.boxes', [
                'boxes' => collect(),
                'stats' => []
            ]);
        }
    }

    /**
     * Get a single box
     */
    public function getBox(Request $request, $id)
    {
        try {
            $box = Product::findOrFail($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'box' => $box
                ]);
            }

            return response()->json($box);
        } catch (\Exception $e) {
            Log::error('Error getting box: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box not found'
                ], 404);
            }

            return response()->json(['error' => 'Box not found'], 404);
        }
    }

    /**
     * Store a new box
     */
    public function storeBox(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:255|unique:products,sku',
                'description' => 'nullable|string',
                'normal_price' => 'required|numeric|min:0',
                'sale_price' => 'required|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'status' => 'required|in:active,inactive',
                'size' => 'nullable|string|max:255',
                'image' => 'nullable|url'
            ]);

            $box = Product::create([
                'name' => $request->name,
                'sku' => $request->sku,
                'category' => 'Boxes',
                'description' => $request->description ?? '',
                'normal_price' => $request->normal_price,
                'sale_price' => $request->sale_price,
                'stock_quantity' => $request->stock_quantity,
                'status' => $request->status,
                'size' => $request->size,
                'images' => $request->image ?? ''
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Box created successfully',
                    'box' => $box
                ]);
            }

            return redirect()->route('warehouse.boxes')->with('success', 'Box created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating box: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create box: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to create box');
        }
    }

    /**
     * Update a box
     */
    public function updateBox(Request $request, $id)
    {
        try {
            $box = Product::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:255|unique:products,sku,' . $id,
                'description' => 'nullable|string',
                'normal_price' => 'required|numeric|min:0',
                'sale_price' => 'required|numeric|min:0',
                'status' => 'required|in:active,inactive',
                'size' => 'nullable|string|max:255',
                'image' => 'nullable|url'
            ]);

            $box->update([
                'name' => $request->name,
                'sku' => $request->sku,
                'description' => $request->description ?? '',
                'normal_price' => $request->normal_price,
                'sale_price' => $request->sale_price,
                'status' => $request->status,
                'size' => $request->size,
                'images' => $request->image ?? ''
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Box updated successfully',
                    'box' => $box
                ]);
            }

            return redirect()->route('warehouse.boxes')->with('success', 'Box updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating box: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update box: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to update box');
        }
    }

    /**
     * Delete a box
     */
    public function destroyBox(Request $request, $id)
    {
        try {
            $box = Product::findOrFail($id);
            $box->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Box deleted successfully'
                ]);
            }

            return redirect()->route('warehouse.boxes')->with('success', 'Box deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting box: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete box: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to delete box');
        }
    }

    /**
     * Update box stock
     */
    public function updateBoxStock(Request $request, $id)
    {
        try {
            $box = Product::findOrFail($id);

            $request->validate([
                'stock_quantity' => 'required|integer|min:0',
                'notes' => 'nullable|string|max:500'
            ]);

            $oldStock = $box->stock_quantity;
            $box->update([
                'stock_quantity' => $request->stock_quantity
            ]);

            // Log the stock change
            Log::info("Box stock updated", [
                'box_id' => $id,
                'box_name' => $box->name,
                'old_stock' => $oldStock,
                'new_stock' => $request->stock_quantity,
                'notes' => $request->notes
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock updated successfully',
                    'box' => $box
                ]);
            }

            return redirect()->route('warehouse.boxes')->with('success', 'Stock updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating box stock: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update stock: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to update stock');
        }
    }

    /**
     * Prime Bags Management
     */
    public function primeBags(Request $request)
    {
        try {
            // Get prime bags from products where category is 'Prime Bags'
            $query = Product::where('category', 'Prime Bags');

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('size')) {
                $query->where('size', $request->size);
            }

            if ($request->filled('color')) {
                $query->where('color', $request->color);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $primeBags = $query->orderBy('name', 'asc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'total_prime_bags' => Product::where('category', 'Prime Bags')->count(),
                'total_bags' => Product::where('category', 'Prime Bags')->count(),
                'in_stock' => Product::where('category', 'Prime Bags')
                    ->where('stock_quantity', '>', 0)->count(),
                'out_of_stock' => Product::where('category', 'Prime Bags')
                    ->where('stock_quantity', 0)->count(),
                'total_value' => Product::where('category', 'Prime Bags')
                    ->selectRaw('SUM(stock_quantity * normal_price) as total')
                    ->value('total') ?? 0
            ];

            return view('warehouse.prime-bags', compact('primeBags', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading prime bags: ' . $e->getMessage());
            return view('warehouse.prime-bags', [
                'primeBags' => collect(),
                'stats' => []
            ]);
        }
    }

    /**
     * Get a single prime bag
     */
    public function getPrimeBag(Request $request, $id)
    {
        try {
            $bag = Product::findOrFail($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'bag' => $bag
                ]);
            }

            return response()->json($bag);
        } catch (\Exception $e) {
            Log::error('Error getting prime bag: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prime bag not found'
                ], 404);
            }

            return response()->json(['error' => 'Prime bag not found'], 404);
        }
    }

    /**
     * Store a new prime bag
     */
    public function storePrimeBag(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:255|unique:products,sku',
                'description' => 'nullable|string',
                'normal_price' => 'required|numeric|min:0',
                'sale_price' => 'required|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'status' => 'required|in:active,inactive',
                'size' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:255',
                'image' => 'nullable|url'
            ]);

            $bag = Product::create([
                'name' => $request->name,
                'sku' => $request->sku,
                'category' => 'Prime Bags',
                'description' => $request->description ?? '',
                'normal_price' => $request->normal_price,
                'sale_price' => $request->sale_price,
                'stock_quantity' => $request->stock_quantity,
                'status' => $request->status,
                'size' => $request->size,
                'color' => $request->color,
                'images' => $request->image ?? ''
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Prime bag created successfully',
                    'bag' => $bag
                ]);
            }

            return redirect()->route('warehouse.prime-bags')->with('success', 'Prime bag created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating prime bag: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create prime bag: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to create prime bag');
        }
    }

    /**
     * Update a prime bag
     */
    public function updatePrimeBag(Request $request, $id)
    {
        try {
            $bag = Product::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:255|unique:products,sku,' . $id,
                'description' => 'nullable|string',
                'normal_price' => 'required|numeric|min:0',
                'sale_price' => 'required|numeric|min:0',
                'status' => 'required|in:active,inactive',
                'size' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:255',
                'image' => 'nullable|url'
            ]);

            $bag->update([
                'name' => $request->name,
                'sku' => $request->sku,
                'description' => $request->description ?? '',
                'normal_price' => $request->normal_price,
                'sale_price' => $request->sale_price,
                'status' => $request->status,
                'size' => $request->size,
                'color' => $request->color,
                'images' => $request->image ?? ''
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Prime bag updated successfully',
                    'bag' => $bag
                ]);
            }

            return redirect()->route('warehouse.prime-bags')->with('success', 'Prime bag updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating prime bag: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update prime bag: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to update prime bag');
        }
    }

    /**
     * Delete a prime bag
     */
    public function destroyPrimeBag(Request $request, $id)
    {
        try {
            $bag = Product::findOrFail($id);
            $bag->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Prime bag deleted successfully'
                ]);
            }

            return redirect()->route('warehouse.prime-bags')->with('success', 'Prime bag deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting prime bag: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete prime bag: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to delete prime bag');
        }
    }

    /**
     * Update prime bag stock
     */
    public function updatePrimeBagStock(Request $request, $id)
    {
        try {
            $bag = Product::findOrFail($id);

            $request->validate([
                'stock_quantity' => 'required|integer|min:0',
                'notes' => 'nullable|string|max:500'
            ]);

            $oldStock = $bag->stock_quantity;
            $bag->update([
                'stock_quantity' => $request->stock_quantity
            ]);

            // Log the stock change
            Log::info("Prime bag stock updated", [
                'bag_id' => $id,
                'bag_name' => $bag->name,
                'old_stock' => $oldStock,
                'new_stock' => $request->stock_quantity,
                'notes' => $request->notes
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock updated successfully',
                    'bag' => $bag
                ]);
            }

            return redirect()->route('warehouse.prime-bags')->with('success', 'Stock updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating prime bag stock: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update stock: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to update stock');
        }
    }

    /**
     * Shopping Bags Management
     */
    public function shoppingBags(Request $request)
    {
        try {
            // Get shopping bags from products where category is 'Shopping Bags'
            $query = Product::where('category', 'Shopping Bags');

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('size')) {
                $query->where('size', $request->size);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $bags = $query->orderBy('name', 'asc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'total_bags' => Product::where('category', 'Shopping Bags')->count(),
                'in_stock' => Product::where('category', 'Shopping Bags')
                    ->where('stock_quantity', '>', 0)->count(),
                'out_of_stock' => Product::where('category', 'Shopping Bags')
                    ->where('stock_quantity', 0)->count(),
                'total_value' => Product::where('category', 'Shopping Bags')
                    ->selectRaw('SUM(stock_quantity * normal_price) as total')
                    ->value('total') ?? 0
            ];

            return view('warehouse.shopping-bags', compact('bags', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading shopping bags: ' . $e->getMessage());
            return view('warehouse.shopping-bags', [
                'bags' => collect(),
                'stats' => []
            ]);
        }
    }

    /**
     * Get a single shopping bag
     */
    public function getShoppingBag(Request $request, $id)
    {
        try {
            $bag = Product::findOrFail($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'bag' => $bag
                ]);
            }

            return response()->json($bag);
        } catch (\Exception $e) {
            Log::error('Error getting shopping bag: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shopping bag not found'
                ], 404);
            }

            return response()->json(['error' => 'Shopping bag not found'], 404);
        }
    }

    /**
     * Store a new shopping bag
     */
    public function storeShoppingBag(Request $request)
    {
        try {
            Log::info('Shopping bag creation request received', [
                'request_data' => $request->all(),
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson()
            ]);

            $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:255|unique:products,sku',
                'description' => 'nullable|string',
                'normal_price' => 'required|numeric|min:0',
                'sale_price' => 'required|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'status' => 'required|in:active,inactive',
                'size' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:255',
                'image' => 'nullable|url'
            ], [
                'sku.unique' => 'This SKU already exists. Please choose a different SKU.',
                'normal_price.required' => 'Normal price is required.',
                'sale_price.required' => 'Sale price is required.',
                'stock_quantity.required' => 'Stock quantity is required.'
            ]);

            $bag = Product::create([
                'name' => $request->name,
                'sku' => $request->sku,
                'category' => 'Shopping Bags',
                'description' => $request->description ?? '',
                'normal_price' => $request->normal_price,
                'sale_price' => $request->sale_price,
                'stock_quantity' => $request->stock_quantity,
                'status' => $request->status,
                'size' => $request->size,
                'color' => $request->color,
                'images' => $request->image ?? ''
            ]);

            Log::info('Shopping bag created successfully', [
                'bag_id' => $bag->id,
                'bag_name' => $bag->name,
                'bag_sku' => $bag->sku
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Shopping bag created successfully',
                    'bag' => $bag
                ]);
            }

            return redirect()->route('warehouse.shopping-bags')->with('success', 'Shopping bag created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error creating shopping bag: ' . $e->getMessage(), [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                $errorMessage = 'Validation failed';
                if (isset($e->errors()['sku']) && in_array('The sku has already been taken.', $e->errors()['sku'])) {
                    $errorMessage = 'This SKU already exists. Please choose a different SKU.';
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => $e->errors(),
                    'debug' => [
                        'sku_used' => $request->sku,
                        'existing_sku_check' => \App\Models\Product::where('sku', $request->sku)->exists()
                    ]
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating shopping bag: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create shopping bag: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to create shopping bag');
        }
    }

    /**
     * Update a shopping bag
     */
    public function updateShoppingBag(Request $request, $id)
    {
        try {
            $bag = Product::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:255|unique:products,sku,' . $id,
                'description' => 'nullable|string',
                'normal_price' => 'required|numeric|min:0',
                'sale_price' => 'required|numeric|min:0',
                'status' => 'required|in:active,inactive',
                'size' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:255',
                'image' => 'nullable|url'
            ]);

            $bag->update([
                'name' => $request->name,
                'sku' => $request->sku,
                'description' => $request->description ?? '',
                'normal_price' => $request->normal_price,
                'sale_price' => $request->sale_price,
                'status' => $request->status,
                'size' => $request->size,
                'color' => $request->color,
                'images' => $request->image ?? ''
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Shopping bag updated successfully',
                    'bag' => $bag
                ]);
            }

            return redirect()->route('warehouse.shopping-bags')->with('success', 'Shopping bag updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating shopping bag: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update shopping bag: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to update shopping bag');
        }
    }

    /**
     * Delete a shopping bag
     */
    public function destroyShoppingBag(Request $request, $id)
    {
        try {
            $bag = Product::findOrFail($id);
            $bag->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Shopping bag deleted successfully'
                ]);
            }

            return redirect()->route('warehouse.shopping-bags')->with('success', 'Shopping bag deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting shopping bag: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete shopping bag: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to delete shopping bag');
        }
    }

    /**
     * Update shopping bag stock
     */
    public function updateShoppingBagStock(Request $request, $id)
    {
        try {
            $bag = Product::findOrFail($id);

            $request->validate([
                'stock_quantity' => 'required|integer|min:0',
                'notes' => 'nullable|string|max:500'
            ]);

            $oldStock = $bag->stock_quantity;
            $bag->update([
                'stock_quantity' => $request->stock_quantity
            ]);

            // Log the stock change
            Log::info("Shopping bag stock updated", [
                'bag_id' => $id,
                'bag_name' => $bag->name,
                'old_stock' => $oldStock,
                'new_stock' => $request->stock_quantity,
                'notes' => $request->notes
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock updated successfully',
                    'bag' => $bag
                ]);
            }

            return redirect()->route('warehouse.shopping-bags')->with('success', 'Stock updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating shopping bag stock: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update stock: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to update stock');
        }
    }


    /**
     * Flyerz Management
     */
    public function flyerz(Request $request)
    {
        try {
            // Get flyerz from products where category is 'Flyerz'
            $query = Product::where('category', 'Flyerz');

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('size')) {
                $query->where('size', $request->size);
            }

            if ($request->filled('color')) {
                $query->where('color', $request->color);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $flyerz = $query->orderBy('name', 'asc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'total_flyerz' => Product::where('category', 'Flyerz')->count(),
                'in_stock' => Product::where('category', 'Flyerz')
                    ->where('stock_quantity', '>', 0)->count(),
                'out_of_stock' => Product::where('category', 'Flyerz')
                    ->where('stock_quantity', 0)->count(),
                'total_value' => Product::where('category', 'Flyerz')
                    ->selectRaw('SUM(stock_quantity * normal_price) as total')
                    ->value('total') ?? 0
            ];

            return view('warehouse.flyerz', compact('flyerz', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading flyerz: ' . $e->getMessage());
            return view('warehouse.flyerz', [
                'flyerz' => collect(),
                'stats' => []
            ]);
        }
    }

    /**
     * Get a single flyer
     */
    public function getFlyer(Request $request, $id)
    {
        try {
            $flyer = Product::findOrFail($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'flyer' => $flyer
                ]);
            }

            return response()->json($flyer);
        } catch (\Exception $e) {
            Log::error('Error getting flyer: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Flyer not found'
                ], 404);
            }

            return response()->json(['error' => 'Flyer not found'], 404);
        }
    }

    /**
     * Store a new flyer
     */
    public function storeFlyer(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:255|unique:products,sku',
                'description' => 'nullable|string',
                'normal_price' => 'required|numeric|min:0',
                'sale_price' => 'required|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'status' => 'required|in:active,inactive',
                'size' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:255',
                'image' => 'nullable|url'
            ]);

            $flyer = Product::create([
                'name' => $request->name,
                'sku' => $request->sku,
                'category' => 'Flyerz',
                'description' => $request->description ?? '',
                'normal_price' => $request->normal_price,
                'sale_price' => $request->sale_price,
                'stock_quantity' => $request->stock_quantity,
                'status' => $request->status,
                'size' => $request->size,
                'color' => $request->color,
                'images' => $request->image ?? ''
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Flyer created successfully',
                    'flyer' => $flyer
                ]);
            }

            return redirect()->route('warehouse.flyerz')->with('success', 'Flyer created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating flyer: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create flyer: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to create flyer');
        }
    }

    /**
     * Update a flyer
     */
    public function updateFlyer(Request $request, $id)
    {
        try {
            $flyer = Product::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:255|unique:products,sku,' . $id,
                'description' => 'nullable|string',
                'normal_price' => 'required|numeric|min:0',
                'sale_price' => 'required|numeric|min:0',
                'status' => 'required|in:active,inactive',
                'size' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:255',
                'image' => 'nullable|url'
            ]);

            $flyer->update([
                'name' => $request->name,
                'sku' => $request->sku,
                'description' => $request->description ?? '',
                'normal_price' => $request->normal_price,
                'sale_price' => $request->sale_price,
                'status' => $request->status,
                'size' => $request->size,
                'color' => $request->color,
                'images' => $request->image ?? ''
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Flyer updated successfully',
                    'flyer' => $flyer
                ]);
            }

            return redirect()->route('warehouse.flyerz')->with('success', 'Flyer updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating flyer: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update flyer: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to update flyer');
        }
    }

    /**
     * Delete a flyer
     */
    public function destroyFlyer(Request $request, $id)
    {
        try {
            $flyer = Product::findOrFail($id);
            $flyer->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Flyer deleted successfully'
                ]);
            }

            return redirect()->route('warehouse.flyerz')->with('success', 'Flyer deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting flyer: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete flyer: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to delete flyer');
        }
    }

    /**
     * Update flyer stock
     */
    public function updateFlyerStock(Request $request, $id)
    {
        try {
            $flyer = Product::findOrFail($id);

            $request->validate([
                'stock_quantity' => 'required|integer|min:0',
                'notes' => 'nullable|string|max:500'
            ]);

            $oldStock = $flyer->stock_quantity;
            $flyer->update([
                'stock_quantity' => $request->stock_quantity
            ]);

            // Log the stock change
            Log::info("Flyer stock updated", [
                'flyer_id' => $id,
                'flyer_name' => $flyer->name,
                'old_stock' => $oldStock,
                'new_stock' => $request->stock_quantity,
                'notes' => $request->notes
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock updated successfully',
                    'flyer' => $flyer
                ]);
            }

            return redirect()->route('warehouse.flyerz')->with('success', 'Stock updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating flyer stock: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update stock: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to update stock');
        }
    }


    /**
     * Update Material Stock
     */
    public function updateMaterialStock(Request $request, $materialId)
    {
        try {
            $request->validate([
                'stock_quantity' => 'required|integer|min:0',
                'notes' => 'nullable|string|max:500'
            ]);

            $material = Product::findOrFail($materialId);

            $oldStock = $material->stock_quantity;
            $material->stock_quantity = $request->stock_quantity;
            $material->save();

            // Log the change
            Log::info("Material stock updated: {$material->name} from {$oldStock} to {$request->stock_quantity}");

            return back()->with('success', 'Material stock updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating material stock: ' . $e->getMessage());
            return back()->with('error', 'Failed to update material stock.');
        }
    }

    /**
     * Bulk Update Materials Stock
     */
    public function bulkUpdateMaterialsStock(Request $request)
    {
        try {
            $request->validate([
                'materials' => 'required|array',
                'materials.*.id' => 'required|exists:products,id',
                'materials.*.stock_quantity' => 'required|integer|min:0'
            ]);

            $updated = 0;
            foreach ($request->materials as $materialData) {
                $material = Product::find($materialData['id']);
                if ($material) {
                    $material->stock_quantity = $materialData['stock_quantity'];
                    $material->save();
                    $updated++;
                }
            }

            Log::info("Bulk updated {$updated} materials stock");

            return back()->with('success', "Successfully updated {$updated} materials stock.");
        } catch (\Exception $e) {
            Log::error('Error bulk updating materials stock: ' . $e->getMessage());
            return back()->with('error', 'Failed to update materials stock.');
        }
    }

    /**
     * ========================================
     * FEEDING REQUESTS METHODS
     * ========================================
     */

    /**
     * Feeding Requests
     */
    public function feedingRequests(Request $request)
    {
        try {
            // Get feeding requests
            $query = Order::whereIn('status', [
                config('constants.FEEDING_REQUESTED'),
                config('constants.FEEDING_PROCESSING'),
                config('constants.FEEDING_COMPLETED'),
                config('constants.FEEDING_REJECTED')
            ]);

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('phone_numbers', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->filled('priority')) {
                $query->where('notes', 'like', "%{$request->priority}%");
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $feedingRequests = $query->with(['customer', 'products'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'total_requests' => Order::where('status', config('constants.FEEDING_REQUESTED'))
                    ->orWhere('status', config('constants.FEEDING_PROCESSING'))
                    ->orWhere('status', config('constants.FEEDING_COMPLETED'))
                    ->orWhere('status', config('constants.FEEDING_REJECTED'))->count(),
                'urgent_requests' => Order::where('status', config('constants.FEEDING_REQUESTED'))
                    ->where('notes', 'like', '%urgent%')->count(),
                'pending_requests' => Order::where('status', config('constants.FEEDING_REQUESTED'))->count(),
                'completed_requests' => Order::where('status', config('constants.FEEDING_COMPLETED'))->count(),
                'total_value' => Order::where('status', config('constants.FEEDING_REQUESTED'))
                    ->orWhere('status', config('constants.FEEDING_PROCESSING'))
                    ->orWhere('status', config('constants.FEEDING_COMPLETED'))
                    ->sum('total')
            ];

            // Get products for the modal
            $products = Product::select('id', 'name', 'sku', 'normal_price')->get();

            return view('warehouse.feeding-requests', compact('feedingRequests', 'stats', 'products'));
        } catch (\Exception $e) {
            Log::error('Error loading feeding requests: ' . $e->getMessage());
            return view('warehouse.feeding-requests', [
                'feedingRequests' => collect(),
                'stats' => []
            ]);
        }
    }

    /**
     * Process Feeding Request
     */
    public function processFeedingRequest(Request $request, $orderId)
    {
        try {
            $request->validate([
                'action' => 'required|in:approve,hold,reject,complete',
                'notes' => 'nullable|string|max:500'
            ]);

            $order = Order::findOrFail($orderId);

            if (!in_array($order->status, [
                config('constants.FEEDING_REQUESTED'),
                config('constants.FEEDING_PROCESSING'),
                config('constants.FEEDING_COMPLETED'),
                config('constants.FEEDING_REJECTED')
            ])) {
                return back()->with('error', 'Order is not in a valid feeding status.');
            }

            switch ($request->action) {
                case 'approve':
                    $order->status = config('constants.FEEDING_PROCESSING');
                    $order->notes = ($order->notes ?? '') . "\nFeeding request approved: " . ($request->notes ?? 'No notes provided');
                    break;
                case 'hold':
                    $order->status = config('constants.FEEDING_REQUESTED');
                    $order->notes = ($order->notes ?? '') . "\nFeeding request on hold: " . ($request->notes ?? 'No notes provided');
                    break;
                case 'reject':
                    $order->status = config('constants.FEEDING_REJECTED');
                    $order->notes = ($order->notes ?? '') . "\nFeeding request rejected: " . ($request->notes ?? 'No notes provided');
                    break;
                case 'complete':
                    $order->status = config('constants.FEEDING_COMPLETED');
                    $order->notes = ($order->notes ?? '') . "\nFeeding request completed: " . ($request->notes ?? 'No notes provided');

                    // Update stock quantities for all products in the order
                    $this->updateStockQuantities($order);
                    break;
            }

            $order->save();

            Log::info("Feeding request {$orderId} {$request->action}d");

            if ($request->expectsJson()) {
                $message = "Feeding request {$request->action}d successfully.";
                if ($request->action === 'complete') {
                    $message .= " Stock quantities have been updated.";
                }

                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            $successMessage = "Feeding request {$request->action}d successfully.";
            if ($request->action === 'complete') {
                $successMessage .= " Stock quantities have been updated.";
            }

            return back()->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Error processing feeding request: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process feeding request: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to process feeding request.');
        }
    }

    /**
     * Update Stock Quantities for Completed Feeding Request
     */
    private function updateStockQuantities($order)
    {
        try {
            // Get all products in the order with their quantities
            $orderProducts = $order->products()->withPivot('quantity')->get();

            foreach ($orderProducts as $product) {
                $quantity = $product->pivot->quantity;

                // Update stock quantity (add to warehouse stock)
                $product->stock_quantity = $product->stock_quantity + $quantity;
                $product->save();

                Log::info("Updated stock for product {$product->name} (ID: {$product->id}): +{$quantity} units. New stock: {$product->stock_quantity}");
            }

            Log::info("Stock quantities updated for feeding request {$order->order_number}");
        } catch (\Exception $e) {
            Log::error('Error updating stock quantities: ' . $e->getMessage());
            // Don't throw exception to avoid breaking the completion process
        }
    }

    /**
     * Create Feeding Request
     */
    public function createFeedingRequest(Request $request)
    {
        try {
            $request->validate([
                'order_number' => 'required|string|max:255',
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'priority' => 'required|in:urgent,high,medium,low',
                'products' => 'required|array|min:1',
                'products.*' => 'required|exists:products,id',
                'quantities' => 'required|array|min:1',
                'quantities.*' => 'required|integer|min:1',
                'notes' => 'nullable|string|max:500'
            ]);

            // Check if order number already exists
            $existingOrder = Order::where('order_number', $request->order_number)->first();
            if ($existingOrder) {
                return back()->with('error', 'Order number already exists. Please use a different order number.');
            }

            // Find or create customer
            $customer = Lead::where('phone_numbers', $request->customer_phone)
                ->where('is_customer', '1')
                ->first();

            if (!$customer) {
                $customer = Lead::create([
                    'name' => $request->customer_name,
                    'phone_numbers' => $request->customer_phone,
                    'is_customer' => '1',
                    'lead_id' => 'CUST_' . time() . '_' . rand(1000, 9999),
                    'email' => '',
                    'governorate' => '',
                    'interested_categories' => '',
                    'interested_products_skus' => '',
                    'source' => 'warehouse',
                    'degree_of_interest' => '',
                    'next_follow_up_period' => '',
                    'potential' => '',
                    'added_by' => auth()->check() ? auth()->user()->name : 'system',
                    'assigned_to' => '',
                    'notes' => 'Created via feeding request'
                ]);
            } else {
                // Update customer name if different
                if ($customer->name !== $request->customer_name) {
                    $customer->name = $request->customer_name;
                    $customer->save();
                }
            }

            // Calculate total
            $total = 0;
            $products = Product::whereIn('id', $request->products)->get();
            foreach ($request->products as $index => $productId) {
                $product = $products->find($productId);
                $quantity = $request->quantities[$index];
                $total += $product->normal_price * $quantity;
            }

            // Create order
            $order = Order::create([
                'order_number' => $request->order_number,
                'customer_id' => $customer->lead_id,
                'status' => config('constants.FEEDING_REQUESTED'),
                'address' => 'Warehouse - Feeding Request',
                'latitude' => '0',
                'longitude' => '0',
                'total' => $total,
                'notes' => $request->notes . "\nPriority: " . ucfirst($request->priority),
                'employee_commission' => '0',
                'governorate' => '',
                'coupon_code' => '',
                'delivery_agent_id' => '',
                'employee_id' => auth()->check() ? auth()->user()->user_id : '1'
            ]);

            // Attach products to order
            foreach ($request->products as $index => $productId) {
                $quantity = $request->quantities[$index];
                $order->products()->attach($productId, ['quantity' => $quantity]);
            }

            Log::info("New feeding request created: {$order->order_number}");

            return back()->with('success', 'Feeding request created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating feeding request: ' . $e->getMessage());
            return back()->with('error', 'Failed to create feeding request. Please try again.');
        }
    }

    /**
     * Get Order by Number (API)
     */
    public function getOrderByNumber($orderNumber)
    {
        try {
            $order = Order::where('order_number', $orderNumber)
                ->with(['customer', 'products'])
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer' => $order->customer ? [
                        'name' => $order->customer->name,
                        'phone' => $order->customer->phone_numbers
                    ] : null,
                    'products' => $order->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'sku' => $product->sku,
                            'price' => $product->normal_price,
                            'quantity' => $product->pivot->quantity
                        ];
                    }),
                    'total' => $order->total,
                    'status' => $order->status,
                    'notes' => $order->notes
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching order by number: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order details'
            ], 500);
        }
    }

    /**
     * Exit Permission
     */
    public function exitPermission(Request $request)
    {
        try {
            // Get exit permissions
            $query = Order::where('status', config('constants.EXIT_REQUESTED'))
                ->orWhere('status', config('constants.EXIT_APPROVED'))
                ->orWhere('status', config('constants.EXIT_SHIPPED'));

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('phone_numbers', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->filled('permission_status')) {
                switch ($request->permission_status) {
                    case 'pending':
                        $query->where('status', config('constants.EXIT_REQUESTED'));
                        break;
                    case 'approved':
                        $query->where('status', config('constants.EXIT_APPROVED'));
                        break;
                    case 'shipped':
                        $query->where('status', config('constants.EXIT_SHIPPED'));
                        break;
                }
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $exitPermissions = $query->with(['customer', 'products', 'deliveryAgent'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'total_permissions' => Order::whereIn('status', [
                    config('constants.EXIT_REQUESTED'),
                    config('constants.EXIT_APPROVED'),
                    config('constants.EXIT_SHIPPED')
                ])->count(),
                'pending_approval' => Order::where('status', config('constants.EXIT_REQUESTED'))->count(),
                'approved_permissions' => Order::where('status', config('constants.EXIT_APPROVED'))->count(),
                'total_value' => Order::whereIn('status', [
                    config('constants.EXIT_REQUESTED'),
                    config('constants.EXIT_APPROVED'),
                    config('constants.EXIT_SHIPPED')
                ])->sum('total')
            ];

            // Get orders ready for exit permission
            // Only orders that are OUT_FOR_DELIVERY, assigned to a driver, and have products with stock
            $readyOrders = Order::where('status', config('constants.OUT_FOR_DELIVERY'))
                ->whereNotNull('delivery_agent_id') // Only orders assigned to a driver
                ->whereHas('products') // Must have products
                ->whereDoesntHave('products', function ($query) {
                    $query->where('stock_quantity', '0');
                })
                ->with(['customer', 'products', 'deliveryAgent'])
                ->get();

            // Get delivery agents
            $deliveryAgents = User::whereHas('role', function ($query) {
                $query->where('role_code', 'delivery_agent');
            })->get();

            return view('warehouse.exit-permission', compact('exitPermissions', 'stats', 'readyOrders', 'deliveryAgents'));
        } catch (\Exception $e) {
            Log::error('Error loading exit permissions: ' . $e->getMessage());
            return view('warehouse.exit-permission', [
                'exitPermissions' => collect(),
                'stats' => []
            ]);
        }
    }

    /**
     * Process Exit Permission
     */
    public function processExitPermission(Request $request, $orderId)
    {
        try {
            Log::info('Processing exit permission request:', [
                'order_id' => $orderId,
                'action' => $request->action,
                'notes' => $request->notes,
                'all_data' => $request->all()
            ]);

            $request->validate([
                'action' => 'required|in:approve,reject,ship',
                'notes' => 'nullable|string|max:500'
            ]);

            $order = Order::findOrFail($orderId);

            if (!in_array($order->status, [
                config('constants.EXIT_REQUESTED'),
                config('constants.EXIT_APPROVED')
            ])) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Order is not in a valid exit permission status.'
                    ], 400);
                }
                return back()->with('error', 'Order is not in a valid exit permission status.');
            }

            switch ($request->action) {
                case 'approve':
                    $order->status = config('constants.EXIT_APPROVED');
                    $order->notes = ($order->notes ?? '') . "\nExit permission approved: " . ($request->notes ?? 'No notes provided');
                    break;
                case 'reject':
                    $order->status = config('constants.EXIT_REJECTED');
                    $order->notes = ($order->notes ?? '') . "\nExit permission rejected: " . ($request->notes ?? 'No notes provided');
                    break;
                case 'ship':
                    $order->status = config('constants.EXIT_SHIPPED');
                    // Get delivery agent name from the existing delivery_agent_id
                    $deliveryAgentName = 'Unknown Driver';
                    if ($order->deliveryAgent) {
                        $deliveryAgentName = $order->deliveryAgent->name;
                    }
                    $order->notes = ($order->notes ?? '') . "\nExit permission shipped by: " . $deliveryAgentName;
                    break;
            }

            $order->save();

            Log::info("Exit permission {$orderId} {$request->action}d");

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Exit permission {$request->action}d successfully."
                ]);
            }

            return back()->with('success', "Exit permission {$request->action}d successfully.");
        } catch (\Exception $e) {
            Log::error('Error processing exit permission: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'action' => $request->action ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process exit permission: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to process exit permission.');
        }
    }

    /**
     * Bulk Process Exit Permissions
     */
    public function bulkProcessExitPermissions(Request $request)
    {
        try {
            $request->validate([
                'action' => 'required|in:approve,reject',
                'permission_ids' => 'required|string',
                'notes' => 'nullable|string|max:500'
            ]);

            $permissionIds = json_decode($request->permission_ids, true);

            if (!is_array($permissionIds) || empty($permissionIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid permission IDs provided.'
                ], 400);
            }

            $orders = Order::whereIn('id', $permissionIds)
                ->whereIn('status', [
                    config('constants.EXIT_REQUESTED'),
                    config('constants.EXIT_APPROVED')
                ])
                ->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid permissions found to process.'
                ], 400);
            }

            $processedCount = 0;
            foreach ($orders as $order) {
                switch ($request->action) {
                    case 'approve':
                        $order->status = config('constants.EXIT_APPROVED');
                        $order->notes = ($order->notes ?? '') . "\nBulk approved: " . ($request->notes ?? 'No notes provided');
                        break;
                    case 'reject':
                        $order->status = config('constants.EXIT_REJECTED');
                        $order->notes = ($order->notes ?? '') . "\nBulk rejected: " . ($request->notes ?? 'No notes provided');
                        break;
                }
                $order->save();
                $processedCount++;
            }

            Log::info("Bulk {$request->action} for {$processedCount} exit permissions");

            return response()->json([
                'success' => true,
                'message' => "Successfully processed {$processedCount} permissions."
            ]);
        } catch (\Exception $e) {
            Log::error('Error in bulk processing exit permissions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process permissions.'
            ], 500);
        }
    }

    /**
     * Create New Exit Permission
     */
    public function createExitPermission(Request $request)
    {
        try {
            // Log the request data
            Log::info('Create exit permission request data:', $request->all());

            // Check if delivery_agent_id is provided
            if (!$request->has('delivery_agent_id') || empty($request->delivery_agent_id)) {
                Log::error('Missing delivery_agent_id in request');
                return back()->with('error', 'Delivery agent ID is missing. Please refresh the page and try again.');
            }

            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'delivery_agent_id' => 'required|string',
                'expected_delivery_date' => 'required|date|after_or_equal:today',
                'notes' => 'nullable|string|max:500'
            ]);

            $order = Order::findOrFail($request->order_id);
            Log::info("Order found: {$order->id}, current delivery_agent_id: {$order->delivery_agent_id}");

            // Check if order is in out for delivery status
            if ($order->status !== config('constants.OUT_FOR_DELIVERY')) {
                return back()->with('error', 'Order must be out for delivery to create exit permission.');
            }

            // Check if all products are in stock
            $outOfStockProducts = $order->products()->where('stock_quantity', '0')->count();
            if ($outOfStockProducts > 0) {
                return back()->with('error', 'Cannot create exit permission. Some products are out of stock.');
            }

            // Update order status to exit requested
            $order->status = config('constants.EXIT_REQUESTED');
            $order->delivery_agent_id = $request->delivery_agent_id;
            $order->expected_delivery_date = $request->expected_delivery_date;
            $order->notes = ($order->notes ?? '') . "\nExit permission created: " . ($request->notes ?? 'No notes provided');
            $order->save();

            Log::info("Exit permission created for order {$order->id}");

            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Exit permission created successfully.',
                    'order_id' => $order->id
                ]);
            }

            return back()->with('success', 'Exit permission created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating exit permission: ' . $e->getMessage());

            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create exit permission: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to create exit permission.');
        }
    }

    /**
     * Exit Permission Details
     */
    public function exitPermissionDetails($orderId)
    {
        try {
            $order = Order::with(['customer', 'products', 'deliveryAgent', 'employee'])
                ->findOrFail($orderId);

            // Check if order is in exit permission status
            if (!in_array($order->status, [
                config('constants.EXIT_REQUESTED'),
                config('constants.EXIT_APPROVED'),
                config('constants.EXIT_REJECTED'),
                config('constants.EXIT_SHIPPED')
            ])) {
                abort(404, 'Order not found in exit permission status.');
            }

            return view('warehouse.exit-permission-details', compact('order'));
        } catch (\Exception $e) {
            Log::error('Error loading exit permission details: ' . $e->getMessage());
            abort(404, 'Order not found.');
        }
    }

    /**
     * Get Order Products API
     */
    public function getOrderProducts($orderId)
    {
        try {
            // Log the request
            Log::info("Fetching products for order ID: {$orderId}");

            $order = Order::with(['products' => function ($query) {
                $query->select('products.id', 'products.name', 'products.sku', 'products.stock_quantity')
                    ->withPivot('quantity');
            }])->findOrFail($orderId);

            Log::info("Order found: " . $order->order_number);
            Log::info("Products count: " . $order->products->count());

            $products = $order->products->map(function ($product) {
                return [
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $product->pivot->quantity,
                    'stock_quantity' => $product->stock_quantity
                ];
            });

            Log::info("Products mapped successfully");
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Error fetching order products: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to fetch products',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * DAMAGED GOODS METHODS
     * ========================================
     */

    /**
     * Damaged Goods
     */
    public function damagedGoods(Request $request)
    {
        try {
            // Get damaged goods (excluding materials: Boxes, Shopping Bags, Prime Bags, Flyerz)
            $query = DamagedItem::with(['product', 'reporter'])
                ->goods(); // This scope excludes materials

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('damage_level')) {
                $query->byDamageLevel($request->damage_level);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('reported_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('reported_at', '<=', $request->date_to);
            }

            $damagedGoods = $query->orderBy('reported_at', 'desc')
                ->paginate(20);

            // Get statistics for goods only
            $goodsQuery = DamagedItem::goods();
            $stats = [
                'total_damaged' => $goodsQuery->count(),
                'critical_damage' => $goodsQuery->byDamageLevel('severe')->count(),
                'repairable' => $goodsQuery->byDamageLevel('minor')->count(),
                'total_loss' => $goodsQuery->with('product')
                    ->get()
                    ->sum(function ($item) {
                        return $item->damaged_quantity * $item->product->normal_price;
                    })
            ];

            return view('warehouse.damaged-goods', compact('damagedGoods', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading damaged goods: ' . $e->getMessage());
            return view('warehouse.damaged-goods', [
                'damagedGoods' => collect(),
                'stats' => []
            ]);
        }
    }

    /**
     * Damaged Materials
     */
    public function damagedMaterials(Request $request)
    {
        try {
            // Get damaged materials (Boxes, Shopping Bags, Prime Bags, Flyerz)
            $query = DamagedItem::with(['product', 'reporter'])
                ->materials(); // This scope includes only materials

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('material_type')) {
                $query->whereHas('product', function ($q) use ($request) {
                    $q->where('category', $request->material_type);
                });
            }

            if ($request->filled('damage_level')) {
                $query->byDamageLevel($request->damage_level);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('reported_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('reported_at', '<=', $request->date_to);
            }

            $damagedMaterials = $query->orderBy('reported_at', 'desc')
                ->paginate(20);

            // Get statistics for materials only
            $materialsQuery = DamagedItem::materials();
            $stats = [
                'total_damaged_materials' => $materialsQuery->count(),
                'critical_damage' => $materialsQuery->byDamageLevel('severe')->count(),
                'repairable' => $materialsQuery->byDamageLevel('minor')->count(),
                'total_loss_value' => $materialsQuery->with('product')
                    ->get()
                    ->sum(function ($item) {
                        return $item->damaged_quantity * $item->product->normal_price;
                    })
            ];

            return view('warehouse.damaged-materials', compact('damagedMaterials', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading damaged materials: ' . $e->getMessage());
            return view('warehouse.damaged-materials', [
                'damagedMaterials' => collect(),
                'stats' => []
            ]);
        }
    }

    /**
     * Mark Product as Damaged
     */
    public function markAsDamaged(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'damaged_quantity' => 'required|integer|min:1',
                'damage_level' => 'required|in:minor,moderate,severe',
                'damage_reason' => 'nullable|string|max:500',
                'notes' => 'nullable|string|max:1000'
            ]);

            $product = Product::findOrFail($request->product_id);

            // Check if there's enough stock
            if ($product->stock_quantity < $request->damaged_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'الكمية المطلوبة أكبر من المخزون المتاح'
                ], 400);
            }

            // Create damaged item record
            $damagedItem = DamagedItem::create([
                'product_id' => $request->product_id,
                'damaged_quantity' => $request->damaged_quantity,
                'damage_level' => $request->damage_level,
                'damage_reason' => $request->damage_reason,
                'reported_by' => auth()->id(),
                'reported_at' => now(),
                'status' => 'reported',
                'notes' => $request->notes
            ]);

            // Deduct from stock
            $product->decrement('stock_quantity', $request->damaged_quantity);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل التلف بنجاح',
                'data' => $damagedItem->load(['product', 'reporter'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking product as damaged: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل التلف'
            ], 500);
        }
    }

    /**
     * Get available products for marking as damaged
     */
    public function getAvailableProducts(Request $request)
    {
        try {
            $type = $request->get('type', 'goods'); // 'goods' or 'materials'

            if ($type === 'materials') {
                $products = Product::whereIn('category', ['Boxes', 'Shopping Bags', 'Prime Bags', 'Flyerz'])
                    ->where('stock_quantity', '>', 0)
                    ->orderBy('name')
                    ->get(['id', 'name', 'sku', 'stock_quantity', 'normal_price', 'category']);
            } else {
                $products = Product::whereNotIn('category', ['Boxes', 'Shopping Bags', 'Prime Bags', 'Flyerz'])
                    ->where('stock_quantity', '>', 0)
                    ->orderBy('name')
                    ->get(['id', 'name', 'sku', 'stock_quantity', 'normal_price', 'category']);
            }

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting available products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المنتجات'
            ], 500);
        }
    }

    /**
     * Update damaged item status
     */
    public function updateDamagedItemStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:repaired,disposed',
                'notes' => 'nullable|string|max:1000'
            ]);

            $damagedItem = DamagedItem::findOrFail($id);
            $damagedItem->update([
                'status' => $request->status,
                'notes' => $request->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الحالة بنجاح'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating damaged item status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الحالة'
            ], 500);
        }
    }

    /**
     * Mark Product as Damaged (Old method - keeping for compatibility)
     */
    public function markAsDamagedOld(Request $request, $productId)
    {
        try {
            $request->validate([
                'damage_quantity' => 'required|integer|min:1',
                'damage_type' => 'required|string|max:255',
                'severity' => 'required|in:minor,moderate,severe',
                'damage_reason' => 'nullable|string|max:500'
            ]);

            $product = Product::findOrFail($productId);

            // Reduce stock by damaged quantity (make it negative to track damaged items)
            $product->stock_quantity -= $request->damage_quantity;
            $product->notes = ($product->notes ?? '') . "\nDamaged: {$request->damage_type} ({$request->severity}). Reason: " . ($request->damage_reason ?? 'No reason provided');
            $product->save();

            Log::info("Product {$productId} marked as damaged: {$request->damage_quantity} units");

            return back()->with('success', 'Product marked as damaged successfully.');
        } catch (\Exception $e) {
            Log::error('Error marking product as damaged: ' . $e->getMessage());
            return back()->with('error', 'Failed to mark product as damaged.');
        }
    }

    /**
     * ============================================
     *  SEND TO MOVE MANAGER - WAITING SEND
     * ============================================
     */

    /**
     * Show orders waiting to be assigned to drivers
     */
    public function waitingSend(Request $request)
    {
        try {
            // Get orders that are ready for delivery but not assigned to any driver
            $query = Order::whereNull('delivery_agent_id')
                ->where('status', config('constants.OUT_FOR_DELIVERY'))
                ->with(['customer', 'products']);

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('phone_numbers', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->filled('priority')) {
                if ($request->priority === 'urgent') {
                    $query->where('notes', 'like', '%urgent%');
                }
            }

            if ($request->filled('governorate')) {
                $query->where('governorate', $request->governorate);
            }

            if ($request->filled('date')) {
                $query->whereDate('created_at', $request->date);
            }

            $orders = $query->orderBy('created_at', 'desc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'waiting_orders' => Order::whereNull('delivery_agent_id')
                    ->where('status', config('constants.OUT_FOR_DELIVERY'))
                    ->count(),
                'urgent_orders' => Order::whereNull('delivery_agent_id')
                    ->where('status', config('constants.OUT_FOR_DELIVERY'))
                    ->where('notes', 'like', '%urgent%')
                    ->count(),
                'available_drivers' => User::whereHas('role', function ($q) {
                    $q->where('role_code', 'driver');
                })->count(),
                'total_value' => Order::whereNull('delivery_agent_id')
                    ->where('status', config('constants.OUT_FOR_DELIVERY'))
                    ->sum('total'),
            ];

            // Get available drivers
            $drivers = User::whereHas('role', function ($q) {
                $q->where('role_code', 'driver');
            })->get();

            // Get all governorates for filter
            $governorates = Order::select('governorate')
                ->distinct()
                ->whereNotNull('governorate')
                ->pluck('governorate');

            return view('warehouse.waiting-send', compact('orders', 'stats', 'drivers', 'governorates'));
        } catch (\Exception $e) {
            Log::error('Error loading waiting send orders: ' . $e->getMessage());

            // Create empty paginator
            $orders = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $stats = [
                'waiting_orders' => 0,
                'urgent_orders' => 0,
                'available_drivers' => 0,
                'total_value' => 0,
            ];
            $drivers = collect();
            $governorates = collect();

            return view('warehouse.waiting-send', compact('orders', 'stats', 'drivers', 'governorates'));
        }
    }

    /**
     * Assign order to driver
     */
    public function assignDriver(Request $request, $orderId)
    {
        try {
            $request->validate([
                'driver_id' => 'required|exists:users,user_id'
            ]);

            $order = Order::findOrFail($orderId);

            // Check if order is not already assigned
            if ($order->delivery_agent_id) {
                return back()->with('error', 'Order is already assigned to a driver.');
            }

            // Assign driver
            $order->delivery_agent_id = $request->driver_id;
            $order->status = config('constants.OUT_FOR_DELIVERY');
            $order->notes = ($order->notes ?? '') . "\nAssigned to driver at: " . now()->format('Y-m-d H:i:s');
            $order->save();

            $driver = User::where('user_id', $request->driver_id)->first();
            Log::info("Order {$orderId} assigned to driver {$driver->name}");

            return back()->with('success', "Order assigned to {$driver->name} successfully.");
        } catch (\Exception $e) {
            Log::error('Error assigning driver: ' . $e->getMessage());
            return back()->with('error', 'Failed to assign driver.');
        }
    }

    /**
     * Bulk assign orders to driver
     */
    public function bulkAssignDriver(Request $request)
    {
        try {
            $request->validate([
                'order_ids' => 'required|array',
                'order_ids.*' => 'exists:orders,id',
                'driver_id' => 'required|exists:users,user_id'
            ]);

            $driver = User::where('user_id', $request->driver_id)->first();
            $assignedCount = 0;

            foreach ($request->order_ids as $orderId) {
                $order = Order::find($orderId);

                if ($order && !$order->delivery_agent_id) {
                    $order->delivery_agent_id = $request->driver_id;
                    $order->status = config('constants.OUT_FOR_DELIVERY');
                    $order->notes = ($order->notes ?? '') . "\nBulk assigned to driver at: " . now()->format('Y-m-d H:i:s');
                    $order->save();
                    $assignedCount++;
                }
            }

            Log::info("Bulk assigned {$assignedCount} orders to driver {$driver->name}");

            return back()->with('success', "{$assignedCount} orders assigned to {$driver->name} successfully.");
        } catch (\Exception $e) {
            Log::error('Error in bulk assign: ' . $e->getMessage());
            return back()->with('error', 'Failed to assign orders.');
        }
    }

    /**
     * ============================================
     *  SEND TO MOVE MANAGER - ORDER SENT
     * ============================================
     */

    /**
     * Show orders that have been assigned to drivers
     */
    public function orderSent(Request $request)
    {
        try {
            // Get orders that are assigned to drivers
            $query = Order::whereNotNull('delivery_agent_id')
                ->with(['customer', 'products', 'deliveryAgent']);

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('phone_numbers', 'like', "%{$search}%");
                        })
                        ->orWhereHas('deliveryAgent', function ($driverQuery) use ($search) {
                            $driverQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->filled('driver')) {
                $query->where('delivery_agent_id', $request->driver);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            $orders = $query->orderBy('created_at', 'desc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'assigned_orders' => Order::whereNotNull('delivery_agent_id')->count(),
                'active_drivers' => Order::whereNotNull('delivery_agent_id')
                    ->distinct('delivery_agent_id')
                    ->count('delivery_agent_id'),
                'in_transit_orders' => Order::whereNotNull('delivery_agent_id')
                    ->where('status', config('constants.OUT_FOR_DELIVERY'))
                    ->count(),
                'total_value' => Order::whereNotNull('delivery_agent_id')
                    ->sum('total'),
            ];

            // Get all drivers for filter
            $drivers = User::whereHas('role', function ($q) {
                $q->where('role_code', 'driver');
            })->get();

            // Get driver summaries
            $driverSummaries = User::whereHas('role', function ($q) {
                $q->where('role_code', 'driver');
            })
                ->withCount([
                    'deliveryOrders as assigned_count',
                    'deliveryOrders as in_transit_count' => function ($query) {
                        $query->where('status', config('constants.OUT_FOR_DELIVERY'));
                    }
                ])
                ->with(['deliveryOrders' => function ($query) {
                    $query->select('delivery_agent_id', DB::raw('SUM(total) as total_value'))
                        ->groupBy('delivery_agent_id');
                }])
                ->limit(3)
                ->get();

            return view('warehouse.order-sent', compact('orders', 'stats', 'drivers', 'driverSummaries'));
        } catch (\Exception $e) {
            Log::error('Error loading order sent: ' . $e->getMessage());

            // Create empty paginator
            $orders = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $stats = [
                'assigned_orders' => 0,
                'active_drivers' => 0,
                'in_transit_orders' => 0,
                'total_value' => 0,
            ];
            $drivers = collect();
            $driverSummaries = collect();

            return view('warehouse.order-sent', compact('orders', 'stats', 'drivers', 'driverSummaries'));
        }
    }

    /**
     * Reassign order to different driver
     */
    public function reassignDriver(Request $request, $orderId)
    {
        try {
            $request->validate([
                'driver_id' => 'required|exists:users,user_id'
            ]);

            $order = Order::findOrFail($orderId);
            $oldDriver = User::where('user_id', $order->delivery_agent_id)->first();
            $newDriver = User::where('user_id', $request->driver_id)->first();

            // Reassign driver
            $order->delivery_agent_id = $request->driver_id;
            $order->notes = ($order->notes ?? '') . "\nReassigned from {$oldDriver->name} to {$newDriver->name} at: " . now()->format('Y-m-d H:i:s');
            $order->save();

            Log::info("Order {$orderId} reassigned from {$oldDriver->name} to {$newDriver->name}");

            return back()->with('success', "Order reassigned to {$newDriver->name} successfully.");
        } catch (\Exception $e) {
            Log::error('Error reassigning driver: ' . $e->getMessage());
            return back()->with('error', 'Failed to reassign driver.');
        }
    }

    /**
     * Approve a return request
     */
    public function approveReturn(Request $request, $returnId)
    {
        try {
            $request->validate([
                'approval_notes' => 'nullable|string|max:1000'
            ]);

            $return = Order::findOrFail($returnId);

            // Check if return is in correct status
            if (!in_array($return->status, [config('constants.RETURN_REQUESTED'), config('constants.RETURN_PENDING')])) {
                return back()->with('error', 'This return cannot be approved in its current status.');
            }

            // Update return status
            $return->status = config('constants.RETURN_APPROVED');
            $return->notes = ($return->notes ?? '') . "\nApproved by " . (auth()->check() ? auth()->user()->name : 'Unknown') . " at: " . now()->format('Y-m-d H:i:s');
            if ($request->approval_notes) {
                $return->notes .= "\nApproval Notes: " . $request->approval_notes;
            }
            $return->save();

            Log::info("Return {$returnId} approved by " . (auth()->check() ? auth()->user()->name : 'Unknown'));

            return back()->with('success', 'Return approved successfully.');
        } catch (\Exception $e) {
            Log::error('Error approving return: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve return.');
        }
    }

    /**
     * Reject a return request
     */
    public function rejectReturn(Request $request, $returnId)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string|max:1000'
            ]);

            $return = Order::findOrFail($returnId);

            // Check if return is in correct status
            if (!in_array($return->status, [config('constants.RETURN_REQUESTED'), config('constants.RETURN_PENDING')])) {
                return back()->with('error', 'This return cannot be rejected in its current status.');
            }

            // Update return status
            $return->status = config('constants.RETURN_REJECTED');
            $return->notes = ($return->notes ?? '') . "\nRejected by " . (auth()->check() ? auth()->user()->name : 'Unknown') . " at: " . now()->format('Y-m-d H:i:s');
            $return->notes .= "\nRejection Reason: " . $request->rejection_reason;
            $return->save();

            Log::info("Return {$returnId} rejected by " . (auth()->check() ? auth()->user()->name : 'Unknown'));

            return back()->with('success', 'Return rejected successfully.');
        } catch (\Exception $e) {
            Log::error('Error rejecting return: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject return.');
        }
    }
}
