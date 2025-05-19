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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            // Foreign key referencing the 'stores' table
            $table->foreignId('store_id')->constrained('stores');

            // Foreign key referencing the 'expense_categories' table
            $table->foreignId('expense_category_id')->constrained('expense_category');

            $table->string('reference_no')->unique();
            $table->date('expense_date')->nullable();

            // Additional fields for expense details
            $table->unsignedBigInteger(column: 'expense_for_id')->nullable();
            $table->unsignedBigInteger('expense_for_contact')->nullable();
            $table->string('document')->nullable();
            $table->decimal('total_amount', 15, 2)->nullable(); // Use decimal for amount
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
