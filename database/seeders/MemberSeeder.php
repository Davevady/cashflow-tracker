<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['name' => 'Diri Sendiri', 'icon' => 'fa fa-user', 'description' => 'Transaksi pribadi'],
            ['name' => 'Orang Tua', 'icon' => 'fa fa-users', 'description' => 'Transaksi untuk orang tua'],
            ['name' => 'Pasangan', 'icon' => 'fa fa-heart', 'description' => 'Transaksi untuk pasangan'],
            ['name' => 'Anak', 'icon' => 'fa fa-child', 'description' => 'Transaksi untuk anak'],
        ];

        foreach ($members as $m) {
            Member::firstOrCreate(['name' => $m['name']], $m);
        }
    }
}


