<?php

use App\Models\Admin\Identitas;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Whatsapp\WhatsApp;
use App\Models\User\Guru\Detailguru;
use Illuminate\Support\Facades\Auth;
use App\Models\User\Siswa\Detailsiswa;

/*
        |--------------------------------------------------------------------------
        | 📌 PesanWaHelper :
        |--------------------------------------------------------------------------
        |
        | Fitur :
        | - xxxxxxxxxxx
        | - xxxxxxxxxxx
        |
        | Tujuan :
        | - xxxxxxxxxxx
        |
        | Penggunaan :
        | - xxxxxxxxxxx
        |
    */

// Proses Coding
if (!function_exists('PesanWaHelper')) {
    function PesanWaHelper()
    {
        // TODO: Implement your helper logic here
    }
}
if (!function_exists('PesanUmumSuratSiswa')) {
    function PesanUmumSuratSiswa($nis, $Kode)
    {
        $Identitas = Identitas::first();
        $Siswa = \App\Models\User\Siswa\Detailsiswa::with('KelasOne')->where('nis', $nis)->first();
        $kelas = $Siswa->KelasOne->kelas;
        $pesanKiriman =
            "==================================\n" .
            "📌 *Data $Kode*\n" .
            "==================================\n\n" .
            "📝 Nama\t\t\t: $Siswa->nama_siswa\n" .
            "🏫 Kelas\t\t\t: $kelas\n" .
            "📅 Tanggal\t\t: " . Carbon::now()->translatedFormat('d F Y') . "\n\n" .
            "📎 Pembuatan dan pengiriman {$Kode} sedang berjalan, Mohon tunggu sejenak\n" .
            str_repeat("─", 25) . "\n" .
            "✍️ Dikirim oleh :\n" .
            "*Bot Asisten - {$Identitas->namasek}* \n";
        sleep(rand(1, 3));
        return $pesanKiriman;
    }
}
