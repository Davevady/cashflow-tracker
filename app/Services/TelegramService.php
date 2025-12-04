<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $botToken;
    private string $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Send message to Telegram chat
     */
    public function sendMessage(int $chatId, string $text, ?array $replyMarkup = null): array
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->makeRequest('sendMessage', $params);
    }

    /**
     * Edit message text
     */
    public function editMessageText(int $chatId, int $messageId, string $text, ?array $replyMarkup = null): array
    {
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->makeRequest('editMessageText', $params);
    }

    /**
     * Answer callback query
     */
    public function answerCallbackQuery(string $callbackQueryId, ?string $text = null, bool $showAlert = false): array
    {
        $params = [
            'callback_query_id' => $callbackQueryId,
        ];

        if ($text) {
            $params['text'] = $text;
            $params['show_alert'] = $showAlert;
        }

        return $this->makeRequest('answerCallbackQuery', $params);
    }

    /**
     * Get main menu keyboard
     */
    public function getMainMenuKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '� Tambah Transaksi', 'callback_data' => 'add_transaction'],
                ],
                [
                    ['text' => '=� Lihat Saldo', 'callback_data' => 'view_balance'],
                    ['text' => '=� Laporan Hari Ini', 'callback_data' => 'daily_report'],
                ],
                [
                    ['text' => '🚪 Logout', 'callback_data' => 'logout'],
                ],
            ],
        ];
    }

    /**
     * Get transaction type keyboard
     */
    public function getTransactionTypeKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '=� Pemasukan', 'callback_data' => 'type_income'],
                    ['text' => '=� Pengeluaran', 'callback_data' => 'type_expense'],
                ],
                [
                    ['text' => '� Kembali', 'callback_data' => 'back_to_main'],
                ],
            ],
        ];
    }

    /**
     * Get category keyboard
     */
    public function getCategoryKeyboard(string $type): array
    {
        // Map telegram type to database type
        $dbType = $type === 'income' ? 'in' : 'out';

        $categories = \App\Models\Category::whereHas('transactionGroup', function ($q) use ($dbType) {
            $q->where('type', $dbType);
        })->get();

        $keyboard = [];
        $row = [];

        foreach ($categories as $index => $category) {
            $row[] = [
                'text' => $category->name,
                'callback_data' => 'category_' . $category->id,
            ];

            // 2 buttons per row
            if (($index + 1) % 2 === 0) {
                $keyboard[] = $row;
                $row = [];
            }
        }

        // Add remaining buttons
        if (!empty($row)) {
            $keyboard[] = $row;
        }

        // Add back button
        $keyboard[] = [
            ['text' => '� Kembali', 'callback_data' => 'back_to_type'],
        ];

        return ['inline_keyboard' => $keyboard];
    }

    /**
     * Get wallet keyboard
     */
    public function getWalletKeyboard(): array
    {
        $wallets = \App\Models\Wallet::where('is_active', true)->get();

        $keyboard = [];

        foreach ($wallets as $wallet) {
            $keyboard[] = [
                [
                    'text' => $wallet->name . ' (' . number_format($wallet->balance) . ')',
                    'callback_data' => 'wallet_' . $wallet->id,
                ],
            ];
        }

        // Add back button
        $keyboard[] = [
            ['text' => '� Kembali', 'callback_data' => 'back_to_category'],
        ];

        return ['inline_keyboard' => $keyboard];
    }

    /**
     * Get cancel keyboard
     */
    public function getCancelKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => 'L Batal', 'callback_data' => 'cancel'],
                ],
            ],
        ];
    }

    /**
     * Format money in Rupiah
     */
    public function formatMoney(int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Parse money input from text
     */
    public function parseMoney(string $text): ?int
    {
        // Remove all non-numeric characters except minus
        $cleaned = preg_replace('/[^0-9\-]/', '', $text);

        if ($cleaned === '' || $cleaned === '-') {
            return null;
        }

        return (int) $cleaned;
    }

    /**
     * Make HTTP request to Telegram API
     */
    private function makeRequest(string $method, array $params = []): array
    {
        try {
            $response = Http::timeout(30)
                ->post("{$this->apiUrl}/{$method}", $params);

            $result = $response->json();

            if (!$result['ok'] ?? false) {
                Log::error('Telegram API Error', [
                    'method' => $method,
                    'params' => $params,
                    'response' => $result,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Telegram Request Failed', [
                'method' => $method,
                'error' => $e->getMessage(),
            ]);

            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Set webhook URL
     */
    public function setWebhook(string $url): array
    {
        return $this->makeRequest('setWebhook', [
            'url' => $url,
            'allowed_updates' => ['message', 'callback_query'],
        ]);
    }

    /**
     * Get webhook info
     */
    public function getWebhookInfo(): array
    {
        return $this->makeRequest('getWebhookInfo');
    }

    /**
     * Delete webhook
     */
    public function deleteWebhook(): array
    {
        return $this->makeRequest('deleteWebhook');
    }
}
