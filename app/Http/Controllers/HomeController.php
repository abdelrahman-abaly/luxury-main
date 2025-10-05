<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Order;
use App\Models\User;
use App\Models\ScheduledTask;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $user = auth()->user();

        // Redirect based on user role
        if ($user->hasRole('warehouse')) {
            return redirect()->route('warehouse.home');
        }

        if ($user->hasRole('driver')) {
            return redirect()->route('driver.home');
        }

        $employee_id = $user->user_id;

        $delivered_orders = Order::where('employee_id', $employee_id)->where('status', config("constants.DELIVERED"))->count();
        $pending_orders = Order::where('employee_id', $employee_id)->where('status', config("constants.PENDING"))->count();
        $processing_orders = Order::where('employee_id', $employee_id)->where('status', config("constants.PROCESSING"))->count();
        $out_for_delivery_orders = Order::where('employee_id', $employee_id)->where('status', config("constants.OUT_FOR_DELIVERY"))->count();
        $cancelled_orders = Order::where('employee_id', $employee_id)->where('status', config("constants.CANCELLED"))->count();
        $returned_orders = Order::where('employee_id', $employee_id)->where('status', config("constants.RETURNED"))->count();

        $recent_orders = Order::where('employee_id', $employee_id)->limit(4)->get();

        $total_leads = Lead::where('assigned_to', $employee_id)->count();
        $cancelled_leads = Lead::where('assigned_to', $employee_id)->where('notes', '%LIKE%', "cancelled")->count();
        $recent_leads = Lead::where('assigned_to', $employee_id)->limit(4)->get();

        // Calculate real numbers for dashboard
        $scheduled_tasks = ScheduledTask::where('user_id', $employee_id)
            ->where('task_done', '0')
            ->count();

        // Calculate conversion rate (leads that became customers)
        $converted_leads = Lead::where('assigned_to', $employee_id)
            ->where('is_customer', '1')
            ->count();
        $conversion_rate = $total_leads > 0 ? round(($converted_leads / $total_leads) * 100, 1) : 0;

        // Calculate potential commission from pending leads
        $potential_commission = Lead::where('assigned_to', $employee_id)
            ->where('is_customer', '0')
            ->where('notes', 'NOT LIKE', '%cancelled%')
            ->sum('potential') ?? 0;

        $formattedOrders = $recent_orders->map(function ($order) {
            $employee = User::where('user_id', $order->employee_id)->first();
            $customer = Lead::where('lead_id', $order->customer_id)->where('is_customer', '1')->first();
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer' => $customer->name,
                'status' => $order->status,
                'address' => $order->address,
                'latitude' => $order->latitude,
                'longitude' => $order->longitude,
                'notes' => $order->notes,
                'total' => $order->total,
                'employee_commission' => $order->employee_commission,
                'governorate' => $order->governorate,
                'coupon_code' => $order->coupon_code,
                'delivery_agent_id' => $order->delivery_agent_id,
                'employee' => $employee->name,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $performance = "User";
        $commission_rate = "0%";
        $next_level = "Beginner";
        $next_commission_rate = "1%";
        $total = 100;
        $progress = ($delivered_orders / $total) * 100;
        if ($delivered_orders >= 0 and $delivered_orders < 99) {
            $performance = "User";
            $commission_rate = "0%";
            $next_level = "Beginner";
            $next_commission_rate = "1%";
            $total = 100;
            $progress = ($delivered_orders / $total) * 100;
        } else if ($delivered_orders >= 100 and $delivered_orders < 199) {
            $performance = "Beginner";
            $commission_rate = "1%";
            $next_level = "Rising";
            $next_commission_rate = "2%";
            $total = 200;
            $progress = ($delivered_orders / $total) * 100;
        } else if ($delivered_orders >= 200 and $delivered_orders < 299) {
            $performance = "Rising";
            $commission_rate = "2%";
            $next_level = "Expert";
            $next_commission_rate = "3%";
            $total = 300;
            $progress = ($delivered_orders / $total) * 100;
        } else if ($delivered_orders >= 300 and $delivered_orders < 399) {
            $performance = "Expert";
            $commission_rate = "3%";
            $next_level = "Pioneer";
            $next_commission_rate = "4%";
            $total = 400;
            $progress = ($delivered_orders / $total) * 100;
        } else if ($delivered_orders >= 400 and $delivered_orders < 499) {
            $performance = "Pioneer";
            $commission_rate = "4%";
            $next_level = "Professional";
            $next_commission_rate = "5%";
            $total = 500;
            $progress = ($delivered_orders / $total) * 100;
        } else {
            $performance = "Professional";
            $commission_rate = "5%";
            $next_level = "Super Hero";
            $next_commission_rate = "6%";
            $total = 600;
            $progress = ($delivered_orders / $total) * 100;
        }

        return view(
            'home',
            compact(
                'delivered_orders',
                'pending_orders',
                'processing_orders',
                'out_for_delivery_orders',
                'cancelled_orders',
                'returned_orders',
                'formattedOrders',
                'total_leads',
                'recent_leads',
                'cancelled_leads',
                'performance',
                'commission_rate',
                'next_level',
                'next_commission_rate',
                'total',
                'progress',
                'scheduled_tasks',
                'conversion_rate',
                'potential_commission'
            )
        );
    }
}
