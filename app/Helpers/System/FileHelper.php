<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Whatsapp\WhatsApp;
use App\Models\User\Guru\Detailguru;
use Illuminate\Support\Facades\Auth;
use App\Models\User\Siswa\Detailsiswa;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/*
        |--------------------------------------------------------------------------
        | 📌 FileHelper :
        |--------------------------------------------------------------------------
        |
        | Fitur :
        | - Menggandakan file di temp ke folder tertentu
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
if (!function_exists('CopyFileWa')) {
    /**
     * Menyalin file dari public/temp ke whatsapp/uploads (di luar public)
     *
     * @param string $namaFile Nama file (contoh: sertifikat.pdf)
     * @return array ['status' => 'success|error', 'message' => string]
     */
    function CopyFileWa(string $namaFile, $folder = 'temp'): array
    {
        $sourcePath = public_path($folder . '/' . $namaFile);
        $targetPath = base_path('whatsapp/uploads/' . $namaFile);

        if (!file_exists($sourcePath)) {
            return ['status' => 'error', 'message' => "File '$namaFile' tidak ditemukan di public/temp"];
        }

        if (!is_dir(dirname($targetPath))) {
            mkdir(dirname($targetPath), 0775, true);
        }

        if (!copy($sourcePath, $targetPath)) {
            return ['status' => 'error', 'message' => 'Gagal menyalin file'];
        }

        return ['status' => 'success', 'message' => "File berhasil disalin ke whatsapp/uploads/$namaFile"];
    }
}
/*
Skenario file public/temp dicopy ke whatsapp/uploads
skenario lanjutan kirim ke no tujuan
$namaFile = 'fileinjec';
        $hasil = CopyFileWa($namaFile . '.pdf');
        $pdf_to_jpg = pdf_to_image_wa($namaFile); // => 'abc123'
*/

/*
    |--------------------------------------------------------------------------
    | 📌 UplaodFile :
    |--------------------------------------------------------------------------
    |
    | Fitur :
    | - Mengatur tempat upload file
    | - xxxxxxxxxxx
    |
    | Tujuan :
    | - Memudahkan untuk upload file
    |
    |
    | Penggunaan :
    | - xxxxxxxxxxx
    |
    $filename = 'rapor_' . time() . '.' . $file->getClientOriginalExtension();
    $path = upload_file_to($file, $filename, 'uploads/rapor');
    */
// Proses Coding

if (!function_exists('UploadFiles')) {
    /**
     * Upload file ke folder tertentu dengan nama custom
     *
     * @param UploadedFile $file
     * @param string $filename Nama file (termasuk extension)
     * @param string $folder Path folder relatif dari storage/app/public
     * @return string Path lengkap yang disimpan
     */
    function UploadFiles(UploadedFile $file, string $filename, string $folder = 'uploads'): string
    {
        $path = $folder . '/' . $filename;
        $file->storeAs('public/' . $folder, $filename);
        return 'storage/' . $path;
    }
}
