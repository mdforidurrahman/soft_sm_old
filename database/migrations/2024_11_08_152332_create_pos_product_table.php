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
        Schema::create('pos_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pos_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('unit_cost_before_tax', 10, 2)->nullable();
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('net_cost', 10, 2)->nullable();
            $table->decimal('profit_margin', 5, 2)->nullable();
            $table->decimal('unit_selling_price', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_products');
    }
};
