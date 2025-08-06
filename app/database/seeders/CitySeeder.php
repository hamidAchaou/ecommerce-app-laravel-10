<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Array of cities mapped to country codes
        $cities = [
            'USA' => [
                ['name' => 'New York'],
                ['name' => 'Los Angeles'],
                ['name' => 'Chicago'],
            ],
            'CAN' => [
                ['name' => 'Toronto'],
                ['name' => 'Vancouver'],
                ['name' => 'Montreal'],
            ],
            'GBR' => [
                ['name' => 'London'],
                ['name' => 'Manchester'],
                ['name' => 'Birmingham'],
            ],
            'AUS' => [
                ['name' => 'Sydney'],
                ['name' => 'Melbourne'],
                ['name' => 'Brisbane'],
            ],
            'DEU' => [
                ['name' => 'Berlin'],
                ['name' => 'Munich'],
                ['name' => 'Hamburg'],
            ],
        ];

        // Insert cities for each country
        foreach ($cities as $countryCode => $cityList) {
            $country = Country::where('code', $countryCode)->first();
            if ($country) {
                foreach ($cityList as $city) {
                    City::firstOrCreate(
                        [
                            'name' => $city['name'],
                            'country_id' => $country->id,
                        ],
                        [
                            'name' => $city['name'],
                            'country_id' => $country->id,
                        ]
                    );
                }
            }
        }
    }
}