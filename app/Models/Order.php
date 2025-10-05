<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_number',
        'customer_id',
        'status',
        'address',
        'latitude',
        'longitude',
        'notes',
        'total',
        'employee_commission',
        'governorate',
        'coupon_code',
        'delivery_agent_id',
        'employee_id',
        'expected_delivery_date',
        'woocommerce_id',
        'woocommerce_synced_at'
    ];

    /**
     * Get the customer that owns the order.
     */
    public function customer()
    {
        return $this->belongsTo(Lead::class, 'customer_id', 'lead_id')->where('is_customer', '1');
    }

    /**
     * Get the employee that created the order.
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'user_id');
    }

    /**
     * Get the products for the order.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Get the delivery agent (driver) for the order.
     */
    public function deliveryAgent()
    {
        return $this->belongsTo(User::class, 'delivery_agent_id', 'user_id');
    }
}
