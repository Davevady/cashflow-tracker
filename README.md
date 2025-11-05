# 💰 CashFlow Tracker

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**CashFlow Tracker** adalah aplikasi web untuk mengelola keuangan pribadi atau bisnis dengan fitur lengkap untuk mencatat pemasukan, pengeluaran, transfer antar dompet, dan integrasi Telegram Bot.

## 📋 Fitur Utama

### 💼 Manajemen Keuangan
- ✅ **Dashboard** - Ringkasan keuangan dengan grafik interaktif
- ✅ **Transaksi** - Pencatatan pemasukan dan pengeluaran
- ✅ **Multi-Wallet** - Kelola banyak dompet dengan grup dompet
- ✅ **Transfer Antar Dompet** - Transfer saldo antar dompet
- ✅ **Kategori Transaksi** - Organisasi transaksi berdasarkan kategori
- ✅ **Member Tracking** - Catat siapa yang melakukan transaksi
- ✅ **Soft Delete & Restore** - Trash management untuk data yang dihapus

### 🤖 Telegram Bot Integration
- ✅ **Record Transactions** - Catat transaksi langsung via Telegram
- ✅ **Interactive Keyboards** - Pilihan kategori, dompet, dan tanggal
- ✅ **Multi-User Support** - Session management per user
- ✅ **Real-time Sync** - Langsung tersimpan ke database

### 🔄 Money+ App Scraper
- ✅ **Auto Sync** - Sinkronisasi otomatis dari Money+ app
- ✅ **Transaction Import** - Import transaksi dari Money+
- ✅ **Category Mapping** - Pemetaan kategori otomatis

### 🎨 Dynamic Add Feature
- ✅ **Wallet Groups** - Tambah grup dompet langsung dari form wallet
- ✅ **Transaction Groups** - Tambah grup transaksi dari form kategori
- ✅ **AJAX Modal** - Tanpa reload halaman

### 👥 User Management (Role-Based)
- ✅ **Admin** - Full access ke semua fitur
- ✅ **Manager** - Kelola transaksi, kategori, dompet
- ✅ **User** - View dan create transaksi saja
- ✅ **Role & Permission Management** - Kelola hak akses

## 🚀 Tech Stack

- **Backend**: Laravel 10.x
- **Frontend**: Bootstrap 5 + jQuery
- **Database**: MySQL 8.0+
- **Charts**: Chart.js
- **Icons**: FontAwesome 5
- **Notifications**: Bootstrap Notify
- **Scraper**: Selenium WebDriver + Appium
- **Bot**: Telegram Bot API

## 📦 Instalasi

### Prerequisites

Pastikan Anda sudah menginstall:
- PHP 8.1 atau lebih tinggi
- Composer
- MySQL 8.0+
- Node.js & NPM (opsional, untuk scraper)
- Git

### Step 1: Clone Repository

```bash
git clone https://github.com/Davevady/cashflow-tracker.git
cd cashflow-tracker
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies (opsional, untuk scraper)
cd public/scrape
npm install
cd ../..
```

### Step 3: Environment Configuration

```bash
# Copy .env.example ke .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Database Configuration

Edit file `.env` dan sesuaikan dengan database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cashflow_tracker
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 5: Run Migrations & Seeders

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE cashflow_tracker"

# Run migrations
php artisan migrate

# Run seeders (optional - untuk data dummy)
php artisan db:seed
```

Seeder akan membuat:
- 1 user admin: `admin@example.com` / `password`
- 1 user manager: `manager@example.com` / `password`
- 1 user biasa: `user@example.com` / `password`
- Roles & permissions
- Sample categories, wallets, dan transactions

### Step 6: Storage Link

```bash
php artisan storage:link
```

### Step 7: Run Application

```bash
# Development server
php artisan serve

# Akses di: http://localhost:8000
```

## ⚙️ Konfigurasi Tambahan

### 🤖 Telegram Bot Setup (Opsional)

