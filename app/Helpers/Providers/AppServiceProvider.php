<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Admin\Ekelas;
use App\Models\Admin\Etapel;
use App\Models\RiwayatLogin;
use App\Models\Admin\Identitas;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use App\Models\User\Guru\Detailguru;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use App\Models\Program\SetingPengguna;
use App\Models\User\Siswa\Detailsiswa;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use App\Console\Commands\MakeCustomController;
use App\Models\Admin\Emapel;
use App\Models\Program\Template\TemplateDokumen;
use App\Models\Tingkat;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    // app/Providers/EventServiceProvider.php
    protected $listen = [
        \Illuminate\Auth\Events\Login::class => [
            RiwayatLogin::class,
        ],
    ];

    public function register(): void
    {
        //

        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(VerifyCsrfToken::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Simpan Identitas sekali saja
        app()->singleton('Identitas', function () {
            return Identitas::first();
        });
        // $identitas = Identitas::first();

        // if ($identitas) {
        //     config([
        //         'whatsapp.NoSekolah' => $identitas->phone,
        //         'whatsapp.NoYayasn' => $identitas->phone,
        //         'whatsapp.NoPengawas' => $identitas->phone,
        //     ]);
        //     // $noSekolah = config('whatsapp.NoSekolah');    // nomor sekolah dari DB
        //     // $noYayasan = config('whatsapp.NoYayasn');     // nomor yayasan dari DB
        //     // $noPengawas = config('whatsapp.NoPengawas');  // nomor pengawas dari DB
        //     // $devNomor = config('whatsapp.DevNomorTujuan'); // dari env atau default
        // }
        // === SCHEDULER: Maintenance + Backup SQL ===
        // $this->app->booted(function () {
        //     $schedule = app(Schedule::class);

        //     // ✅ Clear cache otomatis tiap jam jika belum dilakukan
        //     $lastClear = Cache::get('last_clear_cache_time');
        //     if (!$lastClear || now()->diffInMinutes($lastClear) >= 60) {
        //         Artisan::call('maintenance:clear-all');
        //         Cache::put('last_clear_cache_time', now(), now()->addHours(2));
        //     }

        //     // ✅ Backup database tiap jam
        //     $schedule->command('backup:database')->hourly();
        // });

        // Mengecek jika rute yang diakses bukan admindev atau login
        // if (app()->runningInConsole() === false) {
        //     $lastRun = Cache::get('last_maintenance_run');

        //     if (!$lastRun || now()->diffInHours($lastRun) >= 2) {
        //         Artisan::call('maintenance:clear-all');
        //         Cache::put('last_maintenance_run', now(), now()->addHours(2)); // expire-nya 2 jam
        //     }
        // }
        $routeName = \Illuminate\Support\Facades\Route::currentRouteName();
        // dd(request()->segment(1)); // Output: 'absensi.data'
        // Data Token
        if (!app()->runningInConsole() && php_sapi_name() !== 'cli') {
            try {
                // Lewati pengecekan untuk rute tertentu
                if (request()->is('admindev/*') || request()->is('login/*') || request()->is('logout/*')) {
                    // Tidak melakukan apa-apa untuk rute ini
                } else {
                    // Ambil data identitas dari database
                    $identitas = Identitas::first();
                    // dd($identitas->token);

                    // Jika data identitas atau token tidak tersedia
                    if (!$identitas || !$identitas->token) {
                        abort(403, 'Token tidak ada. Akses dikunci.');
                    }

                    // Dekripsi token
                    $tokenPlain = Crypt::decryptString($identitas->token);

                    // Validasi isi token
                    if (!Str::contains($tokenPlain, ['Gratis', 'Kerjasama', 'Basic', 'Trial', 'Premium'])) {
                        abort(403, 'Token Tidak Valid. Akses dikunci.');
                    }
                }
            } catch (\Exception $e) {
                // Tangani error dekripsi token atau token rusak

                // Ambil segmen pertama dari URL untuk pengecekan rute
                $segment = request()->segment(1);

                // Jika bukan rute yang dikecualikan, blokir akses
                if (!Str::contains($segment, ['login', 'admindev', 'logout', 'public'])) {
                    abort(403, 'Token Telah Dirusak. Akses Dikunci.');
                }
            }
        }
        View::composer('*', function ($view) {
            // Ambil data identitas dari cache jika tersedia, jika tidak maka ambil dari database dan simpan ke cache selama 1 hari
            $identitas = Cache::remember('identitas_data', now()->addDay(), function () {
                return Identitas::first(); // Ambil baris pertama dari tabel identitas (asumsi hanya ada 1 data sekolah)
            });

            $tapels = Cache::remember('data_tapels', now()->addHours(2), function () {
                return Etapel::where('aktiv', 'Y')->first();
            });

            // Jika data identitas berhasil ditemukan
            if ($identitas) {
                // Cek apakah kolom trial_ends_at terisi, jika ya ubah menjadi objek Carbon (untuk pengecekan tanggal)
                $trialEnds = $identitas->trial_ends_at ? Carbon::parse($identitas->trial_ends_at) : null;

                // Jika paketnya adalah Trial atau Kerjasama DAN masa aktifnya sudah lewat
                if (in_array($identitas->paket, ['Trial', 'Kerjasama', 'Basic', 'Premium']) && $trialEnds && $trialEnds->isPast()) {
                    if ($identitas->paket !== 'Gratis') {
                        $identitas->paket = 'Gratis';
                        $identitas->save();
                        Cache::put('identitas_data', $identitas, now()->addDay());
                    }
                }
            }

            $Programs = Cache::tags(['cache_Programs'])->remember('remember_Programs', now()->addHours(2), function () use ($tapels) {
                return SetingPengguna::all();
            });
            // $Programs =  SetingPengguna::all();



            // $Programs = Cache::tags('cache_seting_program')->remember('seting_program', now()->addDays(1), function () {
            //     return SetingPengguna::all();
            // });
            $gurus = Cache::remember('data_guru_all', 60, function () {
                return Detailguru::WhereNotIn('id', [1, 2, 3])->orderBy('nama_guru', 'ASC')->get();
            });

            $kelas = Cache::tags(['cache_kelas'])->remember("kelas_tapel_{$tapels->id}", now()->addHours(2), function () use ($tapels) {
                return Ekelas::where('tapel_id', $tapels->id)->get();
            });
            $siswas = Cache::remember('siswas_global', now()->addMinutes(10), function () {
                return Detailsiswa::with(['KelasOne'])
                    ->whereNotNull('kelas_id')
                    ->orderBy('kelas_id')
                    ->orderBy('nama_siswa')
                    ->get();
            });
            $mapels = Cache::tags(['Cache_mapels'])->remember('Remember_mapels', now()->addMinutes(10), function () {
                return Emapel::where('aktiv', 'Y')->get();
            });
            if (in_array($identitas->jenjang, ['SMP N', 'SMP S', 'MTs S', 'MTs N'])) {
                $jenjang = 2;
            } elseif (in_array($identitas->jenjang, ['SMA N', 'SMA S', 'SMK S', 'SMK N', 'MA S', 'MA N'])) {
                $jenjang = 3;
            } else {
                $jenjang = 1;
            }
            $tingkats = Tingkat::where('jenjang', $jenjang)->get();
            $paket = optional($identitas)->paket;
            $view->with([
                'Identitas' => $identitas,
                'Tapels' => $tapels,
                'Programs' => $Programs,
                'IsPaketAktif' => !in_array($paket, ['Gratis', 'Kerjasama']),
                'IsPaketGratis' => $paket === 'Gratis',
                'IsPaketKerjasama' => $paket === 'Kerjasama',
                'Kelas' => $kelas,
                'Gurus' => $gurus,
                'Siswas' => $siswas,
                'Tingkats' => $tingkats,
                'Mapels' => $mapels,
            ]);
        });

        // HapusCacheDenganTag('seting_program');
        // HapusCacheDenganTag('Programs');
        // Cache::forget('seting_program');
        Blade::component('components.guest-layout', 'guest-layout');

        // Gate definitions
        Gate::define('Is_guru', function ($user) {
            return $user->posisi;
        });

        Gate::define('gateAdmin', function ($user) {
            return $user->posisi;
        });

        // Set default paginator theme
        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();




        // Set locale Carbon ke Bahasa Indonesia
        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF-8'); // Untuk format lokal (tergantung dukungan sistem)
        date_default_timezone_set('Asia/Jakarta');


        // Share objek Carbon ke semua view jika perlu
        view()->share('carbon', new Carbon);

        /*
        // Directive Blade untuk Carbon biasa
        Blade::directive('carbon', function ($expression) {
            return "<?php echo \\Carbon\\Carbon::parse($expression); ?>";
        });

        // Directive dengan format custom (misal: d-m-Y)
        Blade::directive('carbonFormat', function ($expression) {
            return "<?php echo \\Carbon\\Carbon::parse($expression)->format('d-m-Y'); ?>";
        });

        // Directive format full Bahasa Indonesia: Senin, 2 September 2025
        Blade::directive('tanggalIndonesia', function ($expression) {
            return "<?php echo \\Carbon\\Carbon::parse($expression)->translatedFormat('l, j F Y'); ?>";
        });

        // Contoh pada View Composer atau Controller Template surat
        View::composer('*', function ($view) {
            $TemplateDokuments = TemplateDokumen::whereIn('kategori', ['Edaran Atau Pemberitahuan', 'Undangan'])->orderBy('kategori', 'ASC')->get();

            $Templateform = $TemplateDokuments->pluck('nama_dokumen')->toArray();

            $FormOptions = $TemplateDokuments->pluck('name_input', 'nama_dokumen')
                ->mapWithKeys(fn($json, $key) => [\Illuminate\Support\Str::slug($key) => json_decode($json, true)])
                ->toArray();

            $TemplateContent = $TemplateDokuments->pluck('content', 'nama_dokumen')
                ->mapWithKeys(fn($content, $key) => [\Illuminate\Support\Str::slug($key) => $content])
                ->toArray();

            $view->with(compact('Templateform', 'FormOptions', 'TemplateContent'));
        });
        */
    }
}

