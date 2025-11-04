<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CashFlow Tracker - Personal Finance Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <!-- Logo/Header -->
            <div class="mb-8">
                <h1 class="text-6xl font-bold text-indigo-600 mb-4">
                    CashFlow Tracker
                </h1>
                <p class="text-2xl text-gray-600">
                    Kelola Keuangan Pribadi Anda dengan Mudah
                </p>
            </div>

            <!-- Features -->
            <div class="grid md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-4xl mb-4">📊</div>
                    <h3 class="font-semibold text-lg mb-2">Laporan Lengkap</h3>
                    <p class="text-gray-600">Pantau pemasukan dan pengeluaran dengan detail</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-4xl mb-4">🏷️</div>
                    <h3 class="font-semibold text-lg mb-2">Kategori Terorganisir</h3>
                    <p class="text-gray-600">Kelompokkan transaksi berdasarkan kategori</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-4xl mb-4">📈</div>
                    <h3 class="font-semibold text-lg mb-2">Analisis Cashflow</h3>
                    <p class="text-gray-600">Lihat tren keuangan Anda dengan grafik</p>
                </div>
            </div>

            <!-- CTA Button -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">
                    Siap Mengelola Keuangan Anda?
                </h2>
                <p class="text-gray-600 mb-6">
                    Login sebagai CTO untuk mengakses dashboard
                </p>
                <a href="{{ route('login') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-md hover:shadow-lg">
                    Login Sekarang
                </a>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} CashFlow Tracker. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
