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
        Schema::table('repairing_orders', function (Blueprint $table) {
           $table->text('product_images')->nullable()->change(); // لو حابب تخليها قابل للـ null (يتطلب doctrine/dbal)
            $table->string('client_phone')->nullable()->after('order_id');
            $table->boolean('client_out_of_system')->default(false)->after('client_phone');
            $table->decimal('price', 10, 2)->nullable()->after('status');
            $table->unsignedBigInteger('user_id')->nullable()->after('client_out_of_system');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repairing_orders', function (Blueprint $table) {
            $table->text('product_images')->nullable(false)->change();
            $table->dropColumn(['client_phone','client_out_of_system','price','user_id']);
        });
    }
};
