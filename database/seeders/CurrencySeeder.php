<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
            [
                'name' => 'BRL',
                'usd_value' => 5.30,
            ],
            [
                'name' => 'EUR',
                'usd_value' => 0.82,
            ],
            [
                'name' => 'BTC',
                'usd_value' => 0.26,
            ],
            [
                'name' => 'ETH',
                'usd_value' => 0.37,
            ],
            [
                'name' => 'GTA$',
                'usd_value' => 2642.22,
            ]
        ];

        foreach ($currencies as $currency) {
            Currency::create([
                'name' => $currency['name'],
                'usd_value' => $currency['usd_value'],
            ]);
        }
    }
}
