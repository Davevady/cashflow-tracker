<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\TelegramSession;
use App\Models\UserWallet;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    protected TelegramService $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Handle incoming webhook from Telegram
     */
    public function webhook(Request $request)
    {
        try {
            $update = $request->all();
            Log::info('Telegram Webhook', ['update' => $update]);

            // Handle callback query (button clicks)
            if (isset($update['callback_query'])) {
                $this->handleCallbackQuery($update['callback_query']);
                return response()->json(['ok' => true]);
            }

            // Handle text messages
            if (isset($update['message']['text'])) {
                $this->handleMessage($update['message']);
                return response()->json(['ok' => true]);
            }

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            Log::error('Telegram Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle text messages
     */
    protected function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'];
        $session = TelegramSession::getOrCreate($chatId);

        // Handle /start command
        if ($text === '/start') {
            $this->showWelcome($chatId, $session);
            return;
        }

        // Handle /logout command
        if ($text === '/logout') {
            if ($session->isAuthenticated()) {
                $session->logout();
                $this->telegram->sendMessage(
                    $chatId,
                    "✅ Anda berhasil logout.\n\nGunakan /start untuk login kembali."
                );
            } else {
                $this->telegram->sendMessage(
                    $chatId,
                    "❌ Anda belum login.\n\nGunakan /start untuk memulai."
                );
            }
            return;
        }

        // Handle /cancel command
        if ($text === '/cancel') {
            $session->reset();
            $this->telegram->sendMessage(
                $chatId,
                "❌ Proses dibatalkan.\n\nSilakan pilih menu:"
            );
            if ($session->isAuthenticated()) {
                $this->showMainMenu($chatId, $session);
            } else {
                $this->showWelcome($chatId, $session);
            }
            return;
        }

        // Check if user is authenticated for most operations
        if (!$session->isAuthenticated()) {
            // Handle login flow
            match ($session->state) {
                'waiting_email' => $this->handleEmailInput($chatId, $text, $session),
                'waiting_password' => $this->handlePasswordInput($chatId, $text, $session),
                default => $this->showWelcome($chatId, $session),
            };
            return;
        }

        // Handle authenticated user operations
        match ($session->state) {
            'waiting_amount' => $this->handleAmountInput($chatId, $text, $session),
            'waiting_date' => $this->handleDateInput($chatId, $text, $session),
            'waiting_note' => $this->handleNoteInput($chatId, $text, $session),
            default => $this->showMainMenu($chatId, $session),
        };
    }

    /**
     * Handle callback query (button clicks)
     */
    protected function handleCallbackQuery(array $callbackQuery): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $messageId = $callbackQuery['message']['message_id'];
        $data = $callbackQuery['data'];
        $queryId = $callbackQuery['id'];
        $session = TelegramSession::getOrCreate($chatId);

        // Answer callback query to remove loading state
        $this->telegram->answerCallbackQuery($queryId);

        // Check authentication for non-login callbacks
        if (!$session->isAuthenticated() && $data !== 'start_login') {
            $this->telegram->editMessageText(
                $chatId,
                $messageId,
                "⚠️ Sesi Anda telah berakhir atau belum login.\n\nGunakan /start untuk login kembali."
            );
            return;
        }

        // Handle login callback
        if ($data === 'start_login') {
            $session->updateState('waiting_email');
            $this->telegram->editMessageText(
                $chatId,
                $messageId,
                "🔐 <b>Login ke CashFlow Tracker</b>\n\n📧 Silakan masukkan email Anda:"
            );
            return;
        }

        // Handle callback data
        if ($data === 'add_transaction') {
            $this->showTransactionTypeSelection($chatId, $messageId, $session);
        } elseif ($data === 'view_balance') {
            $this->showBalance($chatId, $session);
        } elseif ($data === 'daily_report') {
            $this->showDailyReport($chatId, $session);
        } elseif (str_starts_with($data, 'type_')) {
            $type = str_replace('type_', '', $data);
            $this->showCategorySelection($chatId, $messageId, $type, $session);
        } elseif (str_starts_with($data, 'category_')) {
            $categoryId = str_replace('category_', '', $data);
            $this->showWalletSelection($chatId, $messageId, $categoryId, $session);
        } elseif (str_starts_with($data, 'wallet_')) {
            $walletId = str_replace('wallet_', '', $data);
            $this->showMemberSelection($chatId, $messageId, $walletId, $session);
        } elseif (str_starts_with($data, 'member_')) {
            $memberId = str_replace('member_', '', $data);
            $this->askForAmount($chatId, $messageId, $memberId, $session);
        } elseif (str_starts_with($data, 'date_')) {
            $dateOption = str_replace('date_', '', $data);
            $this->handleDateSelection($chatId, $messageId, $dateOption, $session);
        } elseif ($data === 'logout') {
            $session->logout();
            $this->telegram->editMessageText(
                $chatId,
                $messageId,
                "✅ Anda berhasil logout.\n\nGunakan /start untuk login kembali."
            );
        } elseif ($data === 'back_to_main') {
            $this->showMainMenu($chatId, $session, $messageId);
        } elseif ($data === 'back_to_type') {
            $this->showTransactionTypeSelection($chatId, $messageId, $session);
        } elseif ($data === 'back_to_category') {
            $type = $session->data['type'] ?? 'in';
            $this->showCategorySelection($chatId, $messageId, $type, $session);
        } elseif ($data === 'back_to_wallet') {
            $categoryId = $session->data['category_id'] ?? null;
            if ($categoryId) {
                $this->showWalletSelection($chatId, $messageId, $categoryId, $session);
            }
        } elseif ($data === 'back_to_member') {
            $walletId = $session->data['wallet_id'] ?? null;
            if ($walletId) {
                $this->showMemberSelection($chatId, $messageId, $walletId, $session);
            }
        } elseif ($data === 'cancel') {
            $session->reset();
            $this->telegram->editMessageText(
                $chatId,
                $messageId,
                "❌ Proses dibatalkan.\n\nSilakan pilih menu:"
            );
            $this->showMainMenu($chatId, $session);
        }
    }

    /**
     * Show main menu
     */
    protected function showMainMenu(int $chatId, TelegramSession $session, ?int $messageId = null): void
    {
        $session->reset();

        $text = "👋 <b>Selamat datang di CashFlow Tracker Bot!</b>\n\n" .
            "Silakan pilih menu di bawah ini:";

        $keyboard = $this->telegram->getMainMenuKeyboard();

        if ($messageId) {
            $this->telegram->editMessageText($chatId, $messageId, $text, $keyboard);
        } else {
            $this->telegram->sendMessage($chatId, $text, $keyboard);
        }
    }

    /**
     * Show transaction type selection
     */
    protected function showTransactionTypeSelection(int $chatId, int $messageId, TelegramSession $session): void
    {
        $session->updateState('selecting_type');

        $text = "💰 <b>Tambah Transaksi</b>\n\n" .
            "Silakan pilih jenis transaksi:";

        $keyboard = $this->telegram->getTransactionTypeKeyboard();

        $this->telegram->editMessageText($chatId, $messageId, $text, $keyboard);
    }

    /**
     * Show category selection
     */
    protected function showCategorySelection(int $chatId, int $messageId, string $type, TelegramSession $session): void
    {
        $session->updateState('selecting_category', ['type' => $type]);

        $typeLabel = $type === 'income' ? '📥 Pemasukan' : '📤 Pengeluaran';
        $text = "<b>$typeLabel</b>\n\n" .
            "Silakan pilih kategori:";

        $keyboard = $this->telegram->getCategoryKeyboard($type);

        $this->telegram->editMessageText($chatId, $messageId, $text, $keyboard);
    }

    /**
     * Show wallet selection
     */
    protected function showWalletSelection(int $chatId, int $messageId, int $categoryId, TelegramSession $session): void
    {
        $category = Category::find($categoryId);

        if (!$category) {
            $this->telegram->sendMessage($chatId, "❌ Kategori tidak ditemukan.");
            $this->showMainMenu($chatId, $session);
            return;
        }

        $session->updateState('selecting_wallet', ['category_id' => $categoryId]);

        $text = "<b>Kategori: {$category->name}</b>\n\n" .
            "Silakan pilih dompet/rekening:";

        $keyboard = $this->telegram->getWalletKeyboard();

        $this->telegram->editMessageText($chatId, $messageId, $text, $keyboard);
    }

    /**
     * Show member selection
     */
    protected function showMemberSelection(int $chatId, int $messageId, int $walletId, TelegramSession $session): void
    {
        $wallet = Wallet::find($walletId);

        if (!$wallet) {
            $this->telegram->sendMessage($chatId, "❌ Dompet tidak ditemukan.");
            $this->showMainMenu($chatId, $session);
            return;
        }

        $session->updateState('selecting_member', ['wallet_id' => $walletId]);

        $category = Category::find($session->data['category_id']);
        $typeLabel = $session->data['type'] === 'income' ? '📥 Pemasukan' : '📤 Pengeluaran';

        $text = "<b>$typeLabel</b>\n" .
            "Kategori: {$category->name}\n" .
            "Dompet: {$wallet->name}\n\n" .
            "👤 <b>Pilih Member:</b>\n" .
            "<i>Untuk siapa transaksi ini?</i>";

        $members = Member::orderBy('name')->get();
        $buttons = [];

        foreach ($members as $member) {
            $buttons[] = [
                ['text' => $member->name, 'callback_data' => 'member_' . $member->id]
            ];
        }

        // Add back button
        $buttons[] = [
            ['text' => '🔙 Kembali', 'callback_data' => 'back_to_wallet'],
            ['text' => '❌ Batal', 'callback_data' => 'cancel']
        ];

        $keyboard = ['inline_keyboard' => $buttons];

        $this->telegram->editMessageText($chatId, $messageId, $text, $keyboard);
    }

    /**
     * Ask for transaction amount
     */
    protected function askForAmount(int $chatId, int $messageId, int $memberId, TelegramSession $session): void
    {
        $member = Member::find($memberId);

        if (!$member) {
            $this->telegram->sendMessage($chatId, "❌ Member tidak ditemukan.");
            $this->showMainMenu($chatId, $session);
            return;
        }

        $session->updateState('waiting_amount', ['member_id' => $memberId]);

        $category = Category::find($session->data['category_id']);
        $wallet = Wallet::find($session->data['wallet_id']);
        $typeLabel = $session->data['type'] === 'income' ? '📥 Pemasukan' : '📤 Pengeluaran';

        $text = "<b>$typeLabel</b>\n" .
            "Kategori: {$category->name}\n" .
            "Dompet: {$wallet->name}\n" .
            "Member: {$member->name}\n\n" .
            "💵 <b>Masukkan nominal transaksi:</b>\n" .
            "<i>Contoh: 50000 atau 50.000</i>";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🔙 Kembali', 'callback_data' => 'back_to_member'],
                    ['text' => '❌ Batal', 'callback_data' => 'cancel']
                ]
            ]
        ];

        $this->telegram->editMessageText($chatId, $messageId, $text, $keyboard);
    }

    /**
     * Handle amount input
     */
    protected function handleAmountInput(int $chatId, string $text, TelegramSession $session): void
    {
        $amount = $this->telegram->parseMoney($text);

        if (!$amount || $amount <= 0) {
            $this->telegram->sendMessage(
                $chatId,
                "❌ Nominal tidak valid. Silakan masukkan angka yang benar.\n\n" .
                "<i>Contoh: 50000 atau 50.000</i>",
                $this->telegram->getCancelKeyboard()
            );
            return;
        }

        $session->updateState('selecting_date', ['amount' => $amount]);

        $category = Category::find($session->data['category_id']);
        $wallet = Wallet::find($session->data['wallet_id']);
        $member = Member::find($session->data['member_id']);
        $typeLabel = $session->data['type'] === 'income' ? '📥 Pemasukan' : '📤 Pengeluaran';

        $text = "<b>$typeLabel</b>\n" .
            "Kategori: {$category->name}\n" .
            "Dompet: {$wallet->name}\n" .
            "Member: {$member->name}\n" .
            "Nominal: " . $this->telegram->formatMoney($amount) . "\n\n" .
            "📅 <b>Pilih tanggal transaksi:</b>";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '📅 Hari Ini (' . now()->format('d/m/Y') . ')', 'callback_data' => 'date_today'],
                ],
                [
                    ['text' => '📝 Input Manual', 'callback_data' => 'date_manual'],
                ],
                [
                    ['text' => '🔙 Kembali', 'callback_data' => 'back_to_member'],
                    ['text' => '❌ Batal', 'callback_data' => 'cancel']
                ]
            ]
        ];

        $this->telegram->sendMessage($chatId, $text, $keyboard);
    }

    /**
     * Handle date selection (today or manual)
     */
    protected function handleDateSelection(int $chatId, int $messageId, string $dateOption, TelegramSession $session): void
    {
        if ($dateOption === 'today') {
            // Set date to today and ask for note
            $session->updateState('waiting_note', ['date' => now()->format('Y-m-d H:i:s')]);

            $category = Category::find($session->data['category_id']);
            $wallet = Wallet::find($session->data['wallet_id']);
            $member = Member::find($session->data['member_id']);
            $amount = $session->data['amount'];
            $typeLabel = $session->data['type'] === 'income' ? '📥 Pemasukan' : '📤 Pengeluaran';

            $text = "<b>$typeLabel</b>\n" .
                "Kategori: {$category->name}\n" .
                "Dompet: {$wallet->name}\n" .
                "Member: {$member->name}\n" .
                "Nominal: " . $this->telegram->formatMoney($amount) . "\n" .
                "Tanggal: " . now()->format('d/m/Y H:i') . "\n\n" .
                "📝 <b>Masukkan keterangan transaksi:</b>\n" .
                "<i>Contoh: Beli makan siang</i>";

            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '❌ Batal', 'callback_data' => 'cancel']
                    ]
                ]
            ];

            $this->telegram->editMessageText($chatId, $messageId, $text, $keyboard);
        } else {
            // Ask for manual date input
            $session->updateState('waiting_date');

            $category = Category::find($session->data['category_id']);
            $wallet = Wallet::find($session->data['wallet_id']);
            $member = Member::find($session->data['member_id']);
            $amount = $session->data['amount'];
            $typeLabel = $session->data['type'] === 'income' ? '📥 Pemasukan' : '📤 Pengeluaran';

            $text = "<b>$typeLabel</b>\n" .
                "Kategori: {$category->name}\n" .
                "Dompet: {$wallet->name}\n" .
                "Member: {$member->name}\n" .
                "Nominal: " . $this->telegram->formatMoney($amount) . "\n\n" .
                "📅 <b>Masukkan tanggal dan waktu transaksi:</b>\n" .
                "<i>Format: DD/MM/YYYY HH:MM</i>\n" .
                "<i>Contoh: 15/11/2025 14:30</i>";

            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '❌ Batal', 'callback_data' => 'cancel']
                    ]
                ]
            ];

            $this->telegram->editMessageText($chatId, $messageId, $text, $keyboard);
        }
    }

    /**
     * Handle manual date input
     */
    protected function handleDateInput(int $chatId, string $text, TelegramSession $session): void
    {
        // Parse date format DD/MM/YYYY HH:MM
        $datePattern = '/^(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})$/';

        if (!preg_match($datePattern, $text, $matches)) {
            $this->telegram->sendMessage(
                $chatId,
                "❌ Format tanggal tidak valid.\n\n" .
                "Silakan masukkan dengan format: DD/MM/YYYY HH:MM\n" .
                "<i>Contoh: 15/11/2025 14:30</i>"
            );
            return;
        }

        $day = $matches[1];
        $month = $matches[2];
        $year = $matches[3];
        $hour = $matches[4];
        $minute = $matches[5];

        // Validate date
        if (!checkdate($month, $day, $year) || $hour > 23 || $minute > 59) {
            $this->telegram->sendMessage(
                $chatId,
                "❌ Tanggal atau waktu tidak valid.\n\n" .
                "Silakan masukkan tanggal dan waktu yang benar.\n" .
                "<i>Format: DD/MM/YYYY HH:MM</i>"
            );
            return;
        }

        $date = "$year-$month-$day $hour:$minute:00";

        $session->updateState('waiting_note', ['date' => $date]);

        $category = Category::find($session->data['category_id']);
        $wallet = Wallet::find($session->data['wallet_id']);
        $member = Member::find($session->data['member_id']);
        $amount = $session->data['amount'];
        $typeLabel = $session->data['type'] === 'income' ? '📥 Pemasukan' : '📤 Pengeluaran';

        $text = "<b>$typeLabel</b>\n" .
            "Kategori: {$category->name}\n" .
            "Dompet: {$wallet->name}\n" .
            "Member: {$member->name}\n" .
            "Nominal: " . $this->telegram->formatMoney($amount) . "\n" .
            "Tanggal: " . \Carbon\Carbon::parse($date)->format('d/m/Y H:i') . "\n\n" .
            "📝 <b>Masukkan keterangan transaksi:</b>\n" .
            "<i>Contoh: Beli makan siang</i>";

        $this->telegram->sendMessage($chatId, $text);
    }

    /**
     * Handle note input and save transaction
     */
    protected function handleNoteInput(int $chatId, string $text, TelegramSession $session): void
    {
        $note = trim($text);

        if (empty($note)) {
            $this->telegram->sendMessage(
                $chatId,
                "❌ Keterangan tidak boleh kosong. Silakan masukkan keterangan.",
                $this->telegram->getCancelKeyboard()
            );
            return;
        }

        try {
            DB::beginTransaction();

            $category = Category::find($session->data['category_id']);
            $wallet = Wallet::find($session->data['wallet_id']);
            $member = Member::find($session->data['member_id']);
            $amount = $session->data['amount'];
            $type = $session->data['type'];
            $date = $session->data['date'] ?? now();

            // Create transaction
            Transaction::create([
                'category_id' => $category->id,
                'wallet_id' => $wallet->id,
                'member_id' => $member->id,
                'amount' => $amount,
                'note' => $note,
                'date' => $date,
                'user_id' => $session->user_id,
            ]);

            // Update user wallet balance
            $userWallet = UserWallet::getOrCreate($session->user_id, $wallet->id);
            $userWallet->updateBalance($amount, $type);

            DB::commit();

            $typeLabel = $type === 'income' ? '📥 Pemasukan' : '📤 Pengeluaran';
            $typeIcon = $type === 'income' ? '✅' : '💸';

            // Get updated user wallet balance
            $userWalletBalance = UserWallet::where('user_id', $session->user_id)
                ->where('wallet_id', $wallet->id)
                ->first();

            $successMessage = "$typeIcon <b>Transaksi Berhasil Disimpan!</b>\n\n" .
                "Jenis: $typeLabel\n" .
                "Kategori: {$category->name}\n" .
                "Dompet: {$wallet->name}\n" .
                "Member: {$member->name}\n" .
                "Nominal: " . $this->telegram->formatMoney($amount) . "\n" .
                "Keterangan: $note\n" .
                "Tanggal: " . \Carbon\Carbon::parse($date)->format('d/m/Y H:i') . "\n\n" .
                "Saldo {$wallet->name}: " . $this->telegram->formatMoney($userWalletBalance->balance ?? 0);

            $this->telegram->sendMessage($chatId, $successMessage);

            // Reset session and show main menu
            $this->showMainMenu($chatId, $session);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Transaction Save Error', [
                'error' => $e->getMessage(),
                'session_data' => $session->data,
            ]);

            $this->telegram->sendMessage(
                $chatId,
                "❌ Terjadi kesalahan saat menyimpan transaksi.\n\n" .
                "Error: " . $e->getMessage()
            );

            $this->showMainMenu($chatId, $session);
        }
    }

    /**
     * Show wallet balances
     */
    protected function showBalance(int $chatId, TelegramSession $session): void
    {
        $userId = $session->user_id;

        // Get user wallets directly from user_wallets table
        $userWallets = UserWallet::where('user_id', $userId)
            ->with('wallet')
            ->whereHas('wallet', function ($q) {
                $q->where('is_active', true);
            })
            ->get();

        $text = "💰 <b>Saldo Dompet</b>\n\n";

        $totalBalance = 0;
        foreach ($userWallets as $userWallet) {
            $text .= "• <b>{$userWallet->wallet->name}</b>\n";
            $text .= "  " . $this->telegram->formatMoney($userWallet->balance) . "\n\n";
            $totalBalance += $userWallet->balance;
        }

        if ($userWallets->isEmpty()) {
            $text .= "<i>Belum ada dompet</i>\n\n";
        }

        $text .= "━━━━━━━━━━━━━━━━━━\n";
        $text .= "<b>Total Saldo:</b> " . $this->telegram->formatMoney($totalBalance);

        $this->telegram->sendMessage($chatId, $text, $this->telegram->getMainMenuKeyboard());
    }

    /**
     * Show daily report
     */
    protected function showDailyReport(int $chatId, TelegramSession $session): void
    {
        $today = now()->startOfDay();
        $userId = $session->user_id;

        $income = Transaction::whereHas('category.transactionGroup', function ($q) {
            $q->where('type', 'in');
        })
            ->where('user_id', $userId)
            ->whereDate('date', $today)
            ->sum('amount');

        $expense = Transaction::whereHas('category.transactionGroup', function ($q) {
            $q->where('type', 'out');
        })
            ->where('user_id', $userId)
            ->whereDate('date', $today)
            ->sum('amount');

        $balance = $income - $expense;
        $balanceIcon = $balance >= 0 ? '✅' : '⚠️';

        $text = "📊 <b>Laporan Hari Ini</b>\n" .
            now()->format('d F Y') . "\n\n" .
            "📥 Pemasukan: " . $this->telegram->formatMoney($income) . "\n" .
            "📤 Pengeluaran: " . $this->telegram->formatMoney($expense) . "\n" .
            "━━━━━━━━━━━━━━━━━━\n" .
            "$balanceIcon Selisih: " . $this->telegram->formatMoney($balance);

        // Get top categories today
        $topExpenses = Transaction::select('category_id', DB::raw('SUM(amount) as total'))
            ->whereHas('category.transactionGroup', function ($q) {
                $q->where('type', 'out');
            })
            ->where('user_id', $userId)
            ->whereDate('date', $today)
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('category')
            ->get();

        if ($topExpenses->isNotEmpty()) {
            $text .= "\n\n<b>Top Pengeluaran:</b>\n";
            foreach ($topExpenses as $index => $item) {
                $num = $index + 1;
                $text .= "$num. {$item->category->name}: " . $this->telegram->formatMoney($item->total) . "\n";
            }
        }

        $this->telegram->sendMessage($chatId, $text, $this->telegram->getMainMenuKeyboard());
    }

    /**
     * Show welcome message with login button
     */
    protected function showWelcome(int $chatId, TelegramSession $session): void
    {
        if ($session->isAuthenticated()) {
            $user = $session->user;
            $text = "👋 Selamat datang kembali, <b>{$user->name}</b>!\n\n";
            $text .= "Anda sudah login sebagai: {$user->email}\n\n";
            $text .= "Pilih menu di bawah untuk memulai:";
            $this->telegram->sendMessage($chatId, $text, $this->telegram->getMainMenuKeyboard());
        } else {
            $text = "🤖 <b>Welcome to CashFlow Tracker Bot!</b>\n\n";
            $text .= "Bot ini membantu Anda mencatat transaksi keuangan dengan mudah.\n\n";
            $text .= "Untuk mulai menggunakan bot, silakan login terlebih dahulu dengan email dan password akun CashFlow Tracker Anda.\n\n";
            $text .= "Tekan tombol <b>Login</b> di bawah untuk memulai.";

            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '🔐 Login', 'callback_data' => 'start_login']
                    ]
                ]
            ];

            $this->telegram->sendMessage($chatId, $text, $keyboard);
        }
    }

    /**
     * Handle email input
     */
    protected function handleEmailInput(int $chatId, string $email, TelegramSession $session): void
    {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->telegram->sendMessage(
                $chatId,
                "❌ Format email tidak valid.\n\nSilakan masukkan email yang benar:"
            );
            return;
        }

        // Save email to session
        $session->updateState('waiting_password', ['email' => $email]);

        $this->telegram->sendMessage(
            $chatId,
            "✅ Email: <code>{$email}</code>\n\n🔒 Sekarang masukkan password Anda:\n\n<i>Note: Password tidak akan ditampilkan</i>"
        );
    }

    /**
     * Handle password input
     */
    protected function handlePasswordInput(int $chatId, string $password, TelegramSession $session): void
    {
        $email = $session->data['email'] ?? null;

        if (!$email) {
            $this->telegram->sendMessage(
                $chatId,
                "❌ Sesi login telah berakhir.\n\nGunakan /start untuk memulai ulang."
            );
            $session->reset();
            return;
        }

        // Attempt authentication
        if ($session->authenticate($email, $password)) {
            $user = $session->user;

            $text = "✅ <b>Login Berhasil!</b>\n\n";
            $text .= "Selamat datang, <b>{$user->name}</b>!\n";
            $text .= "Email: {$user->email}\n\n";
            $text .= "Anda sekarang dapat menggunakan bot untuk mencatat transaksi.\n\n";
            $text .= "Pilih menu di bawah untuk memulai:";

            $this->telegram->sendMessage($chatId, $text, $this->telegram->getMainMenuKeyboard());
        } else {
            $this->telegram->sendMessage(
                $chatId,
                "❌ <b>Login Gagal!</b>\n\nEmail atau password salah.\n\nGunakan /start untuk mencoba lagi."
            );
            $session->reset();
        }
    }
}
