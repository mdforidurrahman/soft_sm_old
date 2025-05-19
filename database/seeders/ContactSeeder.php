<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        Contact::factory(5)->create();


        $faker = \Faker\Factory::create();
        $contacts = [];

        for ($i = 0; $i < 10; $i++) {
            $contacts[] = [
                'name' => $faker->name(),
                'role' => $faker->randomElement(['supplier', 'customer']),
                'phone' => '01' . $faker->randomElement([3, 5, 6, 7, 8, 9]) . $faker->numberBetween(10000000, 99999999),
                'address' => $faker->address(),
                'nid' => $faker->unique()->numberBetween(100000000, 999999999),
                'contact_id' => $faker->unique()->numberBetween(100000000, 999999999),
                'created_by' =>1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('contacts')->insert($contacts);
    }
}
