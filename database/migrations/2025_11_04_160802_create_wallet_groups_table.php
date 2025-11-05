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
        Schema::create('wallet_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Kartu Debit, E-Wallet, Cash, dll
            $table->string('icon')->nullable(); // emoji or icon class
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_groups');
    }
};
