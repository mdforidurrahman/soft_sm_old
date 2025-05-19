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
        Schema::create('sell_payments', function (Blueprint $table) {
            $table->id();
              $table->foreignId('sell_id')
               ->constrained('sells')
               ->onDelete('cascade');

            $table->decimal('amount', 10, 2);
            $table->string('paid_on');
            $table->string('payment_method'); // cash, bank_transfer, credit_card, etc.
            $table->string('payment_account')->nullable();
            $table->string('payment_status')->default('completed');
            $table->string('transaction_reference')->nullable();
            $table->text('payment_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sell_payments');
    }
};
