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
            $table->string('customer_full_name')->nullable()->after('customer_phone');
            $table->string('billing_full_name')->nullable()->after('customer_full_name');
            $table->string('shipping_full_name')->nullable()->after('billing_full_name');
            $table->string('customer_first_name')->nullable()->change();
            $table->string('customer_last_name')->nullable()->change();
            $table->string('billing_first_name')->nullable()->change();
            $table->string('billing_last_name')->nullable()->change();
            $table->string('shipping_first_name')->nullable()->change();
            $table->string('shipping_last_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('customer_full_name');
            $table->dropColumn('billing_full_name');
            $table->dropColumn('shipping_full_name');
            $table->string('customer_first_name')->nullable(false)->change();
            $table->string('customer_last_name')->nullable(false)->change();
            $table->string('billing_first_name')->nullable(false)->change();
            $table->string('billing_last_name')->nullable(false)->change();
            $table->string('shipping_first_name')->nullable(false)->change();
            $table->string('shipping_last_name')->nullable(false)->change();
        });
    }
};
