<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\RepairingOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseAnalyticsController extends Controller
{
    /**
     * Show the analytics dashboard
     */
    public function index()
    {
        $kpis = $this->getKPIs();
        $trends = $this->getTrends();
        $insights = $this->getInsights();
        $predictions = $this->getPredictions();

        return view('warehouse.analytics.index', compact('kpis', 'trends', 'insights', 'predictions'));
    }

    /**
     * Get key performance indicators
     */
    protected function getKPIs()
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        // Order fulfillment rate
        $totalOrders = Order::whereMonth('created_at', $now->month)->count();
        $completedOrders = Order::whereMonth('created_at', $now->month)
            ->whereNotNull('completed_at')
            ->count();
        $fulfillmentRate = $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0;

        // Previous month for comparison
        $lastMonthTotal = Order::whereMonth('created_at', $lastMonth->month)->count();
        $lastMonthCompleted = Order::whereMonth('created_at', $lastMonth->month)
            ->whereNotNull('completed_at')
            ->count();
        $lastMonthRate = $lastMonthTotal > 0 ? ($lastMonthCompleted / $lastMonthTotal) * 100 : 0;
        $fulfillmentTrend = $fulfillmentRate - $lastMonthRate;

        // Inventory turnover
        $averageInventory = Product::avg(DB::raw('stock_quantity * normal_price'));
        $costOfGoodsSold = DB::table('order_product')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->whereMonth('orders.created_at', $now->month)
            ->sum(DB::raw('order_product.quantity * products.normal_price'));
        $turnoverRate = $averageInventory > 0 ? ($costOfGoodsSold / $averageInventory) : 0;

        // Last month turnover for comparison
        $lastMonthCOGS = DB::table('order_product')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->whereMonth('orders.created_at', $lastMonth->month)
            ->sum(DB::raw('order_product.quantity * products.normal_price'));
        $lastMonthTurnover = $averageInventory > 0 ? ($lastMonthCOGS / $averageInventory) : 0;
        $turnoverTrend = $turnoverRate - $lastMonthTurnover;

        // Return rate
        $returnedOrders = Order::whereMonth('created_at', $now->month)
            ->whereNotNull('return_reason')
            ->count();
        $returnRate = $totalOrders > 0 ? ($returnedOrders / $totalOrders) * 100 : 0;

        // Last month return rate
        $lastMonthReturns = Order::whereMonth('created_at', $lastMonth->month)
            ->whereNotNull('return_reason')
            ->count();
        $lastMonthReturnRate = $lastMonthTotal > 0 ? ($lastMonthReturns / $lastMonthTotal) * 100 : 0;
        $returnTrend = $returnRate - $lastMonthReturnRate;

        // Damage rate
        $damagedItems = Product::whereMonth('updated_at', $now->month)
            ->where('status', 'damaged')
            ->count();
        $totalItems = Product::count();
        $damageRate = $totalItems > 0 ? ($damagedItems / $totalItems) * 100 : 0;

        // Last month damage rate
        $lastMonthDamaged = Product::whereMonth('updated_at', $lastMonth->month)
            ->where('status', 'damaged')
            ->count();
        $lastMonthDamageRate = $totalItems > 0 ? ($lastMonthDamaged / $totalItems) * 100 : 0;
        $damageTrend = $damageRate - $lastMonthDamageRate;

        return [
            'fulfillment_rate' => [
                'value' => round($fulfillmentRate, 2),
                'trend' => round($fulfillmentTrend, 2),
                'trend_type' => $fulfillmentTrend >= 0 ? 'up' : 'down',
                'trend_class' => $fulfillmentTrend >= 0 ? 'success' : 'danger'
            ],
            'turnover_rate' => [
                'value' => round($turnoverRate, 2),
                'trend' => round($turnoverTrend, 2),
                'trend_type' => $turnoverTrend >= 0 ? 'up' : 'down',
                'trend_class' => $turnoverTrend >= 0 ? 'success' : 'danger'
            ],
            'return_rate' => [
                'value' => round($returnRate, 2),
                'trend' => round($returnTrend, 2),
                'trend_type' => $returnTrend <= 0 ? 'up' : 'down',
                'trend_class' => $returnTrend <= 0 ? 'success' : 'danger'
            ],
            'damage_rate' => [
                'value' => round($damageRate, 2),
                'trend' => round($damageTrend, 2),
                'trend_type' => $damageTrend <= 0 ? 'up' : 'down',
                'trend_class' => $damageTrend <= 0 ? 'success' : 'danger'
            ]
        ];
    }

    /**
     * Get trend data
     */
    protected function getTrends()
    {
        $dates = [];
        $orderCounts = [];
        $returnCounts = [];
        $stockLevels = [];
        $damageCounts = [];

        // Get data for the last 30 days
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;

            // Orders
            $orderCounts[] = Order::whereDate('created_at', $date)->count();

            // Returns
            $returnCounts[] = Order::whereDate('return_date', $date)
                ->whereNotNull('return_reason')
                ->count();

            // Stock levels
            $stockLevels[] = Product::whereDate('updated_at', '<=', $date)
                ->sum('stock_quantity');

            // Damages
            $damageCounts[] = Product::whereDate('updated_at', $date)
                ->where('status', 'damaged')
                ->count();
        }

        return [
            'dates' => $dates,
            'orders' => $orderCounts,
            'returns' => $returnCounts,
            'stock' => $stockLevels,
            'damages' => $damageCounts
        ];
    }

    /**
     * Get business insights
     */
    protected function getInsights()
    {
        $insights = [];

        // Stock level insights
        $lowStock = Product::where('stock_quantity', '<=', DB::raw('reorder_point'))->count();
        if ($lowStock > 0) {
            $insights[] = [
                'type' => 'warning',
                'icon' => 'exclamation-triangle',
                'title' => 'Low Stock Alert',
                'message' => "{$lowStock} products are below reorder point"
            ];
        }

        // Return rate insights
        $returnRate = $this->getKPIs()['return_rate'];
        if ($returnRate['trend'] > 2) {
            $insights[] = [
                'type' => 'danger',
                'icon' => 'arrow-up',
                'title' => 'Increasing Returns',
                'message' => "Return rate increased by {$returnRate['trend']}% this month"
            ];
        }

        // Inventory turnover insights
        $turnover = $this->getKPIs()['turnover_rate'];
        if ($turnover['value'] < 1) {
            $insights[] = [
                'type' => 'info',
                'icon' => 'info-circle',
                'title' => 'Low Inventory Turnover',
                'message' => "Current turnover rate is {$turnover['value']}, consider optimizing stock levels"
            ];
        }

        // Damage rate insights
        $damageRate = $this->getKPIs()['damage_rate'];
        if ($damageRate['trend'] > 1) {
            $insights[] = [
                'type' => 'danger',
                'icon' => 'exclamation-circle',
                'title' => 'Increasing Damages',
                'message' => "Damage rate increased by {$damageRate['trend']}% this month"
            ];
        }

        return $insights;
    }

    /**
     * Get predictions and forecasts
     */
    protected function getPredictions()
    {
        // Calculate average daily order count for the last 30 days
        $avgDailyOrders = Order::where('created_at', '>=', Carbon::now()->subDays(30))
            ->count() / 30;

        // Calculate average daily return count
        $avgDailyReturns = Order::where('return_date', '>=', Carbon::now()->subDays(30))
            ->whereNotNull('return_reason')
            ->count() / 30;

        // Calculate stock depletion rate
        $stockDepletionRate = DB::table('order_product')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', Carbon::now()->subDays(30))
            ->sum('order_product.quantity') / 30;

        // Get current stock levels
        $currentStock = Product::sum('stock_quantity');

        // Calculate days until reorder needed
        $daysUntilReorder = $stockDepletionRate > 0 ? floor($currentStock / $stockDepletionRate) : null;

        return [
            'expected_orders' => round($avgDailyOrders * 7), // Next 7 days
            'expected_returns' => round($avgDailyReturns * 7), // Next 7 days
            'days_until_reorder' => $daysUntilReorder,
            'stock_depletion_rate' => round($stockDepletionRate, 2)
        ];
    }
}