1. Buat bot baru di [@BotFather](https://t.me/BotFather)
2. Copy token yang diberikan
3. Tambahkan ke `.env`:

```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_BOT_WEBHOOK_URL=https://yourdomain.com/telegram/webhook
```

4. Set webhook:

```bash
php artisan tinker
>>> $service = new App\Services\TelegramService();
>>> $service->setWebhook();
```

**Detail lengkap**: Lihat [TELEGRAM_BOT_SETUP.md](TELEGRAM_BOT_SETUP.md)

### 📱 Money+ Scraper Setup (Opsional)

1. Install Appium Server
2. Setup Android Emulator atau device
3. Konfigurasi `.env` di `public/scrape/.env`:

```env
APPIUM_SERVER_URL=http://localhost:4723
DEVICE_NAME=emulator-5554
MONEY_PLUS_APK_PATH=./money_plus.apk
API_BASE_URL=http://localhost:8000/api
API_TOKEN=your_api_token
```

4. Run scraper:

```bash
cd public/scrape
node scrape_money_plus.js
```

**Detail lengkap**: Lihat [MONEY_PLUS_SCRAPER.md](MONEY_PLUS_SCRAPER.md)

### 🎨 Dynamic Add Feature

Fitur ini sudah aktif by default. Dokumentasi lengkap: [DYNAMIC_ADD_FEATURE.md](DYNAMIC_ADD_FEATURE.md)

## 📖 User Guide

### Login

1. Akses `http://localhost:8000/login`
2. Login dengan kredensial:
   - **Admin**: `admin@example.com` / `password`
   - **Manager**: `manager@example.com` / `password`
   - **User**: `user@example.com` / `password`

### Dashboard

Dashboard menampilkan:
- **Summary Cards**: Total pemasukan, pengeluaran, saldo
- **Quick Add Transaction**: Form cepat tambah transaksi
- **Recent Transactions**: 10 transaksi terakhir
- **Charts**:
  - Transaksi per kategori
  - Transaksi per member
  - Transaksi per dompet
  - Tren bulanan (income vs expense)

### Mengelola Transaksi

#### Tambah Transaksi Baru
1. Klik tombol **"Tambah Transaksi"** di dashboard atau menu Transactions
2. Pilih **tipe transaksi**: Cash IN (pemasukan) atau Cash OUT (pengeluaran)
3. Pilih **kategori** (dropdown akan berubah sesuai tipe)
4. Pilih **dompet** yang akan digunakan
5. Masukkan **jumlah uang**
6. Pilih **tanggal transaksi**
7. Pilih **member** (opsional)
8. Tambahkan **catatan** (opsional)
9. Klik **Simpan**

#### Edit Transaksi
1. Buka halaman **Transactions**
2. Klik tombol **Edit** pada transaksi yang ingin diubah
3. Update data yang diperlukan
4. Klik **Update**

**Note**: Saldo dompet akan otomatis di-adjust saat transaksi dibuat, diupdate, atau dihapus.

### Mengelola Dompet

#### Buat Grup Dompet Baru
1. Klik menu **Wallet Groups** (Admin/Manager only)
2. Klik **Tambah Grup Dompet**
3. Isi nama, icon, dan deskripsi
4. Klik **Simpan**

#### Buat Dompet Baru
1. Klik menu **Wallets**
2. Klik **Tambah Dompet**
3. Pilih **Grup Dompet** (atau klik [+] untuk buat grup baru)
4. Isi nama dompet
5. Masukkan **saldo awal**
6. Tambahkan deskripsi (opsional)
7. Klik **Simpan**

#### Transfer Antar Dompet
1. Klik menu **Wallet Transfers**
2. Klik **Tambah Transfer**
3. Pilih **dompet asal**
4. Pilih **dompet tujuan**
5. Masukkan **jumlah transfer**
6. Pilih **tanggal**
7. Tambahkan **catatan** (opsional)
8. Klik **Simpan**

### Mengelola Kategori

#### Buat Grup Transaksi Baru
1. Klik menu **Transaction Groups** (Admin/Manager only)
2. Klik **Tambah Grup Transaksi**
3. Isi nama
4. Pilih **tipe**: Cash IN atau Cash OUT
5. Pilih icon dan deskripsi
6. Klik **Simpan**

#### Buat Kategori Baru
1. Klik menu **Categories**
2. Klik **Tambah Kategori**
3. Pilih **Grup Transaksi** (atau klik [+] untuk buat grup baru)
4. Isi nama kategori
5. Pilih icon dan deskripsi
6. Klik **Simpan**

### Trash Management

Untuk mengelola data yang sudah dihapus:
1. Klik menu **Trash** (Admin/Manager only)
2. Pilih **jenis data** yang ingin dilihat
3. Klik **Restore** untuk mengembalikan
4. Klik **Permanent Delete** untuk hapus permanen
5. Klik **Empty Trash** untuk hapus semua

## 🗂️ Struktur Database

### Core Tables
- `users` - Data pengguna
- `roles` - Role (admin, manager, user)
- `permissions` - Permission untuk setiap role
- `role_user` - Pivot table user dan role

### Transaction Tables
- `transaction_groups` - Grup transaksi (Cash IN/OUT)
- `categories` - Kategori transaksi
- `transactions` - Data transaksi
- `members` - Member yang melakukan transaksi

### Wallet Tables
- `wallet_groups` - Grup dompet (Bank, E-Wallet, Cash)
- `wallets` - Data dompet
- `wallet_transfers` - Transfer antar dompet

### Integration Tables
- `telegram_sessions` - Session Telegram bot

## 🔐 Security

- **Authentication**: Laravel Sanctum (untuk API)
- **Authorization**: Role-based access control (RBAC)
- **CSRF Protection**: Aktif untuk semua form
- **Password Hashing**: Bcrypt
- **SQL Injection Prevention**: Eloquent ORM & Prepared Statements
- **XSS Prevention**: Blade template escaping

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=TransactionTest
```

## 📊 Database Schema

```
users
├── roles (many-to-many)
└── transactions (one-to-many)

transaction_groups
└── categories (one-to-many)
    └── transactions (one-to-many)

wallet_groups
└── wallets (one-to-many)
    ├── transactions (one-to-many)
    ├── wallet_transfers (from_wallet)
    └── wallet_transfers (to_wallet)

members
└── transactions (one-to-many)

telegram_sessions
└── telegram_user_id (unique)
```

## 🎨 Screenshots

### Dashboard
![Dashboard](docs/screenshots/dashboard.png)

### Transactions
![Transactions](docs/screenshots/transactions.png)

### Wallets
![Wallets](docs/screenshots/wallets.png)

### Telegram Bot
![Telegram Bot](docs/screenshots/telegram-bot.png)

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a new branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 Commit Convention

We follow [Conventional Commits](https://www.conventionalcommits.org/):

```
feat: add new feature
fix: bug fix
docs: documentation changes
style: code style changes (formatting, etc)
refactor: code refactoring
test: add or update tests
chore: maintenance tasks
```

## 🐛 Known Issues

- Dynamic add category di dashboard & transaction edit form dinonaktifkan (rendering issues)
- Money+ scraper memerlukan Android emulator yang running

## 📅 Roadmap

- [ ] Export transaksi ke Excel/PDF
- [ ] Multi-currency support
- [ ] Recurring transactions (auto-record)
- [ ] Budget planning & tracking
- [ ] Financial reports (monthly, yearly)
- [ ] Mobile app (React Native)
- [ ] Multi-language support
- [ ] Dark mode

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**David Valerian**
- GitHub: [@Davevady](https://github.com/Davevady)
- Email: david@example.com

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Bootstrap](https://getbootstrap.com) - UI Framework
- [Chart.js](https://www.chartjs.org) - Charts Library
- [FontAwesome](https://fontawesome.com) - Icons
- [Telegram Bot API](https://core.telegram.org/bots/api) - Bot Integration

---

**⭐ If you find this project useful, please consider giving it a star!**

**🤖 Generated with [Claude Code](https://claude.com/claude-code)**
