<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletTransfer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'from_wallet_id',
        'to_wallet_id',
        'amount',
        'fee',
        'note',
        'transfer_date',
    ];

    protected $casts = [
        'amount' => 'integer',
        'fee' => 'integer',
        'transfer_date' => 'datetime',
    ];

    /**
     * Wallet the transfer originates from.
     */
    public function fromWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'from_wallet_id');
    }

    /**
     * Wallet the transfer goes to.
     */
    public function toWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'to_wallet_id');
    }
}


