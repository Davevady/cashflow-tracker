<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramSession extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id',
        'is_authenticated',
        'state',
        'data',
        'last_activity',
    ];

    protected $casts = [
        'data' => 'array',
        'last_activity' => 'datetime',
        'chat_id' => 'integer',
        'is_authenticated' => 'boolean',
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
                'is_authenticated' => false,
                'last_activity' => now(),
            ]
        );
    }

    /**
     * Authenticate user with email and password
     */
    public function authenticate(string $email, string $password): bool
    {
        $user = User::where('email', $email)->first();

        if ($user && \Hash::check($password, $user->password)) {
            $this->update([
                'user_id' => $user->id,
                'is_authenticated' => true,
                'state' => 'idle',
                'data' => null,
                'last_activity' => now(),
            ]);
            return true;
        }

        return false;
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        $this->update([
            'user_id' => null,
            'is_authenticated' => false,
            'state' => 'idle',
            'data' => null,
            'last_activity' => now(),
        ]);
    }

    /**
     * Check if session is authenticated
     */
    public function isAuthenticated(): bool
    {
        // Check if authenticated and not expired (30 days)
        if ($this->is_authenticated && $this->user_id) {
            $daysSinceActivity = now()->diffInDays($this->last_activity);
            if ($daysSinceActivity > 30) {
                $this->logout();
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Scope for authenticated sessions only
     */
    public function scopeAuthenticated($query)
    {
        return $query->where('is_authenticated', true)->whereNotNull('user_id');
    }
}
