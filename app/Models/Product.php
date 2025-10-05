<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    use HasFactory;
    protected $fillable = [
        'name',
        'sku',
        'category',
        'size',
        'color',
        'normal_price',
        'sale_price',
        'status',
        'warehouse_id',
        'stock_quantity',
        'description',
        'images',
        'woocommerce_id',
        'woocommerce_synced_at'
    ];
}
