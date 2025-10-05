<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\RepairingOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseReportsController extends Controller
{
    /**
     * Show the reports dashboard
     */
    public function index()
    {
        $stats = $this->getGeneralStats();
        $trends = $this->getTrends();
        $topProducts = $this->getTopProducts();
        $stockAlerts = $this->getStockAlerts();

        return view('warehouse.reports.index', compact('stats', 'trends', 'topProducts', 'stockAlerts'));
    }

    /**
     * Generate stock movement report
     */
    public function stockMovement(Request $request)
    {
        $query = DB::table('stock_movements')
            ->join('products', 'stock_movements.product_id', '=', 'products.id')
            ->select(
                'products.name',
                'products.sku',
                'stock_movements.movement_type',
                'stock_movements.quantity',
                'stock_movements.reason',
                'stock_movements.created_at'
            );

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('stock_movements.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('stock_movements.created_at', '<=', $request->date_to);
        }
        if ($request->filled('movement_type')) {
            $query->where('stock_movements.movement_type', $request->movement_type);
        }
        if ($request->filled('product_id')) {
            $query->where('stock_movements.product_id', $request->product_id);
        }

        $movements = $query->orderBy('stock_movements.created_at', 'desc')->paginate(20);

        return view('warehouse.reports.stock-movement', compact('movements'));
    }

    /**
     * Generate damaged items report
     */
    public function damagedItems(Request $request)
    {
        $query = Product::where(function ($q) {
            $q->where('status', 'damaged')
                ->orWhere('stock_quantity', '<', 0);
        });

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('updated_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('updated_at', '<=', $request->date_to);
        }
        if ($request->filled('damage_type')) {
            $query->where('notes', 'like', "%{$request->damage_type}%");
        }
        if ($request->filled('category')) {
            $query->where('category', 'like', "%{$request->category}%");
        }

        $items = $query->orderBy('updated_at', 'desc')->paginate(20);

        return view('warehouse.reports.damaged-items', compact('items'));
    }

    /**
     * Generate returns report
     */
    public function returns(Request $request)
    {
        $query = Order::whereNotNull('return_reason');

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('return_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('return_date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('reason')) {
            $query->where('return_reason', 'like', "%{$request->reason}%");
        }

        $returns = $query->orderBy('return_date', 'desc')->paginate(20);

        return view('warehouse.reports.returns', compact('returns'));
    }

    /**
     * Generate repairing orders report
     */
    public function repairingOrders(Request $request)
    {
        $query = RepairingOrder::query();

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('warehouse.reports.repairing-orders', compact('orders'));
    }

    /**
     * Generate performance report
     */
    public function performance(Request $request)
    {
        $stats = [
            'processing_time' => $this->getAverageProcessingTime($request),
            'order_accuracy' => $this->getOrderAccuracy($request),
            'return_rate' => $this->getReturnRate($request),
            'stock_turnover' => $this->getStockTurnover($request),
        ];

        return view('warehouse.reports.performance', compact('stats'));
    }

    /**
     * Export report to Excel/CSV
     */
    public function export(Request $request)
    {
        $type = $request->type;
        $format = $request->format;

        switch ($type) {
            case 'stock-movement':
                return $this->exportStockMovement($format);
            case 'damaged-items':
                return $this->exportDamagedItems($format);
            case 'returns':
                return $this->exportReturns($format);
            case 'repairing-orders':
                return $this->exportRepairingOrders($format);
            case 'performance':
                return $this->exportPerformance($format);
            default:
                return back()->with('error', 'Invalid report type');
        }
    }

    /**
     * Get general warehouse statistics
     */
    protected function getGeneralStats()
    {
        return [
            'total_products' => Product::count(),
            'total_value' => Product::sum(DB::raw('stock_quantity * normal_price')),
            'damaged_items' => Product::where('status', 'damaged')->count(),
            'pending_returns' => Order::whereNotNull('return_reason')
                ->where('status', 'pending')
                ->count(),
        ];
    }

    /**
     * Get warehouse trends
     */
    protected function getTrends()
    {
        $dates = [];
        $stockLevels = [];
        $orderCounts = [];
        $returnCounts = [];

        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;

            // Stock levels
            $stockLevels[] = Product::whereDate('updated_at', '<=', $date)
                ->sum('stock_quantity');

            // Order counts
            $orderCounts[] = Order::whereDate('created_at', $date)->count();

            // Return counts
            $returnCounts[] = Order::whereDate('return_date', $date)
                ->whereNotNull('return_reason')
                ->count();
        }

        return [
            'dates' => $dates,
            'stock_levels' => $stockLevels,
            'order_counts' => $orderCounts,
            'return_counts' => $returnCounts,
        ];
    }

    /**
     * Get top performing products
     */
    protected function getTopProducts()
    {
        return DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->select(
                'products.name',
                'products.sku',
                DB::raw('SUM(order_product.quantity) as total_quantity'),
                DB::raw('SUM(order_product.quantity * products.normal_price) as total_value')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
    }

    /**
     * Get stock alerts
     */
    protected function getStockAlerts()
    {
        return Product::where('stock_quantity', '<=', DB::raw('reorder_point'))
            ->orWhere('stock_quantity', '<', 0)
            ->get();
    }

    /**
     * Get average order processing time
     */
    protected function getAverageProcessingTime(Request $request)
    {
        $query = Order::whereNotNull('completed_at');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query->avg(DB::raw('TIMESTAMPDIFF(HOUR, created_at, completed_at)'));
    }

    /**
     * Get order accuracy rate
     */
    protected function getOrderAccuracy(Request $request)
    {
        $query = Order::whereNotNull('completed_at');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $total = $query->count();
        $accurate = $query->whereDoesntHave('returns')->count();

        return $total > 0 ? ($accurate / $total) * 100 : 0;
    }

    /**
     * Get return rate
     */
    protected function getReturnRate(Request $request)
    {
        $query = Order::whereNotNull('completed_at');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $total = $query->count();
        $returns = $query->whereNotNull('return_reason')->count();

        return $total > 0 ? ($returns / $total) * 100 : 0;
    }

    /**
     * Get stock turnover rate
     */
    protected function getStockTurnover(Request $request)
    {
        $period = [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ];

        if ($request->filled('date_from')) {
            $period[0] = Carbon::parse($request->date_from);
        }
        if ($request->filled('date_to')) {
            $period[1] = Carbon::parse($request->date_to);
        }

        $averageInventory = Product::whereBetween('updated_at', $period)
            ->avg(DB::raw('stock_quantity * normal_price'));

        $costOfGoodsSold = DB::table('order_product')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', $period)
            ->sum(DB::raw('order_product.quantity * products.normal_price'));

        return $averageInventory > 0 ? ($costOfGoodsSold / $averageInventory) : 0;
    }
}
