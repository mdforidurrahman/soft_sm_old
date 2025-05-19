<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' =>1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'name' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'image' => $this->faker->imageUrl(640, 480, 'products'),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'quantity' => $this->faker->numberBetween(1, 100),
            'store_id' => \App\Models\Store::factory(),
            'min_stock' => $this->faker->numberBetween(1, 10),
            'category_id' => \App\Models\ProductCategory::factory(),
            'slug' => Str::slug($this->faker->unique()->words(3, true)),
            'sku' => strtoupper($this->faker->unique()->bothify('SKU-####')),
            'status' => $this->faker->boolean(),
            'manage_stock' => $this->faker->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
