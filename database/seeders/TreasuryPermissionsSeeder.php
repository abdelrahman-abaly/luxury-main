<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TreasuryPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Treasury Permissions
        $permissions = [
            [
                'table_name' => 'treasury',
                'ability' => 'view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'table_name' => 'treasury',
                'ability' => 'export',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($permissions as $permission) {
            // Check if permission already exists
            $existingPermission = DB::table('permissions')
                ->where('table_name', $permission['table_name'])
                ->where('ability', $permission['ability'])
                ->first();

            if (!$existingPermission) {
                DB::table('permissions')->insert($permission);
                $this->command->info("Created permission: {$permission['table_name']} - {$permission['ability']}");
            } else {
                $this->command->info("Permission already exists: {$permission['table_name']} - {$permission['ability']}");
            }
        }

        $this->command->info('Treasury permissions created successfully!');
    }
}
