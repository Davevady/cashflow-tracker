<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get all categories for this transaction group.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'group_id');
    }
}
