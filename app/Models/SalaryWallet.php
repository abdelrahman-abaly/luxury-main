<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryWallet extends Model
{
    use HasFactory;

    protected $table = 'salary_wallets';
    protected $fillable = [
        'days_worked',
        'salary_wallet',
        'ready_salary',
        'borrowing_balance',
        'user_id'
    ];
}
