<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemTablesWarehouseSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $warehouseTables = [
            'warehouse',
            'warehouse_orders',
            'warehouse_move',
            'warehouse_returns',
            'warehouse_feeding',
            'warehouse_stock',
            'warehouse_materials',
            'warehouse_damaged',
        ];

        foreach ($warehouseTables as $tableName) {
            DB::table('system_tables')->insertOrIgnore([
                'name' => $tableName,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('Warehouse tables added to system_tables successfully!');
    }
}
