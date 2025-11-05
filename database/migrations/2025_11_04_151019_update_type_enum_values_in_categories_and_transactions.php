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
        // First, add new enum values to categories (keeping old ones temporarily)
        DB::statement("ALTER TABLE `categories` MODIFY COLUMN `type` ENUM('income', 'expense', 'in', 'out') NOT NULL");

        // Update categories data: income -> in, expense -> out
        DB::statement("UPDATE `categories` SET `type` = 'in' WHERE `type` = 'income'");
        DB::statement("UPDATE `categories` SET `type` = 'out' WHERE `type` = 'expense'");

        // Remove old enum values, keep only in/out
        DB::statement("ALTER TABLE `categories` MODIFY COLUMN `type` ENUM('in', 'out') NOT NULL DEFAULT 'in'");

        // Add type column to transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('type', ['in', 'out'])->after('category_id')->default('in');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert categories: in -> income, out -> expense
        DB::statement("UPDATE `categories` SET `type` = 'income' WHERE `type` = 'in'");
        DB::statement("UPDATE `categories` SET `type` = 'expense' WHERE `type` = 'out'");
        DB::statement("ALTER TABLE `categories` MODIFY COLUMN `type` ENUM('income', 'expense') NOT NULL");

        // Drop type column from transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
