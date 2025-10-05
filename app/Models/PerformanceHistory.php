<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceHistory extends Model
{

    use HasFactory;
    protected  $fillable = [
        'level',
        'month',
        'year',
        'orders_count',
        'commission_amount',
        'status',
        'user_id'
    ];
}
