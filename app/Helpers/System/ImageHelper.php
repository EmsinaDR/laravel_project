<?php

use Carbon\Carbon;
use App\Models\Whatsapp\WhatsApp;
use Carbon\CarbonPeriod;
use App\Models\User\Guru\Detailguru;
use Illuminate\Support\Facades\Auth;
use App\Models\User\Siswa\Detailsiswa;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;
use Intervention\Image\ImageManagerStatic as Image;

/*
|--------------------------------------------------------------------------
| 📌 Image Helpers :
|--------------------------------------------------------------------------
|
| Kumpulan fungsi bantu (helper) terkait proses gambar
| untuk digunakan di seluruh sistem Laravel.
|
*/

/*
|--------------------------------------------------------------------------
| 📌 image_to_base64()
|--------------------------------------------------------------------------
|
| Fitur :
| - Mengubah file gambar lokal menjadi base64 string.
|
| Tujuan :
| - Mempermudah penyisipan gambar ke dalam DOMPDF, inline SVG, dll.
|
| Penggunaan :
| - $path = public_path('img/qrcode/siswa/' . $siswa->nis . '.png');
| - $base64 = image_to_base64($path);
|
*/

if (!function_exists('image_to_base64')) {
    function image_to_base64($path)
    {
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        return ''; // fallback jika file tidak ditemukan
    }
}

/*
|--------------------------------------------------------------------------
| 📌 resize_image()
|--------------------------------------------------------------------------
|
| Fitur :
| - Resize gambar lokal menggunakan package Intervention/Image.
| - Bisa langsung return data URL base64 atau menyimpan hasil resize.
|
| Tujuan :
| - Menyesuaikan ukuran gambar untuk efisiensi cetak/tampilan.
|
| Penggunaan :
| - $img = resize_image('img/foto.jpg', 300, 300);
| - echo '<img src="' . $img . '">';
|
*/
if (!function_exists('resize_image')) {
    function resize_image(string $path, int $width, int $height, bool $save = false, string $outputPath = null)
    {
        $img = \Intervention\Image\ImageManagerStatic::make($path)->resize($width, $height);

        if ($save && $outputPath) {
            $img->save($outputPath);
        }

        return $img->encode('data-url'); // base64 untuk <img src="...">
    }
}

/*
|--------------------------------------------------------------------------
| 📌 download_image_url()
|--------------------------------------------------------------------------
|
| Fitur :
| - Mengunduh gambar dari URL dan menyimpannya ke path lokal.
|
| Tujuan :
| - Otomatisasi pengambilan gambar dari sumber luar (misal: avatar).
|
| Penggunaan :
| - download_image_url('https://example.com/foto.jpg', storage_path('app/public/foto.jpg'));
|
*/
if (!function_exists('download_image_url')) {
    function download_image_url(string $url, string $saveToPath): bool
    {
        $image = file_get_contents($url);
        return file_put_contents($saveToPath, $image) !== false;
    }
}

/*
|--------------------------------------------------------------------------
| 📌 is_valid_image()
|--------------------------------------------------------------------------
|
| Fitur :
| - Mengecek apakah file yang diberikan adalah gambar valid.
|
| Tujuan :
| - Validasi sebelum proses upload, manipulasi, atau encode.
|
| Penggunaan :
| - if (is_valid_image($file)) { ... }
|
*/
if (!function_exists('is_valid_image')) {
    function is_valid_image($file): bool
    {
        $mime = mime_content_type($file);
        return str_starts_with($mime, 'image/');
    }
}

/*
|--------------------------------------------------------------------------
| 📌 generate_svg_base64()
|--------------------------------------------------------------------------
|
| Fitur :
| - Mengubah string SVG menjadi base64 data URL.
|
| Tujuan :
| - Embed SVG inline ke dalam HTML atau PDF.
|
| Penggunaan :
| - $svg = '<svg>...</svg>';
| - echo '<img src="' . generate_svg_base64($svg) . '">';
|
*/
if (!function_exists('generate_svg_base64')) {
    function generate_svg_base64(string $svg): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}

/*
|--------------------------------------------------------------------------
| 📌 storage_image_url()
|--------------------------------------------------------------------------
|
| Fitur :
| - Menghasilkan URL dari gambar yang disimpan di Laravel Storage.
|
| Tujuan :
| - Mengakses gambar dari disk 'public' dengan mudah.
|
| Penggunaan :
| - $url = storage_image_url('foto/siswa123.jpg');
|
*/
if (!function_exists('storage_image_url')) {
    function storage_image_url(string $path): string
    {
        return Storage::url($path); // pastikan `php artisan storage:link` sudah dijalankan
    }
}