<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'group_id',
        'name',
        'type',
        'icon',
    ];

    /**
     * Get the transaction group that owns the category.
     */
    public function transactionGroup(): BelongsTo
    {
        return $this->belongsTo(TransactionGroup::class, 'group_id');
    }

    /**
     * Get all transactions for this category.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }
}
