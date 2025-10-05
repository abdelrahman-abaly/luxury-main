<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WarehouseUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $warehouseRole = Role::where('role_code', 'warehouse')->first();

        if (!$warehouseRole) {
            $this->command->error('Warehouse role not found! Please run WarehouseRoleSeeder first.');
            return;
        }

        $user = User::create([
            'name' => 'Mostafa Mounir',
            'email' => 'warehouse@example.com',
            'password' => bcrypt('password'),
            'role_id' => $warehouseRole->id,
            'user_id' => Str::uuid()->toString(),
            'avatar' => ''
        ]);

        $this->command->info('Warehouse user created: ' . $user->email);
        $this->command->info('Password: password');
    }
}
