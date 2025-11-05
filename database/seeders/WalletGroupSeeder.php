<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WalletGroup;

class WalletGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['name' => 'Kartu Debit', 'icon' => 'fa fa-credit-card', 'description' => 'Kartu debit dari bank'],
            ['name' => 'E-Wallet', 'icon' => 'fa fa-mobile-alt', 'description' => 'Dompet digital'],
            ['name' => 'Tunai', 'icon' => 'fa fa-money-bill-wave', 'description' => 'Uang tunai'],
        ];

        foreach ($groups as $g) {
            WalletGroup::firstOrCreate(['name' => $g['name']], $g);
        }
    }
}


