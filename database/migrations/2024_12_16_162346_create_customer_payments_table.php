<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('contacts');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method');
            $table->datetime('paid_on');
            $table->foreignId('payment_account_id')
                ->nullable()
                ->constrained('payment_accounts');
            $table->string('document')->nullable();
            $table->text('payment_note')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_payments');
    }
};
