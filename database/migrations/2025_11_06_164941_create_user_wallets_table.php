<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0); // Balance per user per wallet
            $table->timestamps();

            // Unique constraint: satu user hanya bisa punya satu record per wallet
            $table->unique(['user_id', 'wallet_id']);

            $table->index('user_id');
            $table->index('wallet_id');
        });

        // Migrate existing wallet balances to user_wallets for the first user
        $firstUserId = DB::table('users')->orderBy('id')->value('id');
        if ($firstUserId) {
            $wallets = DB::table('wallets')->get();
            foreach ($wallets as $wallet) {
                DB::table('user_wallets')->insert([
                    'user_id' => $firstUserId,
                    'wallet_id' => $wallet->id,
                    'balance' => $wallet->balance,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_wallets');
    }
};
