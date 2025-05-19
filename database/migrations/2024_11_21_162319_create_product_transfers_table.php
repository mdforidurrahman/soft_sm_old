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
        Schema::create('product_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_store_id');
            $table->unsignedBigInteger('to_store_id');
            $table->unsignedBigInteger('store_product_id');
            $table->integer('quantity');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');

            $table->foreign('from_store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('to_store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('store_product_id')->references('id')->on('products')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_transfers');
    }
};
