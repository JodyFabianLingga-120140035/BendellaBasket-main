<?php

namespace Database\Seeders;

use App\Models\BasketCourt;
use Illuminate\Database\Seeder;

class BasketCourtSeeder extends Seeder
{
    public function run(): void
    {
        BasketCourt::create([
            'name' => 'Lapangan A',
            'location' => 'Lantai 1, Gedung Olahraga',
            'description' => 'Lapangan basket indoor dengan lantai vinyl',
            'price_per_hour' => 100000,
            'is_available' => true,
            'photo' => 'courts/court-a.jpg'
        ]);

        BasketCourt::create([
            'name' => 'Lapangan B',
            'location' => 'Lantai 1, Gedung Olahraga',
            'description' => 'Lapangan basket indoor dengan lantai kayu',
            'price_per_hour' => 150000,
            'is_available' => true,
            'photo' => 'courts/court-b.jpg'
        ]);
    }
}
