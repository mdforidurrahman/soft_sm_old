<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            $table->uuid('contact_id');

            $table->unsignedBigInteger('store_id')->nullable();

            $table->string('name');
            $table->string('father_name')->nullable();

            $table->enum('role', ['customer', 'supplier'])
                ->default('customer');
          //  $table->enum('sales_type', ['cash', 'credit'])
            //    ->default('customer');

            $table->string('nid')->nullable();
            $table->string('phone')->nullable();
            $table->string('district')->nullable();
            $table->string('thana')->nullable();
            $table->string('post_office')->nullable();
            $table->string('village')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
