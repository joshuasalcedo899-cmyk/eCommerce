<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('looks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('look_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('look_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unique(['look_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('look_product');
        Schema::dropIfExists('looks');
    }
};
