<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'icon',
        'description',
    ];

    /**
     * Get the wallets under this group.
     */
    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class, 'wallet_group_id');
    }
}


