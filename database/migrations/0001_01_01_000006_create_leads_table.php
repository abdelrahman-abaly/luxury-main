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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("phone_numbers");
            $table->string("email");
            $table->string("governorate");
            $table->string("interested_categories");
            $table->string("interested_products_skus");
            $table->string("source");
            $table->string("degree_of_interest");
            $table->string("next_follow_up_period");
            $table->string("potential");
            $table->string("added_by");
            $table->string("assigned_to");
            $table->text("notes");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
