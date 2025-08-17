<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        PaymentMethod::create([
            'name' => 'BCA Transfer',
            'account_number' => '1234567890',
            'account_name' => 'PT Basket Court',
            'is_active' => true
        ]);

        PaymentMethod::create([
            'name' => 'Mandiri Transfer',
            'account_number' => '0987654321',
            'account_name' => 'PT Basket Court',
            'is_active' => true
        ]);

        PaymentMethod::create([
            'name' => 'QRIS',
            'account_number' => null,
            'account_name' => 'PT Basket Court',
            'is_active' => true
        ]);
    }
}
