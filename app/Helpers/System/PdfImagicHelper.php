<?php
use imagick;
use Spatie\PdfToImage\Pdf;
/*
    |--------------------------------------------------------------------------
    | 📌 Imagick Instalasi :
    |--------------------------------------------------------------------------
    |
    | Fitur :
    | - Download Imagick 7.1.2.Q16-HDRI di : https://imagemagick.org/script/download.php#windows
    | ✅ Install legacy utilities (convert, identify, etc)
    | ✅ Add to system PATH
    | - Download dan Extrak : php_imagick-3.7.0-8.1-ts-vs16-x64.zip di https://windows.php.net/downloads/pecl/releases/imagick/3.7.0/
    | - Root Ekstrak E:\laragon\bin\php\php-8.3.10-Win32-vs16-x64\ext
    |
    | Tujuan :
    | - Membuat Conversi pdf to image
    |
    |
    | Penggunaan :
    | - xxxxxxxxxxx
    |
    */
// Proses Coding

if (!function_exists('pdfToImageWa')) {
    function pdfToImageWa($namafile)
    {
        // Pastikan path ImageMagick sesuai
        $magickPath = escapeshellarg("C:\\Program Files\\ImageMagick-7.1.2-Q16-HDRI\\magick.exe");

        // Path file PDF (ambil halaman pertama aja [0])
        $pdfPath = escapeshellarg(base_path("Whatsapp/Uploads/{$namafile}.pdf") . '[0]');

        // Output path untuk JPEG
        $outputPath = escapeshellarg(base_path("Whatsapp/Uploads/{$namafile}.jpg"));

        // Command ImageMagick
        $command = "$magickPath -density 150 $pdfPath -quality 100 $outputPath";

        // Eksekusi command dan ambil outputnya kalau error
        exec($command . ' 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            logger()->error("Gagal convert PDF ke gambar", ['output' => $output]);
            return false;
        }

        return $namafile; // Sukses, balikin nama file
    }
}
