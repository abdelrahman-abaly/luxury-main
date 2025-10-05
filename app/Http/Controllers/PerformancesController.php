<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PerformanceHistory;
use Illuminate\Http\Request;

class PerformancesController extends Controller
{
    public function show() {
        $employee_id = auth()->user()->user_id;

        $delivered_orders = Order::where('employee_id', $employee_id)->where('status', config("constants.DELIVERED"))->count();
        $performance = "User";
        $commission_rate = "0%";
        $next_level = "Beginner";
        $next_commission_rate = "1%";
        $total = 100;
        $progress = ($delivered_orders / $total) * 100;

        $user_progress = 0;
        $beginner_progress = 0;
        $rising_progress = 0;
        $expert_progress = 0;
        $pioneer_progress = 0;
        $professional_progress = 0;

        if($delivered_orders>=0 and $delivered_orders<99){
            $performance = "User";
            $commission_rate = "0%";
            $next_level = "Beginner";
            $next_commission_rate = "1%";
            $total = 100;
            $progress = ($delivered_orders / $total) * 100;
            $user_progress = $progress;
            $beginner_progress = 0;
            $rising_progress = 0;
            $expert_progress = 0;
            $pioneer_progress = 0;
            $professional_progress = 0;
        } else if($delivered_orders>=100 and $delivered_orders<199) {
            $performance = "Beginner";
            $commission_rate = "1%";
            $next_level = "Rising";
            $next_commission_rate = "2%";
            $total = 200;
            $progress = ($delivered_orders / $total) * 100;
            $user_progress = 100;
            $beginner_progress = $progress;
            $rising_progress = 0;
            $expert_progress = 0;
            $pioneer_progress = 0;
            $professional_progress = 0;
        } else if($delivered_orders>=200 and $delivered_orders<299) {
            $performance = "Rising";
            $commission_rate = "2%";
            $next_level = "Expert";
            $next_commission_rate = "3%";
            $total = 300;
            $progress = ($delivered_orders / $total) * 100;
            $user_progress = 100;
            $beginner_progress = 100;
            $rising_progress = $progress;
            $expert_progress = 0;
            $pioneer_progress = 0;
            $professional_progress = 0;
        } else if($delivered_orders>=300 and $delivered_orders<399) {
            $performance = "Expert";
            $commission_rate = "3%";
            $next_level = "Pioneer";
            $next_commission_rate = "4%";
            $total = 400;
            $progress = ($delivered_orders / $total) * 100;
            $user_progress = 100;
            $beginner_progress = 100;
            $rising_progress = 100;
            $expert_progress = $progress;
            $pioneer_progress = 0;
            $professional_progress = 0;
        } else if($delivered_orders>=400 and $delivered_orders<499) {
            $performance = "Pioneer";
            $commission_rate = "4%";
            $next_level = "Professional";
            $next_commission_rate = "5%";
            $total = 500;
            $progress = ($delivered_orders / $total) * 100;
            $user_progress = 100;
            $beginner_progress = 100;
            $rising_progress = 100;
            $expert_progress = 100;
            $pioneer_progress = $progress;
            $professional_progress = 0;
        } else {
            $performance = "Professional";
            $commission_rate = "5%";
            $next_level = "Super Hero";
            $next_commission_rate = "6%";
            $total = 600;
            $progress = ($delivered_orders / $total) * 100;
            $user_progress = 100;
            $beginner_progress = 100;
            $rising_progress = 100;
            $expert_progress = 100;
            $pioneer_progress = 100;
            $professional_progress = $progress;
        }

        $performance_history = PerformanceHistory::where('user_id', $employee_id)->get();

        $user = auth()->user();

        // Get monthly orders for the current year
        $monthlyOrders = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->where('employee_id', $employee_id)
            ->where('status', config("constants.DELIVERED"))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Fill all months
        $monthlyOrdersData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyOrdersData[] = $monthlyOrders[$i] ?? 0;
        }

        $performanceData = [
            'monthly_orders' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'data' => $monthlyOrdersData
            ],
            // Add other real data calculations...
        ];

        return view('my-performance', compact('performance',
            'next_level', 'next_commission_rate', 'total',
            'progress','commission_rate','delivered_orders','user_progress','beginner_progress',
            'rising_progress','expert_progress','pioneer_progress',
            'professional_progress', 'performance_history','performanceData'));
    }
}
