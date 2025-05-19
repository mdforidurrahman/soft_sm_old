<?php

namespace Database\Factories;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Store;
use App\Models\Contact;
use App\Models\User;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'store_id' => Store::factory(),
            'supplier_id' => $this->faker->numberBetween(1, 10),
            'customer_id' => $this->faker->numberBetween(1, 10),

            'reference_no' => $this->faker->unique()->numerify('REF####'),
            'purchase_date' => $this->faker->date(),
            'purchase_status' => $this->faker->randomElement(['pending', 'completed']),
            'address' => $this->faker->address(),
            'business_location' => $this->faker->city(),
            'pay_term' => $this->faker->numberBetween(1, 30),
            'pay_term_type' => $this->faker->randomElement(['days', 'months']),
            'document' => $this->faker->word() . '.pdf',

            'quantity' => $this->faker->numberBetween(1, 100),
            'unit_price' => $this->faker->randomFloat(2, 10, 500),

            'discount' => $this->faker->randomFloat(2, 0, 100),
            'discount_type' => $this->faker->randomElement(['percentage', 'fixed']),
            'total' => $this->faker->randomFloat(2, 100, 10000),

            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
