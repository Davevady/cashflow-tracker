<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL; // ← tambahkan ini

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Force asset & route pakai HTTPS saat diakses dari Cloudflare
        URL::forceScheme('https');

        // Set Carbon locale to Indonesian
        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian');

        // Blade directives
        Blade::directive('indonesianDate', function ($expression) {
            return "<?php echo ($expression)->locale('id')->translatedFormat('l, d F Y - H:i'); ?>";
        });

        Blade::directive('shortIndonesianDate', function ($expression) {
            return "<?php echo ($expression)->locale('id')->translatedFormat('d M Y - H:i'); ?>";
        });
    }
}
