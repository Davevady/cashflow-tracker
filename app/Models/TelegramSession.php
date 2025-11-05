<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramSession extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id',
        'state',
        'data',
        'last_activity',
    ];

    protected $casts = [
        'data' => 'array',
        'last_activity' => 'datetime',
        'chat_id' => 'integer',
    ];

    /**
     * Get the user associated with this session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Update session state and data
     */
    public function updateState(string $state, array $data = []): void
    {
        $this->update([
            'state' => $state,
            'data' => array_merge($this->data ?? [], $data),
            'last_activity' => now(),
        ]);
    }

    /**
     * Reset session to idle state
     */
    public function reset(): void
    {
        $this->update([
            'state' => 'idle',
            'data' => null,
            'last_activity' => now(),
        ]);
    }

    /**
     * Get or create session for chat
     */
    public static function getOrCreate(int $chatId): self
    {
        return self::firstOrCreate(
            ['chat_id' => $chatId],
            [
                'state' => 'idle',
                'last_activity' => now(),
            ]
        );
    }
}
