<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\ExpenseCategory;
use App\Models\GoodCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersSeeder::class,
            RoleSeeder::class,
            RoleUserSeeder::class,
            PermissionSeeder::class,
            PermissionRoleSeeder::class,
            PermissionUserSeeder::class,

            StoreSeeder::class,
            ContactSeeder::class,
            ProductCategorySeeder::class,
//            ProductSeeder::class,
            ExpenseCategorySeeder::class,
//            PurchaseSeeder::class,
//            ExpenseSeeder::class,
//            PlanSeeder::class,
        ]);
    }
}
