<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            ['code' => 'A1', 'name' => 'Area A - Rak 1', 'zone' => 'A', 'aisle' => '1', 'rack' => '1'],
            ['code' => 'A2', 'name' => 'Area A - Rak 2', 'zone' => 'A', 'aisle' => '1', 'rack' => '2'],
            ['code' => 'B1', 'name' => 'Area B - Rak 1', 'zone' => 'B', 'aisle' => '2', 'rack' => '1'],
            ['code' => 'B2', 'name' => 'Area B - Rak 2', 'zone' => 'B', 'aisle' => '2', 'rack' => '2'],
            ['code' => 'C1', 'name' => 'Area C - Rak 1', 'zone' => 'C', 'aisle' => '3', 'rack' => '1'],
            ['code' => 'C2', 'name' => 'Area C - Rak 2', 'zone' => 'C', 'aisle' => '3', 'rack' => '2'],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
