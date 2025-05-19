<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'expense_category_id' => 1,
            'store_id' => \App\Models\Store::factory(),
            'reference_no' => $this->faker->unique()->numerify('REF###'),
            'expense_date' => $this->faker->date(),
            'expense_for_id' => User::factory(),
            'expense_for_contact' => Contact::factory(),
            'document' => $this->faker->word() . '.pdf',
            'total_amount' => $this->faker->randomFloat(2, 100, 5000),
            'note' => $this->faker->sentence(),
            'is_refund' => $this->faker->boolean(),
            'paid_amount' => $this->faker->randomFloat(2, 50, 5000),
            'paid_date' => $this->faker->date(),
            'payment_method' => $this->faker->randomElement(['Cash', 'Card', 'Bank Transfer']),
            'payment_note' => $this->faker->sentence(),
            'status' => $this->faker->boolean(),
        ];
    }
}
