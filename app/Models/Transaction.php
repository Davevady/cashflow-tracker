<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'category_id',
        'amount',
        'date',
        'note',
    ];

    protected $casts = [
        'date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the category that owns the transaction.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
