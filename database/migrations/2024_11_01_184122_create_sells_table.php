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
        Schema::create('sells', function (Blueprint $table) {
    $table->id();
    
    $table->foreignId('store_id')
          ->constrained('stores')
          ->onDelete('cascade');  // Add cascade delete for store relationship
    
    $table->foreignId('customer_id')
          ->constrained('contacts')
          ->onDelete('cascade');  // Add cascade delete for customer relationship
    
    $table->string('reference_no')->unique();
    $table->string('invoice_no')->unique();
    $table->date('sell_date');
    $table->string('sell_status');
    
    $table->string('payment_term')->nullable();
    $table->string('payment_term_type')->nullable();
    
    $table->decimal('total_before_tax', 10, 2);
    $table->decimal('tax_amount', 10, 2)->default(0);
    
    $table->string('discount_type')->nullable();
    $table->decimal('discount_amount', 10, 2)->default(0);
    $table->decimal('discount_percentage', 5, 2)->default(0);
    
    $table->decimal('net_total', 10, 2)->nullable();
    
    $table->decimal('advance_balance', 10, 2)->default(0);
    $table->decimal('payment_due', 10, 2)->default(0);
    $table->string('payment_status')->default('pending');
    
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sells');
    }
};
