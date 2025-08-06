<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Array of countries with name and ISO 3166-1 alpha-3 code
        $countries = [
            ['name' => 'United States', 'code' => 'USA'],
            ['name' => 'Canada', 'code' => 'CAN'],
            ['name' => 'United Kingdom', 'code' => 'GBR'],
            ['name' => 'Australia', 'code' => 'AUS'],
            ['name' => 'Germany', 'code' => 'DEU'],
        ];

        // Insert countries, checking for duplicates
        foreach ($countries as $country) {
            Country::firstOrCreate(
                ['code' => $country['code']],
                ['name' => $country['name']]
            );
        }
    }
}