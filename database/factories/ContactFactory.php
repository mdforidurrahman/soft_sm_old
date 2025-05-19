<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'role'=> $this->faker->randomElement(['supplier','customer']),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'nid' => $this->faker->randomNumber(9),
            'contact_id'=>$this->faker->randomNumber(9),
            'created_by'=>User::factory(),
            'updated_by'=>User::factory(),
        ];
    }
}
