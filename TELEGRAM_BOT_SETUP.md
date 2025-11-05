# Telegram Bot Setup Guide - CashFlow Tracker

## 🚀 Quick Start

### 1. Dapatkan Bot Token dari BotFather

```
1. Buka Telegram, cari @BotFather
2. Kirim /newbot
3. Ikuti instruksi (nama bot & username)
4. Simpan token yang diberikan
```

### 2. Konfigurasi .env

```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_WEBHOOK_URL=https://your-domain.com/telegram/webhook
```

### 3. Set Webhook menggunakan curl

```bash
curl -X POST https://api.telegram.org/bot<YOUR_TOKEN>/setWebhook \
     -d "url=https://your-domain.com/telegram/webhook" \
     -d "allowed_updates=[\"message\",\"callback_query\"]"
```

### 4. Verifikasi Webhook

```bash
curl https://api.telegram.org/bot<YOUR_TOKEN>/getWebhookInfo
```

## 📁 File Structure

```
✅ database/migrations/2025_11_05_130614_create_telegram_sessions_table.php
✅ app/Models/TelegramSession.php
✅ app/Services/TelegramService.php
✅ app/Http/Controllers/TelegramController.php
✅ routes/web.php (webhook route added)
✅ config/services.php (telegram config added)
```

## 🎯 Bot Features

- ➕ **Tambah Transaksi** (Pemasukan/Pengeluaran)
- 💰 **Lihat Saldo** (All wallets)
- 📊 **Laporan Hari Ini** (Income, Expense, Balance)

## 🔄 Conversation Flow

```
/start 
  └─> Menu Utama
       ├─> Tambah Transaksi
       │    ├─> Pilih Jenis (Income/Expense)
       │    ├─> Pilih Kategori
       │    ├─> Pilih Wallet
       │    ├─> Input Nominal
       │    └─> Input Keterangan → ✅ Saved!
       │
       ├─> Lihat Saldo
       │    └─> Tampilkan semua wallet + total
       │
       └─> Laporan Hari Ini
            └─> Income, Expense, Balance, Top Categories
```

## 📊 Database: telegram_sessions

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| chat_id | bigint | Telegram chat ID (unique) |
| user_id | bigint | Laravel user ID (nullable) |
| state | varchar | Conversation state |
| data | json | Session data |
| last_activity | timestamp | Last interaction |

### States

- `idle` - Default state
- `selecting_type` - Choosing transaction type
- `selecting_category` - Choosing category
- `selecting_wallet` - Choosing wallet
- `waiting_amount` - Waiting for amount input
- `waiting_note` - Waiting for note input

## 🛠️ Development dengan ngrok

```bash
# Terminal 1: Run Laravel
php artisan serve

# Terminal 2: Run ngrok
ngrok http 8000

# Set webhook dengan ngrok URL
curl -X POST https://api.telegram.org/bot<TOKEN>/setWebhook \
     -d "url=https://abc123.ngrok.io/telegram/webhook"
```

## 📝 Contoh Webhook JSON

### Message (text):
```json
{
  "update_id": 123456789,
  "message": {
    "message_id": 1234,
    "from": {
      "id": 987654321,
      "first_name": "John",
      "username": "johndoe"
    },
    "chat": {
      "id": 987654321,
      "type": "private"
    },
    "date": 1699000000,
    "text": "/start"
  }
}
```

### Callback Query (button click):
```json
{
  "update_id": 123456790,
  "callback_query": {
    "id": "abc123",
    "from": {
      "id": 987654321,
      "first_name": "John"
    },
    "message": {
      "message_id": 1235,
      "chat": {
        "id": 987654321
      },
      "text": "Menu text"
    },
    "data": "add_transaction"
  }
}
```

## 🐛 Troubleshooting

### Bot tidak merespon?
```bash
# Cek webhook status
curl https://api.telegram.org/bot<TOKEN>/getWebhookInfo

# Cek Laravel logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### Error: HTTPS required
- Webhook harus HTTPS
- Gunakan ngrok untuk development
- Gunakan Let's Encrypt untuk production

### Session tidak tersimpan?
```bash
# Cek migration
php artisan migrate:status

# Test di tinker
php artisan tinker
>>> \App\Models\TelegramSession::count()
```

## ⚙️ Service Methods (TelegramService)

```php
// Send message
$telegram->sendMessage($chatId, $text, $keyboard);

// Edit message
$telegram->editMessageText($chatId, $messageId, $text, $keyboard);

// Answer callback
$telegram->answerCallbackQuery($callbackId, $text);

// Keyboards
$telegram->getMainMenuKeyboard();
$telegram->getTransactionTypeKeyboard();
$telegram->getCategoryKeyboard($type);
$telegram->getWalletKeyboard();
$telegram->getCancelKeyboard();

// Helpers
$telegram->formatMoney($amount); // Returns: "Rp 50.000"
$telegram->parseMoney($text);    // Returns: 50000

// Webhook management
$telegram->setWebhook($url);
$telegram->getWebhookInfo();
$telegram->deleteWebhook();
```

## 🔐 Security Notes

1. Keep bot token secret (never commit to git)
2. Use HTTPS for webhooks
3. Validate all user inputs
4. Implement rate limiting
5. Consider user authentication for production

## 📦 Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Use HTTPS with valid SSL
- [ ] Configure `TELEGRAM_BOT_TOKEN`
- [ ] Configure `TELEGRAM_WEBHOOK_URL`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Setup logging & monitoring
- [ ] Implement user auth
- [ ] Test all flows

## 📞 Support

Laravel logs: `storage/logs/laravel.log`
Webhook info: `https://api.telegram.org/bot<TOKEN>/getWebhookInfo`

---

**Built with ❤️ using Laravel 10 & Telegram Bot API**
