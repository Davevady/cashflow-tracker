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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_group_id')->constrained('wallet_groups')->onDelete('restrict');
            $table->string('name'); // BRI, BCA, ShopeePay, Seabank, Cash, dll
            $table->string('account_number')->nullable(); // Nomor rekening/akun (opsional)
            $table->decimal('balance', 15, 2)->default(0); // Saldo dompet
            $table->string('icon')->nullable(); // emoji or icon class
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true); // Aktif/Nonaktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
