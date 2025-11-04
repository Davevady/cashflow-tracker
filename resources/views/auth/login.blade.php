<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CashFlow Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-indigo-600 mb-2">
                    CashFlow Tracker
                </h1>
                <p class="text-gray-600">Login</p>
            </div>

            <!-- Login Form -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 font-semibold mb-2">
                            Email
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-red-500 @enderror"
                            placeholder="email@example.com"
                        >
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 font-semibold mb-2">
                            Password
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('password') border-red-500 @enderror"
                            placeholder="••••••••"
                        >
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="remember"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            <span class="ml-2 text-gray-700">Remember me</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 shadow-md hover:shadow-lg"
                    >
                        Login
                    </button>
                </form>

                <!-- Back to Home -->
                <div class="mt-6 text-center">
                    <a href="{{ route('welcome') }}" class="text-indigo-600 hover:text-indigo-700 text-sm">
                        &larr; Kembali ke Beranda
                    </a>
                </div>
            </div>

            <!-- Demo Credentials (Optional - for development) -->
            @if(config('app.env') === 'local')
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-sm">
                <p class="font-semibold text-yellow-800 mb-1">Demo Credentials:</p>
                <p class="text-yellow-700">Email: admin@cashflow.com</p>
                <p class="text-yellow-700">Password: password</p>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
