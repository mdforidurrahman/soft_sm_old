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
        Schema::create('daily_cash_reports', function (Blueprint $table) {
            $table->id();
			$table->foreignId('store_id')->constrained('stores');
			$table->date('report_date');
			$table->decimal('opening_balance', 10, 2);
			$table->decimal('total_income', 10, 2)->default(0);
			$table->decimal('total_expenses', 10, 2)->default(0);
			$table->decimal('total_withdrawals', 10, 2)->default(0);
			$table->decimal('total_adjustments', 10, 2)->default(0);
			$table->decimal('closing_balance', 10, 2);
			$table->foreignId('created_by_id')->constrained('users');
			$table->unique(['store_id', 'report_date']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_cash_reports');
    }
};
