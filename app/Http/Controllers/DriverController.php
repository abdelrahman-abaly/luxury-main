<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DriverController extends Controller
{
    /**
     * Driver Dashboard Home
     */
    public function home()
    {
        try {
            $driverId = Auth::id();

            // Get driver statistics
            $stats = $this->getDriverStats($driverId);

            // Get today's orders
            $todayOrders = Order::where('delivery_agent_id', $driverId)
                ->whereDate('created_at', today())
                ->with(['customer', 'products'])
                ->latest()
                ->take(5)
                ->get();

            // Prepare chart data for last 7 days
            $chartLabels = [];
            $chartData = [];
            $chartValues = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $chartLabels[] = $date->format('M d');

                $dayOrders = Order::where('delivery_agent_id', $driverId)
                    ->whereDate('created_at', $date->format('Y-m-d'))
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

            return view('driver.home', compact('stats', 'todayOrders', 'chartData'));
        } catch (\Exception $e) {
            Log::error('Driver dashboard error: ' . $e->getMessage());
            return view('driver.home', [
                'stats' => [],
                'todayOrders' => collect(),
                'chartData' => ['labels' => [], 'data' => [], 'values' => []]
            ]);
        }
    }

    /**
     * Get driver statistics
     */
    private function getDriverStats($driverId)
    {
        return [
            'assigned_orders' => Order::where('delivery_agent_id', $driverId)->count(),
            'pending_orders' => Order::where('delivery_agent_id', $driverId)
                ->where('status', config('constants.OUT_FOR_DELIVERY'))->count(),
            'delivered_orders' => Order::where('delivery_agent_id', $driverId)
                ->where('status', config('constants.DELIVERED'))->count(),
            'today_orders' => Order::where('delivery_agent_id', $driverId)
                ->whereDate('created_at', today())->count(),
            'total_earnings' => Order::where('delivery_agent_id', $driverId)
                ->where('status', config('constants.DELIVERED'))
                ->sum('total'),
        ];
    }

    /**
     * Show assigned orders to driver
     */
    public function myOrders(Request $request)
    {
        try {
            $driverId = Auth::id();

            $query = Order::where('delivery_agent_id', $driverId)
                ->where('status', '!=', config('constants.CANCELLED')) // Exclude cancelled orders
                ->where(function ($q) {
                    // Exclude rejected orders (PROCESSING status with rejection note)
                    $q->where('status', '!=', config('constants.PROCESSING'))
                        ->orWhere(function ($subQ) {
                            $subQ->where('status', config('constants.PROCESSING'))
                                ->where('notes', 'not like', '%Driver rejected order:%');
                        });
                })
                ->with(['customer', 'products']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

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

            if ($request->filled('date')) {
                $query->whereDate('created_at', $request->date);
            }

            $orders = $query->orderBy('created_at', 'desc')
                ->paginate(20);

            // Get statistics for filters
            $stats = [
                'total_orders' => Order::where('delivery_agent_id', $driverId)->count(),
                'pending_orders' => Order::where('delivery_agent_id', $driverId)
                    ->where('status', config('constants.OUT_FOR_DELIVERY'))->count(),
                'in_transit_orders' => Order::where('delivery_agent_id', $driverId)
                    ->where('status', config('constants.OUT_FOR_DELIVERY'))->count(),
                'delivered_orders' => Order::where('delivery_agent_id', $driverId)
                    ->where('status', config('constants.DELIVERED'))->count(),
                'returned_orders' => Order::where('delivery_agent_id', $driverId)
                    ->where('status', config('constants.RETURNED'))->count(),
                'exit_requests' => Order::where('delivery_agent_id', $driverId)
                    ->whereIn('status', [
                        config('constants.EXIT_REQUESTED'),
                        config('constants.EXIT_APPROVED'),
                        config('constants.EXIT_REJECTED'),
                        config('constants.EXIT_SHIPPED')
                    ])->count(),
            ];

            return view('driver.my-orders', compact('orders', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading driver orders: ' . $e->getMessage());
            return view('driver.my-orders', [
                'orders' => collect(),
                'stats' => []
            ]);
        }
    }

    /**
     * Accept Order
     */
    public function acceptOrder(Request $request, $orderId)
    {
        try {
            $driverId = Auth::id();
            $order = Order::findOrFail($orderId);

            // Check if order is assigned to this driver
            if ($order->delivery_agent_id != $driverId) {
                return back()->with('error', 'This order is not assigned to you.');
            }

            // Check if order can be accepted
            if ($order->status != config('constants.OUT_FOR_DELIVERY')) {
                return back()->with('error', 'Order cannot be accepted at this status.');
            }

            // Accept the order (keep it as OUT_FOR_DELIVERY but add note)
            $order->notes = ($order->notes ?? '') . "\nDriver accepted order at: " . now()->format('Y-m-d H:i:s');
            $order->save();

            Log::info("Driver {$driverId} accepted order {$orderId}");

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
            $driverId = Auth::id();
            $order = Order::findOrFail($orderId);

            // Check if order is assigned to this driver
            if ($order->delivery_agent_id != $driverId) {
                return back()->with('error', 'This order is not assigned to you.');
            }

            // Check if order can be rejected
            if ($order->status != config('constants.OUT_FOR_DELIVERY')) {
                return back()->with('error', 'Order cannot be rejected at this status.');
            }

            $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            // Reject the order - remove driver assignment and return to processing
            $order->delivery_agent_id = null;
            $order->status = config('constants.PROCESSING'); // Return to processing for reassignment
            $order->notes = ($order->notes ?? '') . "\nDriver rejected order: " . $request->reason . " at " . now()->format('Y-m-d H:i:s');
            $order->save();

            Log::info("Driver {$driverId} rejected order {$orderId}");

            return back()->with('success', 'Order rejected successfully.');
        } catch (\Exception $e) {
            Log::error('Error rejecting order: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject order.');
        }
    }

    /**
     * Mark Order as Delivered
     */
    public function markAsDelivered(Request $request, $orderId)
    {
        try {
            $driverId = Auth::id();
            $order = Order::findOrFail($orderId);

            // Check if order is assigned to this driver
            if ($order->delivery_agent_id != $driverId) {
                return back()->with('error', 'This order is not assigned to you.');
            }

            // Check if order can be marked as delivered
            if (!in_array($order->status, [config('constants.OUT_FOR_DELIVERY'), config('constants.EXIT_SHIPPED')])) {
                return back()->with('error', 'Order is not in delivery status.');
            }

            $request->validate([
                'notes' => 'nullable|string|max:500'
            ]);

            // Mark as delivered
            $order->status = config('constants.DELIVERED');
            $order->notes = ($order->notes ?? '') . "\nDelivered by driver at: " . now()->format('Y-m-d H:i:s');
            if ($request->notes) {
                $order->notes .= "\nDelivery notes: " . $request->notes;
            }
            $order->save();

            Log::info("Driver {$driverId} marked order {$orderId} as delivered");

            return back()->with('success', 'Order marked as delivered successfully.');
        } catch (\Exception $e) {
            Log::error('Error marking order as delivered: ' . $e->getMessage());
            return back()->with('error', 'Failed to mark order as delivered.');
        }
    }

    /**
     * Mark Order as Returned
     */
    public function markAsReturned(Request $request, $orderId)
    {
        try {
            $driverId = Auth::id();
            $order = Order::findOrFail($orderId);

            // Check if order is assigned to this driver
            if ($order->delivery_agent_id != $driverId) {
                return back()->with('error', 'This order is not assigned to you.');
            }

            // Check if order can be marked as returned
            if (!in_array($order->status, [config('constants.OUT_FOR_DELIVERY'), config('constants.EXIT_SHIPPED')])) {
                return back()->with('error', 'Order is not in delivery status.');
            }

            $request->validate([
                'reason' => 'required|string|max:500',
                'notes' => 'nullable|string|max:500'
            ]);

            // Mark as returned
            $order->status = config('constants.RETURNED');
            // Keep delivery_agent_id to show in driver's orders list
            $order->notes = ($order->notes ?? '') . "\nReturned by driver at: " . now()->format('Y-m-d H:i:s');
            $order->notes .= "\nReturn reason: " . $request->reason;
            if ($request->notes) {
                $order->notes .= "\nReturn notes: " . $request->notes;
            }
            $order->save();

            Log::info("Driver {$driverId} marked order {$orderId} as returned");

            return back()->with('success', 'Order marked as returned successfully.');
        } catch (\Exception $e) {
            Log::error('Error marking order as returned: ' . $e->getMessage());
            return back()->with('error', 'Failed to mark order as returned.');
        }
    }
}
