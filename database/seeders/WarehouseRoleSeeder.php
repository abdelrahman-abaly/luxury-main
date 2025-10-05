<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseRoleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create Warehouse Role
        $warehouseRole = Role::create([
            'role_code' => 'warehouse',
            'role_name' => 'Warehouse Manager'
        ]);

        // Define warehouse permissions
        $warehousePermissions = [
            // Main warehouse access
            ['table_name' => 'warehouse', 'ability' => 'view'],
            
            // Warehouse orders permissions
            ['table_name' => 'warehouse_orders', 'ability' => 'view'],
            ['table_name' => 'warehouse_orders', 'ability' => 'create'],
            ['table_name' => 'warehouse_orders', 'ability' => 'edit'],
            ['table_name' => 'warehouse_orders', 'ability' => 'delete'],
            
            // Warehouse move manager permissions
            ['table_name' => 'warehouse_move', 'ability' => 'view'],
            ['table_name' => 'warehouse_move', 'ability' => 'create'],
            ['table_name' => 'warehouse_move', 'ability' => 'edit'],
            
            // Warehouse returns permissions
            ['table_name' => 'warehouse_returns', 'ability' => 'view'],
            ['table_name' => 'warehouse_returns', 'ability' => 'create'],
            ['table_name' => 'warehouse_returns', 'ability' => 'edit'],
            ['table_name' => 'warehouse_returns', 'ability' => 'delete'],
            
            // Warehouse feeding requests permissions
            ['table_name' => 'warehouse_feeding', 'ability' => 'view'],
            ['table_name' => 'warehouse_feeding', 'ability' => 'create'],
            ['table_name' => 'warehouse_feeding', 'ability' => 'edit'],
            
            // Warehouse stock permissions
            ['table_name' => 'warehouse_stock', 'ability' => 'view'],
            ['table_name' => 'warehouse_stock', 'ability' => 'create'],
            ['table_name' => 'warehouse_stock', 'ability' => 'edit'],
            ['table_name' => 'warehouse_stock', 'ability' => 'delete'],
            
            // Warehouse materials permissions
            ['table_name' => 'warehouse_materials', 'ability' => 'view'],
            ['table_name' => 'warehouse_materials', 'ability' => 'create'],
            ['table_name' => 'warehouse_materials', 'ability' => 'edit'],
            ['table_name' => 'warehouse_materials', 'ability' => 'delete'],
            
            // Warehouse damaged permissions
            ['table_name' => 'warehouse_damaged', 'ability' => 'view'],
            ['table_name' => 'warehouse_damaged', 'ability' => 'create'],
            ['table_name' => 'warehouse_damaged', 'ability' => 'edit'],
            ['table_name' => 'warehouse_damaged', 'ability' => 'delete'],
            
            // Communication permissions (shared with other roles)
            ['table_name' => 'chats', 'ability' => 'view'],
            ['table_name' => 'calls', 'ability' => 'view'],
            ['table_name' => 'emails', 'ability' => 'view'],
        ];

        // Create permissions and associate with warehouse role
        foreach ($warehousePermissions as $permissionData) {
            // Check if permission already exists
            $permission = Permission::firstOrCreate([
                'table_name' => $permissionData['table_name'],
                'ability' => $permissionData['ability']
            ]);

            // Associate permission with warehouse role
            DB::table('permission_role')->insertOrIgnore([
                'role_id' => $warehouseRole->id,
                'permission_id' => $permission->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('Warehouse role and permissions created successfully!');
    }
}