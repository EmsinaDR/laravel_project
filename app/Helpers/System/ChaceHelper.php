<?php

use App\Models\User\Guru\Detailguru;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

// Mengambil data guru dan dijadikan format ul>li
if (!function_exists('AmbilCacheDenganTag')) {
    /**
     * Ambil cache berdasarkan tag dan key, jika tidak ada simpan dari closure
     *
     * @param string|array $tag     Nama tag atau array tag
     * @param string $key           Key unik cache
     * @param \DateTimeInterface|\DateInterval|int $ttl TTL waktu kedaluwarsa
     * @param \Closure $callback    Logic jika cache belum ada
     * @return mixed
     */
    function AmbilCacheDenganTag($tag, string $key, $ttl, \Closure $callback)
    {
        return Cache::tags((array)$tag)->remember($key, $ttl, $callback);
    }
}

if (!function_exists('HapusCacheDenganTag')) {
    /**
     * Hapus semua cache berdasarkan tag
     *
     * @param string|array $tag Nama tag
     * @return void
     */
    function HapusCacheDenganTag($tag)
    {
        Cache::tags((array)$tag)->flush();
        Cache::tags(['' . $tag . ''])->flush();
        Cache::tags(['Cache_' . $tag])->forget('Remember_' . $tag);
    }
}
if (!function_exists('HapusFile')) {
    /**
     * Menghapus semua isi dari folder di dalam public/
     *
     * @param string $relatifPath Contoh: 'img/qrcode/siswa'
     * @return array ['status' => 'success|error', 'message' => string]
     */
    function HapusFile(string $relatifPath): array
    {
        // Validasi path agar tidak bisa keluar dari direktori public
        if (!preg_match('/^[a-zA-Z0-9_\-\/]+$/', $relatifPath)) {
            return ['status' => 'error', 'message' => 'Path folder tidak valid.'];
        }

        $folderPath = public_path($relatifPath);

        if (!File::exists($folderPath)) {
            return ['status' => 'error', 'message' => "Folder '$relatifPath' tidak ditemukan."];
        }

        // Hapus semua file
        collect(File::allFiles($folderPath))->each(fn($file) => File::delete($file->getPathname()));

        // Hapus semua subfolder (jika ada)
        collect(File::directories($folderPath))->each(fn($dir) => File::deleteDirectory($dir));

        return ['status' => 'success', 'message' => "Isi folder '$relatifPath' berhasil dihapus."];
    }
}
function HapusFolder(string $relativePath): array
{
    $folderPath = base_path($relativePath);

    if (!File::exists($folderPath)) {
        return ['status' => 'error', 'message' => "Folder '$relativePath' tidak ditemukan."];
    }

    $deleted = [];
    $failed = [];

    foreach (File::allFiles($folderPath) as $file) {
        $filePath = $file->getPathname();
        $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
        $filePath = trim($filePath);
        // dd($filePath);
        try {
            if (is_writable($filePath)) {
                if (unlink($filePath)) {
                    $deleted[] = $filePath;
                } else {
                    $failed[] = $filePath . ' (unlink gagal)';
                }
            } else {
                $failed[] = $filePath . ' (tidak writable)';
            }
        } catch (\Throwable $e) {
            $failed[] = $filePath . ' (error: ' . $e->getMessage() . ')';
        }
    }

    // Subfolder tetap dihapus kalau kosong
    foreach (File::directories($folderPath) as $dir) {
        try {
            File::deleteDirectory($dir);
        } catch (\Throwable $e) {
            $failed[] = $dir . ' (dir error: ' . $e->getMessage() . ')';
        }
    }

    return [
        'status' => empty($failed) ? 'success' : 'error',
        'message' => 'File terhapus: ' . count($deleted) . ', gagal: ' . count($failed),
        'deleted' => $deleted,
        'failed' => $failed,
    ];
}
