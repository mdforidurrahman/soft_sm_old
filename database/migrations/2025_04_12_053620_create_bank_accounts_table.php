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
        Schema::create('bank_accounts', function (Blueprint $table) {
			$table->id();
			$table->foreignId('store_id')->constrained('stores');
			$table->string('bank_name');
			$table->string('account_number');
			$table->string('account_holder_name');
			$table->decimal('current_balance', 10, 2)->default(0);
			$table->timestamps();

			$table->index('account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
