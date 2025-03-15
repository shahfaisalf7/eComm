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
            // Add a new column 'billing_zone' and 'shipping_zone'
            $table->string('billing_zone')->nullable()->after('billing_zip');
            $table->string('shipping_zone')->nullable()->after('shipping_zip');

            // Modify 'billing_zip' and 'shipping_zip' to be nullable
            $table->string('billing_zip')->nullable()->change();
            $table->string('shipping_zip')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
             // Remove the 'billing_zone' and 'shipping_zone' column
            $table->dropColumn('billing_zone');
            $table->dropColumn('shipping_zone');

            // Revert 'billing_zip' and 'shipping_zip' to non-nullable
            $table->string('billing_zip')->nullable(false)->change();
            $table->string('shipping_zip')->nullable(false)->change();
        });
    }
};
