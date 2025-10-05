<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('latitude')->nullable()->change();
            $table->string('longitude')->nullable()->change();
            $table->text('notes')->nullable()->change();
            $table->string('governorate')->nullable()->change();
            $table->string('coupon_code')->nullable()->change();
            $table->string('delivery_agent_id')->nullable()->change();
            $table->string('employee_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('latitude')->nullable(false)->change();
            $table->string('longitude')->nullable(false)->change();
            $table->text('notes')->nullable(false)->change();
            $table->string('governorate')->nullable(false)->change();
            $table->string('coupon_code')->nullable(false)->change();
            $table->string('delivery_agent_id')->nullable(false)->change();
            $table->string('employee_id')->nullable(false)->change();
        });
    }
};
