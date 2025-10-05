<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class RepairingOrder extends Model
// {
//     protected $fillable = [
//         'product_images',
//         'maintenance_note',
//         'product_name',
//         'request_number',
//         'warranty',
//         'status',
//         'order_id'
//     ];
// }




namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairingOrder extends Model
{
    protected $fillable = [
        'product_images',
        'maintenance_note',
        'product_name',
        'request_number',
        'warranty',
        'status',
        'order_id',
        'client_phone',
        'client_out_of_system',
        'price',
        'user_id',
    ];

    protected $casts = [
        'product_images' => 'array',
        'client_out_of_system' => 'boolean',
        'price' => 'decimal:2',
    ];
}
