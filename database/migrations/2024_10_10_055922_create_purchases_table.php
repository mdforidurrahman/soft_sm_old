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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_id')->constrained('stores');

            $table->foreignId('supplier_id')->constrained('contacts');
            $table->unsignedBigInteger('customer_id')->nullable();



            $table->string('reference_no')->unique();
            $table->date('purchase_date');
            $table->string('purchase_status');
            $table->string('payment_term')->nullable();
            $table->string('payment_term_type')->nullable();
            $table->string('document_path')->nullable();
            $table->decimal('total_before_tax', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            // Discount related fields
            $table->string('discount_type')->nullable(); // percentage, fixed
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);


            $table->decimal('net_total', 10, 2)->nullable();
            $table->text('additional_notes')->nullable();

            $table->decimal('advance_balance', 10, 2)->default(0);
            $table->decimal('payment_due', 10, 2)->default(0);
            $table->string('payment_status')->default('pending'); // paid, partial, pending
            $table->date('payment_due_date')->nullable();

            // Additional purchase details
            $table->string('purchase_type')->nullable();
            $table->boolean('is_advance_payment')->default(false);

            $table->foreign('customer_id')->references('id')->on('stores')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
