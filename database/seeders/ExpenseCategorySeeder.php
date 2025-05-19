<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        ExpenseCategory::factory()->count(5)->create();

        $categories = [
            [
                'name' => 'Travel',
                'code' => Str::upper(Str::random(5)), // Generate a random unique code
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Office Supplies',
                'code' => Str::upper(Str::random(5)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Utilities',
                'code' => Str::upper(Str::random(5)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marketing',
                'code' => Str::upper(Str::random(5)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Employee Benefits',
                'code' => Str::upper(Str::random(5)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('expense_category')->insert($categories);

    }
}
