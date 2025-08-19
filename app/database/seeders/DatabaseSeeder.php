<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\Admin\CategorySeeder;
use Database\Seeders\Admin\ProductSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            CountrySeeder::class,
            CitySeeder::class,
            CategorySeeder::class,
            \Database\Seeders\Admin\ProductSeeder::class,
        ]);
    }
}
