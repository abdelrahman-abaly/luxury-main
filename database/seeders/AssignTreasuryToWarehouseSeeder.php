<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignTreasuryToWarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get warehouse role
        $warehouseRole = DB::table('roles')->where('role_code', 'warehouse')->first();

        if (!$warehouseRole) {
            $this->command->error('Warehouse role not found!');
            return;
        }

        // Get treasury permissions
        $treasuryPermissions = DB::table('permissions')
            ->where('table_name', 'treasury')
            ->get();

        if ($treasuryPermissions->isEmpty()) {
            $this->command->error('Treasury permissions not found!');
            return;
        }

        // Assign permissions to warehouse role
        foreach ($treasuryPermissions as $permission) {
            // Check if already assigned
            $exists = DB::table('permission_role')
                ->where('role_id', $warehouseRole->id)
                ->where('permission_id', $permission->id)
                ->exists();

            if (!$exists) {
                DB::table('permission_role')->insert([
                    'role_id' => $warehouseRole->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info("Assigned permission: {$permission->table_name} - {$permission->ability}");
            } else {
                $this->command->info("Permission already assigned: {$permission->table_name} - {$permission->ability}");
            }
        }

        $this->command->info('Treasury permissions assigned to warehouse role successfully!');
    }
}
