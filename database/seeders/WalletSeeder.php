<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{WalletGroup, Wallet};

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        $debit = WalletGroup::where('name', 'Kartu Debit')->first();
        $ewallet = WalletGroup::where('name', 'E-Wallet')->first();
        $cash = WalletGroup::where('name', 'Tunai')->first();

        $wallets = [
            ['wallet_group_id' => $debit->id, 'name' => 'BRI', 'balance' => 0, 'icon' => 'fa fa-university', 'description' => 'Rekening Bank BRI'],
            ['wallet_group_id' => $debit->id, 'name' => 'BCA', 'balance' => 0, 'icon' => 'fa fa-university', 'description' => 'Rekening Bank BCA'],
            ['wallet_group_id' => $ewallet->id, 'name' => 'ShopeePay', 'balance' => 0, 'icon' => 'fa fa-shopping-bag', 'description' => 'Dompet Digital ShopeePay'],
            ['wallet_group_id' => $ewallet->id, 'name' => 'SeaBank', 'balance' => 0, 'icon' => 'fa fa-piggy-bank', 'description' => 'Rekening Digital SeaBank'],
            ['wallet_group_id' => $cash->id, 'name' => 'Dompet Cash', 'balance' => 0, 'icon' => 'fa fa-money-bill-wave', 'description' => 'Uang tunai di dompet'],
        ];

        foreach ($wallets as $w) {
            Wallet::firstOrCreate(
                ['name' => $w['name'], 'wallet_group_id' => $w['wallet_group_id']],
                $w
            );
        }
    }
}


