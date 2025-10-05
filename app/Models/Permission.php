<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'table_name',
        'ability'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
