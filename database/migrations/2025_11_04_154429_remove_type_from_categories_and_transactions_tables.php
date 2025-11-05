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
        // Remove type column from categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        // Remove type column from transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore type column to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('type', ['in', 'out'])->after('group_id')->default('in');
        });

        // Restore type column to transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('type', ['in', 'out'])->after('category_id')->default('in');
        });
    }
};
