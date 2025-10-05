<?php

namespace Database\Seeders;

use App\Models\CommissionWallet;
use App\Models\Lead;
use App\Models\Order;
use App\Models\PerformanceHistory;
use App\Models\Product;
use App\Models\SalaryWallet;
use App\Models\ScheduledTask;
use App\Models\TransactionHistory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //        Lead::factory(50)->create();
        //        Order::factory(100)->create();
        //        ScheduledTask::factory(80)->create();
        //        Product::factory(100)->create();
        //        PerformanceHistory::factory(500)->create();
        //        TransactionHistory::factory(500)->create();
        //        CommissionWallet::factory(500)->create();
        SalaryWallet::factory(500)->create();
    }
}
