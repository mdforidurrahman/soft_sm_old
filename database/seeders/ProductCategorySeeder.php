<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        ProductCategory::factory(20)->create();

        $categories = [
            [
                'name' => 'Electronics',
                'slug' => Str::slug('Electronics'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Furniture',
                'slug' => Str::slug('Furniture'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Clothing',
                'slug' => Str::slug('Clothing'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Beauty Products',
                'slug' => Str::slug('Beauty Products'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Books',
                'slug' => Str::slug('Books'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('product_categories')->insert($categories);
    }

}
