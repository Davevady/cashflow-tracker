# Money+ Wallet Scraper

Scraper untuk mengambil data wallet dari aplikasi Money+ dan sinkronisasi ke database Laravel CashFlow Tracker.

## 🎯 Fitur

- ✅ Auto-klik tab "Dompet" di Money+ app
- ✅ Scraping semua wallet dengan balance
- ✅ Auto-scroll sampai mentok untuk memastikan semua wallet ter-scrape
- ✅ Deduplikasi wallet berdasarkan nama
- ✅ Sinkronisasi otomatis ke database Laravel
- ✅ Auto-create atau update wallet

## 📋 Prerequisites

1. **Appium Server** (harus running)
   ```bash
   npm install -g appium
   appium
   ```

2. **Android Device/Emulator** dengan Money+ app terinstall

3. **ADB** (Android Debug Bridge)
   ```bash
   adb devices  # Cek device terhubung
   ```

4. **Node.js dependencies**
   ```bash
   cd public/scrape
   npm install webdriverio axios
   ```

5. **Laravel API** (harus running)
   ```bash
   php artisan serve
   ```

## 🚀 Cara Menggunakan

### 1. Setup Konfigurasi

Edit file `public/scrape/run_money_plus.js`:

```javascript
const CONFIG = {
    adbName: "emulator-5554",  // Ganti dengan device name kamu
    appPackage: "com.iglesiasantos.money",  // Package name Money+
    appActivity: ".MainActivity",  // Activity utama
    apiBaseUrl: "http://127.0.0.1:8000"  // URL Laravel
};
```

**Cara mendapatkan package name dan activity:**
```bash
# Buka Money+ app, lalu jalankan:
adb shell dumpsys window | grep -E 'mCurrentFocus'

# Output contoh:
# mCurrentFocus=Window{abc123 u0 com.iglesiasantos.money/.MainActivity}
```

### 2. Jalankan Scraper

```bash
cd public/scrape
node run_money_plus.js
```

### 3. Lihat Hasil

Scraper akan:
1. Membuka app Money+
2. Klik tab "Dompet"
3. Scrape semua wallet dengan scroll otomatis
4. Kirim data ke Laravel API
5. Create/update wallet di database

## 📊 Format Data yang Di-scrape

Dari deskripsi elemen seperti:
```
"Arisan
Rp0
IDR(1.0)"
```

Akan di-parse menjadi:
```json
{
  "name": "Arisan",
  "balance": 0
}
```

## 🔄 API Endpoints

### POST `/api/wallets/sync-from-scraper`

Sinkronisasi wallet dari scraper.

**Request:**
```json
{
  "wallets": [
    {
      "name": "Arisan",
      "balance": 0
    },
    {
      "name": "Kas Utama",
      "balance": 150000
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Wallets synced successfully",
  "created": 2,
  "updated": 0,
  "skipped": 0,
  "total": 2
}
```

### GET `/api/wallets/sync-status`

Cek status sinkronisasi wallet Money+.

**Response:**
```json
{
  "success": true,
  "synced": true,
  "wallet_count": 5,
  "total_balance": 1500000,
  "wallets": [
    {
      "id": 1,
      "name": "Kas Utama",
      "balance": 1000000,
      "is_active": true
    }
  ]
}
```

## 🗂️ File Structure

```
public/scrape/
├── scrape_money_plus.js     # Class utama scraper
├── run_money_plus.js         # Runner script
└── scrape_gopay.js          # Scraper GoPay (referensi)

app/Http/Controllers/Api/
└── WalletSyncController.php  # API controller

routes/
└── api.php                   # API routes
```

## 🔧 Cara Kerja

### 1. Click Tab Dompet
```javascript
await this.clickDompetTab();
// Klik elemen dengan description: "Dompet\nTab 2 dari 4"
```

### 2. Scraping Wallet
```javascript
await this.scrapeWallets();
// Cari semua elemen dengan "IDR" di description
// Parse format: "Nama\nRpBalance\nIDR(1.0)"
// Scroll sampai mentok untuk scrape semua wallet
```

### 3. Sinkronisasi ke Database
```javascript
await this.syncToDatabase();
// POST data ke Laravel API
// API akan create/update wallet berdasarkan nama
```

## 🎨 Wallet Group

Semua wallet dari Money+ akan dikelompokkan dalam:
- **Nama Group:** "Money+ Wallets"
- **Deskripsi:** "Auto-synced from Money+ app"
- **Icon:** `fa fa-wallet`

## 🐛 Troubleshooting

### 1. Tab Dompet tidak ditemukan?
```bash
# Pastikan format description benar
# Cek dengan UIAutomatorViewer atau inspector Appium
```

### 2. Wallet tidak ter-scrape semua?
```javascript
// Tambah maxScroll di scrapeWallets():
const maxScroll = 20; // default: 10
```

### 3. Error "ECONNREFUSED" saat sync?
```bash
# Pastikan Laravel running
php artisan serve

# Atau ganti apiBaseUrl jika berbeda
```

### 4. Device tidak terhubung?
```bash
# Cek device
adb devices

# Restart adb jika perlu
adb kill-server
adb start-server
```

### 5. Appium error?
```bash
# Pastikan Appium running
appium

# Atau restart
pkill -f appium
appium
```

## 📝 Logs

### Scraper Logs
- ✅ Wallet found: Tampil di console
- 📦 Total wallet: Tampil setelah scraping selesai
- 💾 Sync result: Created/Updated/Skipped count

### Laravel Logs
- Log di `storage/logs/laravel.log`
- Info: Wallet created/updated
- Error: Sync failures

## 🔐 Security Notes

1. **CSRF Protection:** API endpoint tidak menggunakan CSRF token (untuk scraper)
2. **Authentication:** Tidak ada auth untuk simplicity, tambahkan jika perlu
3. **Rate Limiting:** Belum diimplementasi, tambahkan jika production

## 🚦 Best Practices

1. **Backup database** sebelum running scraper pertama kali
2. **Test dengan emulator** dulu sebelum device real
3. **Cek hasil sinkronisasi** di web dashboard setelah scrape
4. **Schedule scraping** dengan cron job untuk auto-sync berkala

## 📞 Support

- Laravel logs: `storage/logs/laravel.log`
- Appium logs: Terminal tempat Appium running
- ADB logs: `adb logcat`

---

**Built with ❤️ using Appium, WebdriverIO & Laravel**
