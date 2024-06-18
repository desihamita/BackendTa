<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $payment_methods = [
            [
                'name' => 'Cash',
                'status' => 1,
                'account_number' => '',
            ], [
                'name' => 'Debet',
                'status' => 1,
                'account_number' => '01719123886',
            ], [
                'name' => 'Go-pay',
                'status' => 1,
                'account_number' => '01719123886',
            ],[
                'name' => 'Ovo',
                'status' => 1,
                'account_number' => '01719123886',
            ], [
                'name' => 'Dana',
                'status' => 1,
                'account_number' => '01719123886',
            ], [
                'name' => 'Qris',
                'status' => 1,
                'account_number' => '01719123886',
            ],
        ];
        (new PaymentMethod())->insert($payment_methods);
    }
}