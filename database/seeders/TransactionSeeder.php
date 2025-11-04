<?php

namespace Database\Seeders;

use App\Models\TransactionGroup;
use App\Models\Category;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Transaction Groups
        $groupPrimer = TransactionGroup::create([
            'name' => 'Primer',
            'description' => 'Kebutuhan primer atau kebutuhan pokok sehari-hari',
        ]);

        $groupSekunder = TransactionGroup::create([
            'name' => 'Sekunder',
            'description' => 'Kebutuhan sekunder atau kebutuhan tambahan',
        ]);

        $groupHiburan = TransactionGroup::create([
            'name' => 'Hiburan',
            'description' => 'Kebutuhan hiburan dan rekreasi',
        ]);

        $groupIncome = TransactionGroup::create([
            'name' => 'Pemasukan',
            'description' => 'Sumber pemasukan',
        ]);

        // Create Categories for Expense - Primer
        Category::create([
            'group_id' => $groupPrimer->id,
            'name' => 'Makan',
            'type' => 'expense',
            'icon' => '🍽️',
        ]);

        Category::create([
            'group_id' => $groupPrimer->id,
            'name' => 'Transportasi',
            'type' => 'expense',
            'icon' => '🚗',
        ]);

        Category::create([
            'group_id' => $groupPrimer->id,
            'name' => 'Listrik & Air',
            'type' => 'expense',
            'icon' => '💡',
        ]);

        // Create Categories for Expense - Sekunder
        Category::create([
            'group_id' => $groupSekunder->id,
            'name' => 'Belanja',
            'type' => 'expense',
            'icon' => '🛍️',
        ]);

        Category::create([
            'group_id' => $groupSekunder->id,
            'name' => 'Komunikasi',
            'type' => 'expense',
            'icon' => '📱',
        ]);

        // Create Categories for Expense - Hiburan
        Category::create([
            'group_id' => $groupHiburan->id,
            'name' => 'Nongkrong',
            'type' => 'expense',
            'icon' => '☕',
        ]);

        Category::create([
            'group_id' => $groupHiburan->id,
            'name' => 'Streaming',
            'type' => 'expense',
            'icon' => '🎬',
        ]);

        Category::create([
            'group_id' => $groupHiburan->id,
            'name' => 'Hobi',
            'type' => 'expense',
            'icon' => '🎮',
        ]);

        // Create Categories for Income
        Category::create([
            'group_id' => $groupIncome->id,
            'name' => 'Gaji',
            'type' => 'income',
            'icon' => '💰',
        ]);

        Category::create([
            'group_id' => $groupIncome->id,
            'name' => 'Freelance',
            'type' => 'income',
            'icon' => '💼',
        ]);

        Category::create([
            'group_id' => $groupIncome->id,
            'name' => 'Bonus',
            'type' => 'income',
            'icon' => '🎁',
        ]);
    }
}
