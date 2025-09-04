<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule; // <-- Ini yang penting bro!

class ServiceLaporanBulananProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
        $this->app->booted(
            function () {
                $schedule = $this->app->make(Schedule::class);
                // $schedule->command('cek:jadwal')->hourly();
                // $schedule->command('siakad:WhatsappBulanan')->monthlyOn(3, '08:30');
            }
        );
        //$schedule->command('maintenance:clear-all')->everyMinute();
    }
}
// $schedule->command('cek:jadwal')->when(function () {
//     $targetTimes = ['07:00', '07:40', '08:20', '09:00'];

//     foreach ($targetTimes as $target) {
//         $targetTime = \Carbon\Carbon::createFromFormat('H:i', $target)
//             ->setDate(now()->year, now()->month, now()->day);

//         $diff = now()->diffInMinutes($targetTime, false); // false = hasil bisa negatif

//         if ($diff >= 0 && $diff <= 2) return true; // toleransi 2 menit
//     }

//     return false;
// })->everyMinute();
