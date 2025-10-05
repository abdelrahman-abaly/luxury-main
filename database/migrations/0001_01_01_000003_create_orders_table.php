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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string("order_number");
            $table->string("customer_id");
            $table->string("status");
            $table->text("address");
            $table->string("latitude");
            $table->string("longitude");
            $table->text("notes");
            $table->string("total");
            $table->string("employee_commission");
            $table->string("governorate");
            $table->string("coupon_code");
            $table->string("delivery_agent_id");
            $table->string("employee_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
