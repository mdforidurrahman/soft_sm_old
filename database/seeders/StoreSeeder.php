<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        Store::factory(200)->create();

        // $totalRecords = 1000000;
        // $batchSize = 5000;

        // $batches = ceil($totalRecords / $batchSize);

        // DB::disableQueryLog();
        // DB::beginTransaction();

        // try {
        //     for ($i = 0; $i < $batches; $i++) {
        //         $data = Store::factory($batchSize)->make()->toArray();
        //         Store::insert($data);

        //         if (($i + 1) % 10 == 0) {
        //             DB::commit();
        //             DB::beginTransaction();
        //         }

        //         $this->command->info('Seeded batch ' . ($i + 1) . ' of ' . $batches);
        //     }

        //     DB::commit();
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     $this->command->error('An error occurred: ' . $e->getMessage());
        // }

        // DB::enableQueryLog();

        $stores = [
            [
                'name' => 'Walmart',
                'address' => '702 SW 8th St, Bentonville, AR 72716, USA',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Target',
                'address' => '1000 Nicollet Mall, Minneapolis, MN 55403, USA',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Costco',
                'address' => '999 Lake Dr, Issaquah, WA 98027, USA',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Best Buy',
                'address' => '7601 Penn Ave S, Richfield, MN 55423, USA',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Amazon Go',
                'address' => '300 Boren Ave N, Seattle, WA 98109, USA',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Home Depot',
                'address' => '2455 Paces Ferry Rd SE, Atlanta, GA 30339, USA',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IKEA',
                'address' => '420 Alan Wood Rd, Conshohocken, PA 19428, USA',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Whole Foods Market',
                'address' => '550 Bowie St, Austin, TX 78703, USA',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Apple Store',
                'address' => '1 Infinite Loop, Cupertino, CA 95014, USA',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sephora',
                'address' => '525 Market St, San Francisco, CA 94105, USA',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('stores')->insert($stores);
    }
}
