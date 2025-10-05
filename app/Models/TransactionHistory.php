<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    use HasFactory;

    protected $table = 'transactions_history';
    protected $fillable = [
        'transaction_id',
        'type',
        'send_date',
        'status',
        'amount',
        'balance',
        'new_balance',
        'user_id'
    ];
}
