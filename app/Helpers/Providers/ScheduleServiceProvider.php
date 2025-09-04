<?php

namespace App\Providers;

use App\Models\Admin\Identitas;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule; // <-- Ini yang penting bro!

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->commands([
            \App\Console\Commands\KirimJadwalWhatsapp::class,
            \App\Console\Commands\CekAbsenKosongSiswa::class,
            \App\Console\Commands\DataRapatCommand::class,
            \App\Console\Commands\PengingatPembayaranSiswaCommand::class,
            \App\Console\Commands\RessetAndReseed::class,
            \App\Console\Commands\BersihkanTemp::class,
            \App\Console\Commands\BendaharaKomitecommand::class,
            \App\Console\Commands\BendaharaBoscommand::class,
            \App\Console\Commands\Ekstracommand::class,
            \App\Console\Commands\JadwalCbtcommand::class,
            \App\Console\Commands\JadwalPengingatUlanganCommand::class,
            \App\Console\Commands\PengembalianBukuPerpustakaanCommand::class,
            \App\Console\Commands\Ppdbcommand::class,
            \App\Console\Commands\TabunganSiswaCommand::class,
            \App\Console\Commands\Tahfidzcommand::class,

            \App\Console\Commands\PengingatEkstracommand::class, //

            \App\Console\Commands\TahfidzCommand::class,

            \App\Console\Commands\ShalatBerjamaahCommand::class,
        ]);
    }

    /**
     * Bootstrap services.
     * php artisan schedule:work
     * php artisan schedule:list
     * php artisan schedule:run
     * Gunakan ->runInBackground() untuk proses banyak data jadi tetap lanjut
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $Identitas = Identitas::first();
            if (!$Identitas) {
                return; // kalau belum ada data identitas, skip
            }

            $schedule->command('maintenance:clear-all')
                ->dailyAt('02:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/clear_all_cache.log'));

            switch ($Identitas->paket) {
                case 'Kerjasama':
                    // Ekstra
                    //

                    // Shalat Berjamaah
                    // jalankan logika khusus kerjasama
                    break;

                case 'of':

                    // jalankan logika khusus offline
                    break;

                case 'Premium':
                    // Perpustakaan
                    $schedule->command('siakad:pengembalian-buku-perpustakaan')->dailyAt('09:30')->runInBackground(); // Chat ke semua peminjam buku agar besok pagi segera dikembalikan
                    // Tabungan setiap akhir bulan
                    $schedule->command('tabungan:tabungan-siswa')
                        ->dailyAt('09:00')
                        ->when(function () {
                            return now()->isLastOfMonth();
                        });
                    $schedule->command('rapat:data-rapat')->dailyAt('07:30')->runInBackground(); // Chat ke semua peminjam buku agar besok pagi segera dikembalikan
                    // \Log::info('Mode Premium aktif');
                    // jalankan logika khusus premium
                    break;

                default:
                    // \Log::warning("Paket {$Identitas->paket} tidak dikenali");
            }
            $schedule->command('ekstra:pengingat-ekstra')->dailyAt('09:30'); // PengingatEkstracommand
            // $schedule->command('siakad:cobabuat')->everyFiveMinutes(); // KirimJadwalWhatsapp Bagian Internal
            // $schedule->command('siakad:cobabuat')->everyMinute(); // KirimJadwalWhatsapp Bagian Internal
            /*
                |--------------------------------------------------------------------------
                | 📌 Jadwal Whatsapp :
                |--------------------------------------------------------------------------
                |
                | Fitur :
                | - Mengirim pesan sesuai jadwal pengiriman didalam internal sekolah sebagai pengingat harian
                |
                | Tujuan :
                | - Meningkatkan ketepatan waktu dalam melaksanakan agenda kegiatan
                |
                |
                |
            */
            // Proses Coding
            $schedule->command('wa:PenjadwalanPesan')->everyMinute()->runInBackground(); // KirimJadwalWhatsapp Bagian Internal
            /*
                |--------------------------------------------------------------------------
                | 📌 Absensi :
                |--------------------------------------------------------------------------
                |
                | Fitur :
                | - Auto Alfa tidak ada absen pukul 08:000
                | - Rekap dan Laporan Absen Siswa untuk kepala
                | - Rekap dan Laporan Absen Siswa perkelas untuk wali kelas : absen:rekap-absen
                |
                | Tujuan :
                | - Meringankan tugas guru piket
                | - Meningkatkan kerjasama wali kelas dengan laporan penyediaan data absensi
                |
            */
            // Proses Coding
            $schedule->command('siakad:CekAbsenKosongSiswa')->dailyAt('08:30')->runInBackground(); // CekAbsenKosong
            // $schedule->command('absen:rekap-absen')->dailyAt('08:30')->runInBackground(); //Rekap Absen untuk laporan
            /*
                |--------------------------------------------------------------------------
                | 📌 Perpustakaan :
                |--------------------------------------------------------------------------
                |
                | Fitur :
                | - Pengingat pengembalian buku ke siswa 1 hari sebelumnya
                | - xxxxxxxxxxx
                |
                | Tujuan :
                | - Penertiban pengembalian buku ke siswa
            */

            /*
                |--------------------------------------------------------------------------
                | 📌 Pembayaran Siswa : Dilakukan setiap tanggal 2
                |--------------------------------------------------------------------------
                |
                | Fitur Kerjasama :
                | - Pengingat Pembayaran Bulanan Sasaran Ortu Siswa
                |
                | Full Version :
                | - Pengingat Pembayaran Bulanan Sasaran Ortu Siswa
                | - Rekap Pembayaran
                |
                | Tujuan :
                | - Penertiban pembayaran siswa agar terjadinya kestabilan anggaran
                |
            */
            $schedule->command('siakad:pengingat-pembayaran-siswa')->monthlyOn(2, '09:30')->runInBackground();
            // Shalat Berjamaah
            /*
                |--------------------------------------------------------------------------
                | 📌 Shalat Berjamaah :
                |--------------------------------------------------------------------------
                |
                | Fitur :
                | - Pengingat Shalat Berjamaah 1 Hari sebelumnya ke siswa + Imam
                | - xxxxxxxxxxx
                |
                | Tujuan :
                | - Mengingatkan siswa membawa perlengkapan shalat
                |
                |
                | Penggunaan :
                | - xxxxxxxxxxx
                |
                */
            // Proses Coding

            /*
                |--------------------------------------------------------------------------
                | 📌 BackUpDatabase :
                |--------------------------------------------------------------------------
                |
                | Fitur :
                | - Backup otomatis database untuk menghindari terjadi sesuatu yang tidak di inginkan
                | - xxxxxxxxxxx
                |
                | Tujuan :
                | - xxxxxxxxxxx
                |
                |
                | Penggunaan :
                | - xxxxxxxxxxx
                |
                */
            // Proses Coding
            $schedule->command('backup:database')->dailyAt('08:00'); //BackupDatabase

        });

        //$schedule->command('maintenance:clear-all')->everyMinute();
    }
}
/*
Untuk hari sabtu
$schedule->command('namaCommand')->weekly()->saturdays();
$schedule->command('namaCommand')->weeklyOn(6, '14:00'); // 6 = Sabtu, jam 14:00
*/
