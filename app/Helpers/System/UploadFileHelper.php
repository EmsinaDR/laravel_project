<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Whatsapp\WhatsApp;
use App\Models\User\Guru\Detailguru;
use Illuminate\Support\Facades\Auth;
use App\Models\User\Siswa\Detailsiswa;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\File;
/*
        |--------------------------------------------------------------------------
        | 📌 UploadFileHelper :
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

if (!function_exists('upload_file')) {
    /**
     * Upload file ke direktori tujuan
     *
     * @param string $inputName Nama input file dari form (ex: 'gambar')
     * @param string $targetDir Folder tujuan upload (ex: 'uploads/')
     * @param array $allowedTypes Tipe file yang diizinkan (ex: ['jpg', 'png'])
     * @param int $maxSize Ukuran maksimal file dalam bytes (ex: 2MB = 2*1024*1024)
     * @return array Hasil ['success' => bool, 'message' => string, 'filename' => string|null]
     * Cara penggunaan : $result = upload_file('gambar', 'uploads/', ['jpg', 'jpeg', 'png', 'webp'], 2 * 1024 * 1024);
     */
    function upload_file(string $inputName, string $targetDir, array $allowedTypes = [], int $maxSize = 2097152): array
    {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Tidak ada file yang diupload atau terjadi error.', 'filename' => null];
        }

        $file = $_FILES[$inputName];
        $fileName = basename($file['name']);
        $fileSize = $file['size'];
        $fileTmp = $file['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validasi tipe file
        if (!empty($allowedTypes) && !in_array($fileExt, $allowedTypes)) {
            return ['success' => false, 'message' => 'Tipe file tidak diizinkan.', 'filename' => null];
        }

        // Validasi ukuran file
        if ($fileSize > $maxSize) {
            return ['success' => false, 'message' => 'Ukuran file terlalu besar.', 'filename' => null];
        }

        // Pastikan folder tujuan ada
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                return ['success' => false, 'message' => 'Gagal membuat folder upload.', 'filename' => null];
            }
        }

        // Bikin nama unik
        $newFileName = uniqid('upload_', true) . '.' . $fileExt;
        $destination = rtrim($targetDir, '/') . '/' . $newFileName;

        if (move_uploaded_file($fileTmp, $destination)) {
            return ['success' => true, 'message' => 'File berhasil diupload.', 'filename' => $newFileName];
        }

        return ['success' => false, 'message' => 'Gagal memindahkan file.', 'filename' => null];
    }
}
if (!function_exists('UploadDataExcel')) {
    /**
     * Upload file Excel ke folder sementara dan parse jadi array
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array ['success' => bool, 'message' => string, 'data' => array|null]
     */
    function UploadDataExcel($file): array
    {
        try {
            $fileName = $file->getClientOriginalName();
            $targetPath = public_path('temp/') . $fileName;

            // Cek dan hapus file jika sudah ada
            if (File::exists($targetPath)) {
                File::delete($targetPath);
            }

            // Pindahkan file ke direktori sementara
            $file->move(public_path('temp/'), $fileName);

            // Baca file Excel
            $spreadsheet = IOFactory::load($targetPath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true); // hasil array, huruf kolom sebagai key

            return [
                'success' => true,
                'message' => 'Berhasil membaca file Excel.',
                'data' => $data
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Gagal memproses file Excel: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}