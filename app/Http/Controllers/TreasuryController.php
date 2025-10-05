<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TreasuryController extends Controller
{
    /**
     * Treasury Dashboard Home
     */
    public function home()
    {
        try {
            // Get treasury statistics
            $stats = $this->getTreasuryStats();

            // Get recent delivered orders
            $recentOrders = Order::where('status', config('constants.DELIVERED'))
                ->with(['customer', 'deliveryAgent', 'employee'])
                ->orderBy('updated_at', 'desc')
                ->take(10)
                ->get();

            // Prepare chart data for last 30 days
            $chartLabels = [];
            $chartData = [];

            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $chartLabels[] = $date->format('M d');

                $dayTotal = Order::where('status', config('constants.DELIVERED'))
                    ->whereDate('updated_at', $date->format('Y-m-d'))
                    ->sum('total');

                $chartData[] = $dayTotal;
            }

            $chartData = [
                'labels' => $chartLabels,
                'data' => $chartData
            ];

            // Get monthly breakdown
            $monthlyBreakdown = $this->getMonthlyBreakdown();

            return view('treasury.home', compact('stats', 'recentOrders', 'chartData', 'monthlyBreakdown'));
        } catch (\Exception $e) {
            Log::error('Treasury dashboard error: ' . $e->getMessage());
            return view('treasury.home', [
                'stats' => [],
                'recentOrders' => collect(),
                'chartData' => ['labels' => [], 'data' => []],
                'monthlyBreakdown' => []
            ]);
        }
    }

    /**
     * Get treasury statistics
     */
    private function getTreasuryStats()
    {
        $totalRevenue = Order::where('status', config('constants.DELIVERED'))->sum('total');
        $todayRevenue = Order::where('status', config('constants.DELIVERED'))
            ->whereDate('updated_at', today())
            ->sum('total');
        $monthRevenue = Order::where('status', config('constants.DELIVERED'))
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('total');
        $deliveredOrders = Order::where('status', config('constants.DELIVERED'))->count();

        // Calculate average order value
        $averageOrderValue = $deliveredOrders > 0 ? $totalRevenue / $deliveredOrders : 0;

        // Get pending amount (orders not yet delivered)
        $pendingAmount = Order::whereIn('status', [
            config('constants.PENDING'),
            config('constants.PROCESSING'),
            config('constants.OUT_FOR_DELIVERY')
        ])->sum('total');

        return [
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'month_revenue' => $monthRevenue,
            'delivered_orders' => $deliveredOrders,
            'average_order_value' => $averageOrderValue,
            'pending_amount' => $pendingAmount,
        ];
    }

    /**
     * Get monthly breakdown
     */
    private function getMonthlyBreakdown()
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthRevenue = Order::where('status', config('constants.DELIVERED'))
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)
                ->sum('total');

            $monthOrders = Order::where('status', config('constants.DELIVERED'))
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)
                ->count();

            $months[] = [
                'name' => $date->format('F Y'),
                'revenue' => $monthRevenue,
                'orders' => $monthOrders,
            ];
        }

        return $months;
    }

    /**
     * Show all treasury transactions (delivered orders)
     */
    public function transactions(Request $request)
    {
        try {
            $query = Order::where('status', config('constants.DELIVERED'))
                ->with(['customer', 'deliveryAgent', 'employee']);

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('updated_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('updated_at', '<=', $request->date_to);
            }

            if ($request->filled('driver')) {
                $query->where('delivery_agent_id', $request->driver);
            }

            if ($request->filled('employee')) {
                $query->where('employee_id', $request->employee);
            }

            // Get total for filtered results
            $filteredTotal = $query->sum('total');
            $filteredCount = $query->count();

            $orders = $query->orderBy('updated_at', 'desc')
                ->paginate(20);

            // Get statistics
            $stats = [
                'total_amount' => Order::where('status', config('constants.DELIVERED'))->sum('total'),
                'filtered_amount' => $filteredTotal,
                'filtered_count' => $filteredCount,
                'today_amount' => Order::where('status', config('constants.DELIVERED'))
                    ->whereDate('updated_at', today())
                    ->sum('total'),
            ];

            // Get drivers and employees for filter
            $drivers = User::whereHas('role', function ($q) {
                $q->where('role_code', 'driver');
            })->get();

            $employees = User::whereHas('orders', function ($q) {
                $q->where('status', config('constants.DELIVERED'));
            })->get();

            return view('treasury.transactions', compact('orders', 'stats', 'drivers', 'employees'));
        } catch (\Exception $e) {
            Log::error('Error loading treasury transactions: ' . $e->getMessage());

            $orders = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $stats = [
                'total_amount' => 0,
                'filtered_amount' => 0,
                'filtered_count' => 0,
                'today_amount' => 0,
            ];
            $drivers = collect();
            $employees = collect();

            return view('treasury.transactions', compact('orders', 'stats', 'drivers', 'employees'));
        }
    }

    /**
     * Export treasury report
     */
    public function exportReport(Request $request)
    {
        try {
            $query = Order::where('status', config('constants.DELIVERED'))
                ->with(['customer', 'deliveryAgent', 'employee']);

            // Apply same filters as transactions page
            if ($request->filled('date_from')) {
                $query->whereDate('updated_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('updated_at', '<=', $request->date_to);
            }

            $orders = $query->orderBy('updated_at', 'desc')->get();

            // Generate CSV
            $filename = 'treasury_report_' . now()->format('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function () use ($orders) {
                $file = fopen('php://output', 'w');

                // Add UTF-8 BOM for Excel
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Header row
                fputcsv($file, [
                    'Order #',
                    'Customer',
                    'Driver',
                    'Employee',
                    'Amount',
                    'Date Delivered',
                    'Governorate'
                ]);

                // Data rows
                foreach ($orders as $order) {
                    fputcsv($file, [
                        $order->order_number,
                        $order->customer->name ?? 'Unknown',
                        $order->deliveryAgent->name ?? 'N/A',
                        $order->employee->name ?? 'N/A',
                        $order->total,
                        $order->updated_at->format('Y-m-d H:i:s'),
                        $order->governorate ?? 'N/A'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error exporting treasury report: ' . $e->getMessage());
            return back()->with('error', 'Failed to export report.');
        }
    }
}
