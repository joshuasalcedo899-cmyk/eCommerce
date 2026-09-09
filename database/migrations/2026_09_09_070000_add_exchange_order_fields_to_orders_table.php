<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_exchange')->default(false)->after('status');
            $table->foreignId('exchange_from_order_id')
                ->nullable()
                ->after('is_exchange')
                ->constrained('orders')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('exchange_from_order_id');
            $table->dropColumn('is_exchange');
        });
    }
};
