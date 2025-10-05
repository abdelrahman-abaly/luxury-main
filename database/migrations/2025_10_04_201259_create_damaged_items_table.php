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
        Schema::create('damaged_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('damaged_quantity');
            $table->enum('damage_level', ['minor', 'moderate', 'severe']);
            $table->text('damage_reason')->nullable();
            $table->foreignId('reported_by')->constrained('users');
            $table->timestamp('reported_at');
            $table->enum('status', ['reported', 'repaired', 'disposed'])->default('reported');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damaged_items');
    }
};
