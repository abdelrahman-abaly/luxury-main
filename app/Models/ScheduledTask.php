<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledTask extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "lead_id",
        "task_done",
        "complete_date",
        "task_date"
    ];
}
