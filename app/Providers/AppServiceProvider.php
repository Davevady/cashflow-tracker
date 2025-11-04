<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set Carbon locale to Indonesian
        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian');

        // Register custom Blade directive for Indonesian date format
        Blade::directive('indonesianDate', function ($expression) {
            return "<?php echo ($expression)->locale('id')->translatedFormat('l, d F Y - H:i'); ?>";
        });

        Blade::directive('shortIndonesianDate', function ($expression) {
            return "<?php echo ($expression)->locale('id')->translatedFormat('d M Y, H:i'); ?>";
        });
    }
}
