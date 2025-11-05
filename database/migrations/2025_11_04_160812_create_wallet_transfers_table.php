<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallet_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_wallet_id')->constrained('wallets')->onDelete('restrict');
            $table->foreignId('to_wallet_id')->constrained('wallets')->onDelete('restrict');
            $table->decimal('amount', 15, 2); // Jumlah transfer
            $table->decimal('fee', 15, 2)->default(0); // Biaya transfer (opsional)
            $table->text('note')->nullable(); // Catatan transfer
            $table->timestamp('transfer_date'); // Tanggal transfer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transfers');
    }
};
