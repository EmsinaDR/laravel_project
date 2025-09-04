<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;

// Cek dulu, apakah fungsi 'tgl' sudah didefinisikan sebelumnya atau belum
if (!function_exists('tgl')) {

    // Kalau belum ada, kita buat fungsi 'tgl' yang menerima dua parameter:
    // $tanggal: tanggal input yang mau diformat (string atau DateTime)
    // $format: format output tanggal, default 'Y-m-d' (tahun-bulan-tanggal)
    function tgl($tanggal, $format = 'Y-m-d')
    {

        // Gunakan Carbon untuk membuat objek tanggal dari input $tanggal
        // lalu format tanggal tersebut sesuai format yang diminta
        // translatedFormat mengembalikan tanggal dengan nama bulan/hari yang sudah diterjemahkan ke bahasa lokal (misal bahasa Indonesia)
        // tgl($data->tanggal, 'l, Y m d');
        return Carbon::create($tanggal)->translatedFormat($format);
    }
}
function tglall($tanggal, $preset = 'default')
{
    if (!$tanggal) return '-';

    $format = match ($preset) {
        'default' => 'Y-m-d',
        'indo'    => 'd F Y',
        'long'    => 'l, d F Y',
        'short'   => 'd/m/Y',
        default   => $preset,
    };

    $formatted = Carbon::create($tanggal)->translatedFormat($format);

    // Ubah agar huruf pertama tiap kata kapital (khususnya untuk bulan dan hari)
    return ucwords($formatted);
}

// Cek dulu, apakah fungsi 'strpad' sudah ada atau belum supaya gak error fungsi ganda
if (!function_exists('strpad')) {
    /**
     * Fungsi untuk menambahkan padding nol di depan angka supaya panjang string sesuai kebutuhan.
     *
     * @param int|string $angka Angka asli yang mau dipadding
     * @param int $length Panjang total string hasil padding, default 3
     * @return string Angka yang sudah dipadding nol di depan
     */
    function strpad($angka, $length = 3)
    {
        // Gunakan fungsi bawaan PHP str_pad untuk menambahkan karakter '0' di kiri (STR_PAD_LEFT)
        return str_pad($angka, $length, '0', STR_PAD_LEFT);
    }
}
if (!function_exists('umur')) {
    /**
     * Menghitung umur dari tanggal lahir.
     *
     * @param string|Carbon $tanggal_lahir
     * @return int|null
     */
    function umur($tanggal_lahir)
    {
        if (!$tanggal_lahir) return null;

        return Carbon::parse($tanggal_lahir)->age;
    }
}

// vai get
if (!function_exists('cleanPesanWAGet')) {
    function cleanPesanWAGet($html)
    {
        // Ubah <p> jadi new line biar rapi
        $text = preg_replace('/<\/?p[^>]*>/', "\n", $html);

        // Ubah <strong> jadi bold WA pakai *
        $text = preg_replace('/<strong>(.*?)<\/strong>/i', '*$1*', $text);

        // Hapus tag HTML lain
        $text = strip_tags($text);

        // Trim, replace multi-newline jadi 1 newline
        $text = preg_replace('/\n{2,}/', "\n", $text);

        // URL encode
        return urlencode(trim($text));
    }
}
/*
    |--------------------------------------------------------------------------
    | 📌 Helper generate tanggal sesuai interval :
    |--------------------------------------------------------------------------
    |

    | Tujuan :
    | - Pembuatan array tanggal dengan generator sesuai ketentuan
    | - Membuat tanggal dari tanggal mulai sampai akhir tetapi mengambil hari tertentu saja
    |
    |
    */
