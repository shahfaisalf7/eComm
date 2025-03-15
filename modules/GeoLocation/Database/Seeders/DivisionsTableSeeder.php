<?php

namespace Modules\GeoLocation\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions_json = json_decode('{
            "divisions": [
                {
                "id": "1",
                "name": "Barishal",
                "bn_name": "বরিশাল",
                "lat": "22.701002",
                "long": "90.353451"
                },
                {
                "id": "2",
                "name": "Chattogram",
                "bn_name": "চট্টগ্রাম",
                "lat": "22.356851",
                "long": "91.783182"
                },
                {
                "id": "3",
                "name": "Dhaka",
                "bn_name": "ঢাকা",
                "lat": "23.810332",
                "long": "90.412518"
                },
                {
                "id": "4",
                "name": "Khulna",
                "bn_name": "খুলনা",
                "lat": "22.845641",
                "long": "89.540328"
                },
                {
                "id": "5",
                "name": "Rajshahi",
                "bn_name": "রাজশাহী",
                "lat": "24.363589",
                "long": "88.624135"
                },
                {
                "id": "6",
                "name": "Rangpur",
                "bn_name": "রংপুর",
                "lat": "25.743892",
                "long": "89.275227"
                },
                {
                "id": "7",
                "name": "Sylhet",
                "bn_name": "সিলেট",
                "lat": "24.894929",
                "long": "91.868706"
                },
                {
                "id": "8",
                "name": "Mymensingh",
                "bn_name": "ময়মনসিংহ",
                "lat": "24.747149",
                "long": "90.420273"
                }
            ]
            }');

        $divisions = [];
        foreach ($divisions_json->divisions as $division) {
            $divisions[] = [
                'id' => $division->id,
                'name' => $division->name,
                'description' => '',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('divisions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table('divisions')->insert($divisions);
    }
}
