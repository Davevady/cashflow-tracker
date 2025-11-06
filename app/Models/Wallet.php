<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'wallet_group_id',
        'name',
        'account_number',
        'balance',
        'icon',
        'description',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Group of the wallet.
     */
    public function walletGroup(): BelongsTo
    {
        return $this->belongsTo(WalletGroup::class, 'wallet_group_id');
    }

    /**
     * Transactions associated with this wallet.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'wallet_id');
    }

    /**
     * Outgoing transfers from this wallet.
     */
    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(WalletTransfer::class, 'from_wallet_id');
    }

    /**
     * Incoming transfers to this wallet.
     */
    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(WalletTransfer::class, 'to_wallet_id');
    }

    /**
     * Users who have this wallet (with balance).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_wallets')
            ->withPivot('balance')
            ->withTimestamps();
    }

    /**
     * Get user's balance for this wallet.
     */
    public function getBalanceForUser(int $userId): int
    {
        $userWallet = UserWallet::where('user_id', $userId)
            ->where('wallet_id', $this->id)
            ->first();

        return $userWallet ? $userWallet->balance : 0;
    }
}