// Proses Coding
if (!function_exists('generateTanggalJamByHari')) {
    /**
     * Membuat array tanggal + jam berdasarkan hari tertentu dalam rentang tanggal.
     *
     * @param string $tanggalMulai Format 'Y-m-d', misal: '2025-07-25'
     * @param string $tanggalAkhir Format 'Y-m-d', misal: '2025-10-25'
     * @param int $hariTarget Gunakan konstanta Carbon::MONDAY, Carbon::TUESDAY, dll.
     * @param string $jamTetap Format 'H:i:s', misal: '08:00:00'
     * @param int|null $batasJumlah (Opsional) Ambil hanya sebanyak jumlah ini, jika diberikan
     * int $batasJumlah = null = data
     * Carbon::SUNDAY    = 0 // Minggu
     * Carbon::MONDAY    = 1
     * Carbon::TUESDAY   = 2
     * Carbon::WEDNESDAY = 3
     * Carbon::THURSDAY  = 4
     * Carbon::FRIDAY    = 5
     * Carbon::SATURDAY  = 6 // Sabtu
     * Contoh penggunaan : generateTanggalJamByHari('2025-08-01', '2025-09-30', 1, '08:00:00', 3); // Akan ambil 3 Senin pertama saja.
     * Contoh penggunaan : $hasil = generateTanggalJamByHari('2025-08-01', '2025-08-31', [0, 1], '08:00:00'); // Minggu dan Senin
     * Contoh penggunaan : generateTanggalJamByHari('2025-08-01', '2025-09-30', 1) // Ambil semua tanggal dan yang dipilih hari senin, jam default
     * @return array Array berisi tanggal lengkap (Y-m-d H:i:s) pada hari tertentu
     */
    function generateTanggalJamByHari(
        string $tanggalMulai,
        string $tanggalAkhir,
        int|array $hariTarget,
        string $jamTetap = '08:00:00',
        int $batasJumlah = null
    ): array {
        // Konversi tanggal string ke objek Carbon
        $startDate = Carbon::createFromFormat('Y-m-d', $tanggalMulai);
        $endDate   = Carbon::createFromFormat('Y-m-d', $tanggalAkhir);

        // Buat periode tanggal dari tanggalMulai sampai tanggalAkhir
        $period = CarbonPeriod::create($startDate, $endDate);

        // Array penampung hasil akhir
        $result = [];

        // Jika hariTarget bukan array, jadikan array agar konsisten
        $hariTarget = is_array($hariTarget) ? $hariTarget : [$hariTarget];

        // Loop tiap tanggal dalam periode
        foreach ($period as $tanggal) {
            // Cek apakah hari dari tanggal saat ini termasuk dalam hariTarget
            if (in_array($tanggal->dayOfWeek, $hariTarget)) {
                // Salin tanggal, set jam tetap, lalu simpan dalam format datetime string
                $result[] = $tanggal->copy()->setTimeFromTimeString($jamTetap)->toDateTimeString();
            }
        }

        // Jika batas jumlah ditentukan, potong array hasil
        return $batasJumlah ? array_slice($result, 0, $batasJumlah) : $result;
    }
}

/*
    |--------------------------------------------------------------------------
    | 📌 Data dan Time :
    |--------------------------------------------------------------------------
    |
    |
    | Tujuan :
    | - Menggabungkan tanggal dan waktu
    | - xxxxxxxxxxx
    | - xxxxxxxxxxx
    |
    |
    | Penggunaan :
    | - $jadwal = TglWkatu('2025-07-27', '08:30');
    |
    */
// Proses Coding
if (!function_exists('TglWkatu')) {
    function TglWkatu($tanggal, $waktu)
    {
        try {
            $scheduled_at = Carbon::createFromFormat('Y-m-d H:i', "$tanggal $waktu");
            return $scheduled_at->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null; // atau bisa lempar error/log
        }
    }
}
/*
    |--------------------------------------------------------------------------
    | 📌 Ckear Data No HP :
    |--------------------------------------------------------------------------
    |
    | Fitur :
    | - xxxxxxxxxxx
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
if (!function_exists('format_no_hp')) {
    function format_no_hp(string $nomor): string
    {
        // Hilangkan semua karakter kecuali angka dan plus
        $nomor = preg_replace('/[^0-9\+]/', '', $nomor);

        // Jika diawali dengan '00' ubah jadi '+'
        if (substr($nomor, 0, 2) === '00') {
            $nomor = '+' . substr($nomor, 2);
        }

        // Hilangkan tanda plus
        $nomor = ltrim($nomor, '+');

        // Jika diawali 0, ganti jadi 62
        if (substr($nomor, 0, 1) === '0') {
            $nomor = '62' . substr($nomor, 1);
        }

        return $nomor;
    }
}

/*
    |--------------------------------------------------------------------------
    | 📌 Format Data Tanggal :
    |--------------------------------------------------------------------------
    |
    | Fitur :
    | - xxxxxxxxxxx
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
if (!function_exists('format_tanggal_lahir')) {
    function format_tanggal_lahir(string $tanggal): ?string
    {
        // Bersihkan karakter aneh
        $tanggal = trim($tanggal, "'\" ");

        // Format sudah benar?
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            return $tanggal; // sudah format yyyy-mm-dd
        }

        // Deteksi format dd/mm/yyyy
        $parts = explode('/', $tanggal);
        if (count($parts) === 3) {
            [$dd, $mm, $yyyy] = $parts;

            if (checkdate((int)$mm, (int)$dd, (int)$yyyy)) {
                return sprintf('%04d-%02d-%02d', $yyyy, $mm, $dd);
            }
        }

        return null;
    }
}

/*
    |--------------------------------------------------------------------------
    | 📌 Kecilkan Nama yang ada titlenya :
    |--------------------------------------------------------------------------
    |
    | Fitur :
    | - xxxxxxxxxxx
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
if (!function_exists('ucwords_nama_dengan_gelar')) {
    function ucwords_nama_dengan_gelar(string $namaLengkap): string
    {
        $parts = explode(',', $namaLengkap, 2); // Pisahkan berdasarkan koma pertama
        $nama = isset($parts[0]) ? ucwords(strtolower(trim($parts[0]))) : '';
        $gelar = isset($parts[1]) ? ',' . trim($parts[1]) : '';

        return $nama . $gelar;
    }
}
if (!function_exists('singkatkan_nama')) {
    function singkatkan_nama($namaLengkap)
    {
        // Pisahkan antara nama dan gelar (asumsi gelar dimulai setelah koma atau kapital titik-titik)
        // Contoh: "Ameliya Nur Hikmah A.md.ds" atau "Ameliya Nur Hikmah, S.Pd"

        // Pisahkan gelar dari nama utama
        $namaGelar = preg_split('/[,]/', $namaLengkap, 2); // misah pakai koma
        $namaUtama = trim($namaGelar[0]);
        $gelar = isset($namaGelar[1]) ? trim($namaGelar[1]) : '';

        // Atau kalau nggak ada koma, cari potensi gelar (kata berisi titik)
        if (!$gelar && preg_match_all('/\b[\w]+\.[\w\.]*/i', $namaLengkap, $matches)) {
            $gelar = implode(' ', $matches[0]);
            // Hapus gelar dari nama utama
            $namaUtama = trim(str_replace($gelar, '', $namaLengkap));
        }

        // Pisah nama utama jadi array
        $parts = explode(' ', $namaUtama);
        $jumlah = count($parts);

        if ($jumlah > 2) {
            // Ambil 2 pertama, dan inisial dari sisanya
            $namaPendek = $parts[0] . ' ' . $parts[1];

            for ($i = 2; $i < $jumlah; $i++) {
                $namaPendek .= ' ' . strtoupper(substr($parts[$i], 0, 1)) . '.';
            }
        } else {
            $namaPendek = $namaUtama;
        }

        return trim($namaPendek . ($gelar ? ' ' . $gelar : ''));
    }
}

