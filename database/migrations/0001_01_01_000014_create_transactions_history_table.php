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
        Schema::create('transactions_history', function (Blueprint $table) {
            $table->id();
            $table->string("transaction_id");
            $table->string("type");
            $table->string("send_date");
            $table->string("status");
            $table->string("amount");
            $table->string("balance");
            $table->string("new_balance");
            $table->string("user_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions_history');
    }
};  
