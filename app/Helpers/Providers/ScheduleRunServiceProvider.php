<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;

class ScheduleRunServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Mengecek apakah schedule sudah pernah dijalankan
        if (cache('schedule_run') !== true) {
            // Jalankan scheduler sekali
            Artisan::call('schedule:run');

            // Tandai bahwa schedule telah dijalankan
            cache(['schedule_run' => true], now()->addMinutes(30)); // Misal cache selama 30 menit
        }
    }
}
