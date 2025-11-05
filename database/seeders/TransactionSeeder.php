<?php

namespace Database\Seeders;

use App\Models\{TransactionGroup, Category, Wallet, Member, Transaction};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Transaction Groups with type
        $groupPrimer = TransactionGroup::firstOrCreate([
            'name' => 'Primer', 'type' => 'out'
        ], ['description' => 'Kebutuhan primer atau kebutuhan pokok sehari-hari']);

        $groupSekunder = TransactionGroup::firstOrCreate([
            'name' => 'Sekunder', 'type' => 'out'
        ], ['description' => 'Kebutuhan sekunder atau kebutuhan tambahan']);

        $groupHiburan = TransactionGroup::firstOrCreate([
            'name' => 'Hiburan', 'type' => 'out'
        ], ['description' => 'Kebutuhan hiburan dan rekreasi']);

        $groupIncome = TransactionGroup::firstOrCreate([
            'name' => 'Pemasukan', 'type' => 'in'
        ], ['description' => 'Sumber pemasukan']);

        // Create Categories with Font Awesome icons
        $makan = Category::firstOrCreate(['group_id' => $groupPrimer->id, 'name' => 'Makan'], ['icon' => 'fa fa-utensils']);
        $transport = Category::firstOrCreate(['group_id' => $groupPrimer->id, 'name' => 'Transportasi'], ['icon' => 'fa fa-car']);
        $listrik = Category::firstOrCreate(['group_id' => $groupPrimer->id, 'name' => 'Listrik & Air'], ['icon' => 'fa fa-lightbulb']);
        $belanja = Category::firstOrCreate(['group_id' => $groupSekunder->id, 'name' => 'Belanja'], ['icon' => 'fa fa-shopping-bag']);
        $komunikasi = Category::firstOrCreate(['group_id' => $groupSekunder->id, 'name' => 'Komunikasi'], ['icon' => 'fa fa-mobile-alt']);
        $nongkrong = Category::firstOrCreate(['group_id' => $groupHiburan->id, 'name' => 'Nongkrong'], ['icon' => 'fa fa-coffee']);
        $streaming = Category::firstOrCreate(['group_id' => $groupHiburan->id, 'name' => 'Streaming'], ['icon' => 'fa fa-film']);
        $hobi = Category::firstOrCreate(['group_id' => $groupHiburan->id, 'name' => 'Hobi'], ['icon' => 'fa fa-gamepad']);
        $gaji = Category::firstOrCreate(['group_id' => $groupIncome->id, 'name' => 'Gaji'], ['icon' => 'fa fa-money-bill-wave']);
        $freelance = Category::firstOrCreate(['group_id' => $groupIncome->id, 'name' => 'Freelance'], ['icon' => 'fa fa-briefcase']);
        $bonus = Category::firstOrCreate(['group_id' => $groupIncome->id, 'name' => 'Bonus'], ['icon' => 'fa fa-gift']);

        // Sample Transactions tied to wallets & members
        $walletBRI = Wallet::where('name', 'BRI')->first();
        $walletBCA = Wallet::where('name', 'BCA')->first();
        $walletShopee = Wallet::where('name', 'ShopeePay')->first();
        $walletCash = Wallet::where('name', 'Dompet Cash')->first();

        $self = Member::where('name', 'Diri Sendiri')->first();
        $ortu = Member::where('name', 'Orang Tua')->first();

        if ($walletBRI && $walletBCA && $walletShopee && $walletCash && $self && $ortu) {
            DB::transaction(function () use (
                $walletBRI, $walletBCA, $walletShopee, $walletCash,
                $self, $ortu, $gaji, $makan, $belanja, $komunikasi, $nongkrong
            ) {
                // Incomes
                Transaction::create([
                    'category_id' => $gaji->id,
                    'wallet_id' => $walletBRI->id,
                    'member_id' => $self->id,
                    'amount' => 8000000,
                    'date' => now()->startOfMonth()->addDays(1),
                    'note' => 'Gaji bulanan',
                ]);
                Transaction::create([
                    'category_id' => $gaji->id,
                    'wallet_id' => $walletBCA->id,
                    'member_id' => $self->id,
                    'amount' => 2000000,
                    'date' => now()->startOfMonth()->addDays(2),
                    'note' => 'Bonus proyek',
                ]);

                // Expenses
                Transaction::create([
                    'category_id' => $makan->id,
                    'wallet_id' => $walletShopee->id,
                    'member_id' => $self->id,
                    'amount' => 150000,
                    'date' => now()->startOfMonth()->addDays(3),
                    'note' => 'Makan siang',
                ]);
                Transaction::create([
                    'category_id' => $belanja->id,
                    'wallet_id' => $walletBRI->id,
                    'member_id' => $ortu->id,
                    'amount' => 350000,
                    'date' => now()->startOfMonth()->addDays(4),
                    'note' => 'Belanja perabotan untuk orang tua',
                ]);
                Transaction::create([
                    'category_id' => $komunikasi->id,
                    'wallet_id' => $walletBCA->id,
                    'member_id' => $self->id,
                    'amount' => 100000,
                    'date' => now()->startOfMonth()->addDays(5),
                    'note' => 'Pulsa/Kuota',
                ]);
                Transaction::create([
                    'category_id' => $nongkrong->id,
                    'wallet_id' => $walletCash->id,
                    'member_id' => $self->id,
                    'amount' => 80000,
                    'date' => now()->startOfMonth()->addDays(6),
                    'note' => 'Ngopi',
                ]);
            });
        }
    }
}
