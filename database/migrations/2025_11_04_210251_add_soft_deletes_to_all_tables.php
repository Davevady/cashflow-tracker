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
        // Add soft deletes to transaction_groups
        Schema::table('transaction_groups', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to categories
        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to wallet_groups
        Schema::table('wallet_groups', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to wallets
        Schema::table('wallets', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to wallet_transfers
        Schema::table('wallet_transfers', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to members
        Schema::table('members', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to users (if not exists)
        if (!Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to roles
        Schema::table('roles', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to permissions
        Schema::table('permissions', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove soft deletes from all tables
        Schema::table('transaction_groups', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('wallet_groups', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('wallets', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('wallet_transfers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        if (Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        Schema::table('roles', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