// {{-- Tanggal default --}}
// @carbon($data->created_at)

// {{-- Format Indonesia --}}
// @tanggalIndonesia($data->tanggal)

// {{-- Format khusus --}}
// @carbonFormat($data->updated_at)
/*

Cache::forget('siswas_global'); // Mengapus seluruh chace siswa tidak dalam tag ( Ini hanya data chace siswa )
Cache::tags(['siswa'])->forget('siswas_global');

Cache::tags(['siswas'])->flush(); // Ini mengapus seluruh data chace yang sama tagnya jadi ini bisa dikatakan kelompok data chace ( Siswa, Osis, dll)

Cache::tags(['siswa'])->remember('siswas_global', ...);
Cache::tags(['siswa'])->remember('chart_siswa', ...);
Cache::tags(['siswa'])->remember('dropdown_kelas', ...);
Cache::tags(['siswas'])->flush()


$key = 'userDataGurunamachace' . $data->id;
    Cache::tags(['userDataGurunamachace'])->forget($key);
    Cache::tags(['userDataGurunamachace'])->put($userDataGurunamachace, $userDataGurunamachace->fresh(), 3600); // Pastikan fresh


Skema
$variabel = Cache::remember('namacache', now()->addHour(), function () {
    return Model::query()->get(); // atau Model::with(...)->where(...)->get()
});

$variabel = Cache::tags(['namatag'])->remember('namacache', now()->addHour(), function () {
    return Model::query()->get();
});


Contoh :

$siswas = Cache::tags(['siswa'])->remember('siswas_global', now()->addHour(), function () {
        return Detailsiswa::with('KelasOne')
            ->whereNotNull('kelas_id')
            ->orderBy('kelas_id')
            ->orderBy('nama_siswa')
            ->get();
    });


*/