<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DriverRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Driver Role
        $driverRoleId = DB::table('roles')->insertGetId([
            'role_code' => 'driver',
            'role_name' => 'Driver',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Driver Permissions
        $permissions = [
            [
                'table_name' => 'driver',
                'ability' => 'view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'table_name' => 'driver_orders',
                'ability' => 'view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'table_name' => 'driver_orders',
                'ability' => 'edit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($permissions as $permission) {
            $permissionId = DB::table('permissions')->insertGetId($permission);

            // Attach permission to driver role
            DB::table('permission_role')->insert([
                'role_id' => $driverRoleId,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Driver role and permissions created successfully!');
        $this->command->info("Driver Role ID: {$driverRoleId}");
    }
}
