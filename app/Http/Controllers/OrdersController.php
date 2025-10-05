<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Lead;
use App\Models\Order;
use App\Models\PerformanceHistory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrdersController extends Controller
{
    /*
     *          Orders List Page
     * */
    public function ordersList() {
        $orders = Order::where("employee_id", auth()->user()->user_id)->get();
        $orders_collection = OrderResource::collection($orders);
        $formated_orders = $orders_collection->toArray(request());

        return view("orders.orders-list", compact( "formated_orders"));
    }

    /*
     *
     *          Add New Order Page
     * */
    public function create(Request $request)
{
    // كل العملاء
    $customers = Lead::where("is_customer", "1")->get();

    // كل المنتجات (لعرض القائمة الكاملة لو حابب تغيّر المنتج)
    $products = Product::all();

    // المنتج المُختار من صفحة المنتجات (لو تم إرساله)
    $selectedProduct = null;
    if ($request->filled('product_id')) {
        $selectedProduct = Product::find($request->input('product_id'));
    }

    return view("orders.create", compact("customers", "products", "selectedProduct"));
}


    /*
     *
     *          Edit Order Page
     * */
    public function edit($id) {
        $order = Order::where("id", $id)->first();

        return view("orders.edit", compact("order"));
    }

    /*
     *
     *          Repairing Orders Page
     * */
    public function repairingOrders() {
        return view("orders.repairing-orders");
    }

    // public function store(Request $request) {
    //     $addLead = $request->input("add_lead");
    //     $customerID = $request->input("customer_id");
    //     $address = $request->input("address");
    //     $notes = $request->input("notes");
    //     $name = $request->input("name");
    //     $phone = $request->input("phone");
    //     $governorate = $request->input("governorate");
    //     $couponCode = $request->input("coupon_code");
    //     $products = $request->input("products");
    //     $subtotal = $request->input("subtotal");
    //     $shipping = $request->input("shipping");
    //     $total = $request->input("total");


    //     try {
    //         if($addLead === "true") {
    //             $lead = new Lead();

    //             $lead->name = $name;
    //             $lead->phone_numbers = $phone;
    //             $lead->email = "";
    //             $lead->governorate = $governorate;
    //             $lead->interested_categories = "";
    //             $lead->interested_products_skus = "";
    //             $lead->lead_id = Str::uuid()->toString();
    //             $lead->source = "";
    //             $lead->degree_of_interest = "";
    //             $lead->next_follow_up_period = "";
    //             $lead->potential = "0";
    //             $lead->added_by = auth()->user()->user_id;
    //             $lead->assigned_to = auth()->user()->user_id;
    //             $lead->notes = $notes;
    //             $lead->is_customer = "0";

    //             $lead->save();

    //             $customerID = $lead->lead_id;
    //         }

    //         $performance = PerformanceHistory::where("user_id",auth()->user()->user_id)
    //             ->where("year", date("Y"))->where("month", date("F"))->pluck("level");
    //         if($performance === null) {
    //             $performance = PerformanceHistory::where("user_id",auth()->user()->user_id)
    //                 ->where("year", date("Y"))->where("month", date('F', strtotime('last month')))->pluck("level");
    //         }

    //         if($performance === null) {
    //             $performance = "User";
    //         }

    //         $commission = $this->mapPerformanceToCommission($performance);
    //         $employeeCommission = ($total * $commission) / 100;

    //         $order = new Order();

    //         $order->order_number = random_int(0,9999999999);
    //         $order->customer_id = $customerID;
    //         $order->status = config("constants.PENDING");
    //         $order->address = $address;
    //         $order->latitude = "";
    //         $order->longitude = "";
    //         $order->notes = $notes;
    //         $order->total = $total;
    //         $order->employee_commission = $employeeCommission;
    //         $order->governorate = $governorate;
    //         $order->coupon_code = $couponCode;
    //         $order->delivery_agent_id = "";
    //         $order->employee_id = auth()->user()->user_id;
    //         $order->save();

    //         foreach ($products as $product) {
    //             DB::table("order_product")->insert([
    //                 "order_id" => $order->id,
    //                 "product_id" => $product->id,
    //             ]);
    //         }

    //         return back()->with("success", "Order created successfully");
    //     } catch (\Throwable $e) {
    //         return back()->with("errors", "Creating order failed!");
    //     }


    // }



public function store(Request $request)
{


    // ✅ التحقق من البيانات الأساسية
    $validated = $request->validate([
        'customer_id' => 'nullable', // سمحنا إنها ممكن تبقى null لو add_lead
        'address'     => 'required|string',
        'governorate' => 'required|string',
        'notes'       => 'nullable|string',
        'coupon_code' => 'nullable|string',
        'products'    => 'required|array|min:1',
        'total'       => 'required|numeric|min:0',
    ], [
        'address.required'     => 'من فضلك أدخل العنوان',
        'governorate.required' => 'من فضلك أدخل المحافظة',
        'products.required'    => 'يجب اختيار منتج واحد على الأقل',
    ]);

    $customerID = $validated['customer_id'];

    // ✅ لو المستخدم مش بيضيف lead جديد → تأكد إن فيه customer_id صحيح
    if (!$request->boolean('add_lead') && empty($customerID)) {
        return response()->json([
            'success' => false,
            'message' => 'يجب اختيار عميل أو تفعيل إضافة Lead جديد',
        ], 422);
    }

    // ✅ لو مش Add Lead → تأكد مفيش طلب لنفس العميل من مستخدم آخر خلال آخر 12 ساعة
    if (!$request->boolean('add_lead') && !empty($customerID)) {
        $since = Carbon::now()->subHours(12);

        $recentOrder = Order::where('customer_id', $customerID)
            ->where('employee_id', '!=', auth()->user()->user_id)
            ->where('created_at', '>=', $since)
            ->first();

        if ($recentOrder) {
            return response()->json([
                'success' => false,
                'message' =>
                    'يوجد طلب آخر لهذا العميل خلال آخر 12 ساعة من مستخدم آخر، لا يمكن إنشاء طلب جديد.',
            ], 422);
        }
    }

    try {
        // ✅ لو Add Lead مفعلة → أنشئ lead جديد وخزن الـ ID
        if ($request->boolean('add_lead')) {
            $lead = new Lead();
            $lead->name = $request->input('name');
            $lead->phone_numbers = $request->input('phone');
            $lead->email = '';
            $lead->governorate = $request->input('governorate');
            $lead->interested_categories = '';
            $lead->interested_products_skus = '';
            $lead->lead_id = Str::uuid()->toString();
            $lead->source = '';
            $lead->degree_of_interest = '';
            $lead->next_follow_up_period = '';
            $lead->potential = '0';
            $lead->added_by = auth()->user()->user_id;
            $lead->assigned_to = auth()->user()->user_id;
            $lead->notes = $request->input('notes');
            $lead->is_customer = '0';
            $lead->save();

            $customerID = $lead->lead_id;
        }

        // ✅ حساب العمولة بناءً على الأداء
        $performance =
            PerformanceHistory::where('user_id', auth()->user()->user_id)
                ->where('year', date('Y'))
                ->where('month', date('F'))
                ->pluck('level')
                ->first() ?? 'User';

        $commissionRate = $this->mapPerformanceToCommission($performance);
        $employeeCommission =
            ($request->input('total') * $commissionRate) / 100;

        // ✅ إنشاء الطلب
        $order = new Order();
        $order->order_number = random_int(0, 9999999999);
        $order->customer_id = $customerID;
        $order->status = config('constants.PENDING');
        $order->address = $request->input('address');
        $order->latitude = '';
        $order->longitude = '';
        $order->notes = $request->input('notes') ?? '';
        $order->total = $request->input('total');
        $order->employee_commission = $employeeCommission;
        $order->governorate = $request->input('governorate');
        $order->coupon_code = $request->input('coupon_code') ?? '';
        $order->delivery_agent_id = '';
        $order->employee_id = auth()->user()->user_id;
        $order->save();

        // ✅ حفظ المنتجات
        foreach ($request->input('products') as $product) {
            DB::table('order_product')->insert([
                'order_id' => $order->id,
                'product_id' => $product['id'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الطلب بنجاح',
            'order_id' => $order->id,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء إنشاء الطلب: ' . $e->getMessage(),
        ], 500);
    }
}







    protected function mapPerformanceToCommission(string $performance) {
        $commission = 0;
        switch ($performance) {
            case "User":
                $commission = 0;
                break;
            case "Beginner":
                $commission = 1;
                break;
            case "Rising":
                $commission = 2;
                break;
            case "Expert":
                $commission = 3;
                break;
            case "Pioneer":
                $commission = 4;
                break;
            case "Professional":
                $commission = 5;
                break;
        }
        return $commission;
    }
}
