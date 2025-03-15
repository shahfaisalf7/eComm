<?php

namespace Modules\GeoLocation\Database\Seeders;

use Illuminate\Database\Seeder;

class GeoLocationDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(DivisionsTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        $this->call(ZonesTableSeeder::class);
    }
}
