<?php


namespace App\Http\Controllers;

use App\Models\RepairingOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RepairingOrderController extends Controller
{
    // عرض الصفحة مع الطلبات السابقة
    public function index()
    {
        $orders = RepairingOrder::latest()->get();
        return view('orders.repairing-orders', compact('orders'));
    }

    // حفظ طلب جديد
    public function store(Request $request)
    {
        $request->validate([
            'maintenance_note' => 'required|string',
            'product_name' => 'required|string',
            'warranty' => 'required|string',
            'product_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'client_phone' => 'nullable|string',
            'client_out_of_system' => 'nullable|boolean',
        ]);

        // حفظ الصور على disk public
        $images = [];
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $file) {
                $path = $file->store('repairing_orders', 'public');
                $images[] = $path;
            }
        }

        $order = RepairingOrder::create([
            'product_images' => $images,
            'maintenance_note' => $request->maintenance_note,
            'product_name' => $request->product_name,
            'request_number' => 'TEMP', // هنحدّثه بعد الانشاء
            'warranty' => $request->warranty,
            'status' => $request->status ?? 'pending',
            'order_id' => $request->order_id ?? null,
            'client_phone' => $request->client_phone ?? null,
            'client_out_of_system' => $request->has('client_out_of_system'),
            'price' => $request->price ?? null,
            'user_id' => auth()->id() ?? null,
        ]);

        // بتوليد رقم الطلب بناءً على id (atomic ومرتب)
        $order->request_number = 'REP-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
        $order->save();

        return redirect()->back()->with('success', 'Repair request submitted successfully!');
    }

    // بحث برقم التليفون -- يرجّع JSON بالـ orders السابقة (من جدول orders لو موجود أو من repairing_orders)
    public function searchByPhone(Request $request)
    {
        $phone = $request->get('phone');
        $results = [];

        // لو عندك Model Order (فحص سريع)
        if (class_exists(\App\Models\Order::class)) {
            $orders = \App\Models\Order::where('customer_phone', $phone)->get();
            foreach ($orders as $o) {
                $results[] = [
                    'source' => 'order',
                    'id' => $o->id,
                    'product_name' => $o->product_name ?? ($o->name ?? 'Product'),
                    'purchase_date' => $o->purchase_date ?? null,
                    'warranty_months' => $o->warranty_months ?? null,
                ];
            }
        }

        // أضف أي repairing orders بنفس الرقم
        $repairing = RepairingOrder::where('client_phone', $phone)->get();
        foreach ($repairing as $r) {
            $results[] = [
                'source' => 'repairing',
                'id' => $r->id,
                'product_name' => $r->product_name,
                'warranty' => $r->warranty,
                'request_number' => $r->request_number,
            ];
        }

        return response()->json($results);
    }
}
