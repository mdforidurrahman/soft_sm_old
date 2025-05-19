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
        Schema::create('business_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->unique();
            $table->foreign('store_id')->references('id')->on('stores')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->string('landmark');
            $table->string('city');
            $table->string('zip_code');
            $table->string('state');
            $table->string('country');
            $table->tinyInteger('status')->default(0);

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_locations');
    }
};
