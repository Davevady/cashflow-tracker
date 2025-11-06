<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWallet extends Model
{
    protected $fillable = [
        'user_id',
        'wallet_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'integer',
    ];

    /**
     * Get the user that owns this wallet balance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get or create user wallet with initial balance 0.
     */
    public static function getOrCreate(int $userId, int $walletId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'wallet_id' => $walletId],
            ['balance' => 0]
        );
    }

    /**
     * Update balance (add or subtract).
     */
    public function updateBalance(int $amount, string $type): void
    {
        if ($type === 'in' || $type === 'income') {
            $this->increment('balance', $amount);
        } else {
            $this->decrement('balance', $amount);
        }
    }
}
