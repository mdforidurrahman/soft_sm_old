<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$permissions = [
			[ 'name' => 'accounts_view', 'display_name' => 'View Accounts Menu', 'description' => 'Can view the Accounts menu', 'category' => 'Accounts'],
			[ 'name' => 'accounts_transaction', 'display_name' => 'View Account Transactions', 'description' => 'Can view account transactions', 'category' => 'Accounts'],
			[ 'name' => 'accounts_banks_view', 'display_name' => 'View Banks', 'description' => 'Can view bank list', 'category' => 'Accounts'],
			[ 'name' => 'accounts_withdrawals', 'display_name' => 'View Withdrawals', 'description' => 'Can view withdrawals', 'category' => 'Accounts'],
		];
		foreach ($permissions as $permissionData) {
			Permission::firstOrCreate(['name' => $permissionData['name']], $permissionData);
		}

		$user = User::where('email', 'admin@gmail.com')->first();
		if ($user) {
			$user->givePermissions([
				'accounts_view',
				'accounts_transaction',
				'accounts_banks_view',
				'accounts_withdrawals',
			]);
		}

    }
}