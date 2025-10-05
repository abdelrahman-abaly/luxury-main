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
        Schema::create('repairing_orders', function (Blueprint $table) {
            $table->id();
            $table->text("product_images");
            $table->text("maintenance_note");
            $table->string("product_name");
            $table->string("request_number");
            $table->string("warranty");
            $table->string("status");
            $table->string("order_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairing_orders');
    }
};
