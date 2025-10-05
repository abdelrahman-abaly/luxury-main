<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_id',
        'role_id',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission($table, $ability)
    {
        // Check direct user permissions first
        $hasDirectPermission = $this->permissions()
            ->where('table_name', $table)
            ->where('ability', $ability)
            ->exists();

        if ($hasDirectPermission) {
            return true;
        }

        // Check role permissions
        if ($this->role) {
            return $this->role->permissions()
                ->where('table_name', $table)
                ->where('ability', $ability)
                ->exists();
        }

        return false;
    }

    public function hasRole($roleCode)
    {
        return $this->role && $this->role->role_code === $roleCode;
    }

    public function deliveryOrders()
    {
        return $this->hasMany(Order::class, 'delivery_agent_id', 'user_id');
    }
}
