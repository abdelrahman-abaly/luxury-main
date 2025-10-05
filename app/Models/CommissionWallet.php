<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionWallet extends Model
{

    use HasFactory;

    protected $table = 'commission_wallets';
    protected $fillable = [
        'on_processing',
        'pending',
        'ready_to_pay',
        'completed',
        'user_id'
    ];
}