/*
    |--------------------------------------------------------------------------
    | 📌 IsDay :
    |--------------------------------------------------------------------------
    |
    | Fitur :
    | - Mengecek hari ini hari apa
    | - xxxxxxxxxxx
    |
    | Tujuan :
    | - Mengecek hari ini hari apa jika iya akan proses eksekusi
    |
    |
    | Penggunaan :
    | - xxxxxxxxxxx
    |
    | is_day('sabtu');               // true kalau hari ini Sabtu
    | is_day('saturday');            // sama, tapi pakai Inggris
    | is_day('Rabu', '2025-08-06');  // true
    | is_day('jumat', '2025-08-08'); // true
    | is_day('jum\'at', '2025-08-08'); // juga true

    if (is_day('minggu')) {
        echo "Libur, Bro!";
    }
    */
// Proses Coding
if (!function_exists('is_day')) {
    /**
     * Cek apakah hari dari tanggal (atau hari ini) adalah hari tertentu
     *
     * @param string $hari      Nama hari (dalam Inggris atau Indonesia)
     * @param string|null $tanggal Tanggal (optional), default hari ini
     * @return bool
     */
    function is_day(string $hari, string $tanggal = null): bool
    {
        // Pemetaan nama hari ke format Carbon
        $mapHari = [
            'minggu'    => 'sunday',
            'senin'     => 'monday',
            'selasa'    => 'tuesday',
            'rabu'      => 'wednesday',
            'kamis'     => 'thursday',
            'jumat'     => 'friday',
            'jum\'at'   => 'friday',
            'sabtu'     => 'saturday',

            'sunday'    => 'sunday',
            'monday'    => 'monday',
            'tuesday'   => 'tuesday',
            'wednesday' => 'wednesday',
            'thursday'  => 'thursday',
            'friday'    => 'friday',
            'saturday'  => 'saturday',
        ];

        $hari = strtolower(trim($hari));
        $cariHari = $mapHari[$hari] ?? null;

        if (!$cariHari) return false;

        $tanggal = $tanggal ? Carbon::parse($tanggal) : Carbon::now();
        return strtolower($tanggal->format('l')) === $cariHari;
    }
}

function bulanRomawi($bulan)
{
    $romawi = [
        'I',
        'II',
        'III',
        'IV',
        'V',
        'VI',
        'VII',
        'VIII',
        'IX',
        'X',
        'XI',
        'XII'
    ];

    // Ubah string bulan ke integer (biar aman)
    $index = intval($bulan) - 1;

    return $romawi[$index] ?? '';
}
