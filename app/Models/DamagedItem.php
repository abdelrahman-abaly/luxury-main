<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamagedItem extends Model
{
    protected $fillable = [
        'product_id',
        'damaged_quantity',
        'damage_level',
        'damage_reason',
        'reported_by',
        'reported_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'reported_at' => 'datetime',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    // Scopes
    public function scopeMaterials($query)
    {
        return $query->whereHas('product', function ($q) {
            $q->whereIn('category', ['Boxes', 'Shopping Bags', 'Prime Bags', 'Flyerz']);
        });
    }

    public function scopeGoods($query)
    {
        return $query->whereHas('product', function ($q) {
            $q->whereNotIn('category', ['Boxes', 'Shopping Bags', 'Prime Bags', 'Flyerz']);
        });
    }

    public function scopeByDamageLevel($query, $level)
    {
        return $query->where('damage_level', $level);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
