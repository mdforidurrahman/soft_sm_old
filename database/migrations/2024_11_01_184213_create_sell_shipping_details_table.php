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
        Schema::create('sell_shipping_details', function (Blueprint $table) {
            $table->id();
          
          
              $table->foreignId('sell_id')
               ->constrained('sells')
               ->onDelete('cascade');

            $table->string('shipping_address');
            $table->string('shipping_method')->nullable();
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->date('expected_delivery_date')->nullable();
            $table->string('tracking_number')->nullable();
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sell_shipping_details');
    }
};
