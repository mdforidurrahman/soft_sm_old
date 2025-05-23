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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
			$table->foreignId('store_id')->constrained('stores');
			$table->decimal('current_balance', 10, 2)->default(0);
			$table->foreignId('created_by_id')->constrained('users');
			$table->timestamps();

			$table->unique(['store_id']);
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
