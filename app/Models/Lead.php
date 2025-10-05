<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone_numbers',
        'email',
        'governorate',
        'interested_categories',
        'interested_products_skus',
        'lead_id',
        'source',
        'degree_of_interest',
        'next_follow_up_period',
        'potential',
        'added_by',
        'assigned_to',
        'notes',
        'is_customer'
    ];
}
