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
        Schema::create('product_weight_charges', function (Blueprint $table) {
            $table->id();
            $table->decimal('weight', 8, 2); // Weight column with precision
            $table->decimal('charge', 10, 2); // Charge column with precision
            $table->boolean('status')->default(1); // Status column, default active
            $table->unsignedBigInteger('created_by')->nullable(); // Created by user ID
            $table->unsignedBigInteger('updated_by')->nullable(); // Updated by user ID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_weight_charges');
    }
};
