<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('look_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('look_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('image_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('look_variant_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('look_variant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unique(['look_variant_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('look_variant_product');
        Schema::dropIfExists('look_variants');
    }
};
