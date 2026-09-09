<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_item_returns', function (Blueprint $table) {
            $table->decimal('replacement_subtotal', 10, 2)->nullable()->after('replacement_size');
            $table->decimal('price_difference', 10, 2)->nullable()->after('replacement_subtotal');
            $table->string('settlement_method')->nullable()->after('price_difference');
        });
    }

    public function down(): void
    {
        Schema::table('order_item_returns', function (Blueprint $table) {
            $table->dropColumn(['replacement_subtotal', 'price_difference', 'settlement_method']);
        });
    }
};
