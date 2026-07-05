<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Amenity;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Jacuzzi', 'description' => 'Private jacuzzi access', 'price' => 1000.00, 'price_type' => 'flat'],
            ['name' => 'Heater', 'description' => 'Water/room heater setup', 'price' => 800.00, 'price_type' => 'flat'],
            ['name' => 'LPG (11 hrs)', 'description' => 'Gas tank cooking access', 'price' => 300.00, 'price_type' => 'flat'],
            ['name' => 'LPG (21-22 hrs)', 'description' => 'Extended cooking gas access', 'price' => 700.00, 'price_type' => 'flat'],
            ['name' => 'Small Pet Fee', 'description' => 'Bringing a small pet along', 'price' => 300.00, 'price_type' => 'flat'],
            ['name' => 'Big Pet Fee', 'description' => 'Bringing a large pet along', 'price' => 500.00, 'price_type' => 'flat'],
        ];

        foreach ($items as $item) {
            Amenity::updateOrCreate(['name' => $item['name']], $item);
        }
    }
}