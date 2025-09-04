<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Whatsapp\WhatsApp;
use App\Models\User\Guru\Detailguru;
use Illuminate\Support\Facades\Auth;
use App\Models\User\Siswa\Detailsiswa;

/*
        |--------------------------------------------------------------------------
        | 📌 SvgHelper :
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
if (!function_exists('wrap_text_to_tspans')) {
    function wrap_text_to_tspans($text, $maxChars = 42, $x = 13418.62, $lineHeight = 750)
    {
        $lines = wordwrap($text, $maxChars, "\n", true);
        $lines = explode("\n", $lines);
        $tspans = '';
        foreach ($lines as $i => $line) {
            $dy = $i === 0 ? 0 : $lineHeight;
            $bullet = $i === 0 ? '• ' : '  ';
            $tspans .= '<tspan x="' . $x . '" dy="' . $dy . '">' . $bullet . htmlspecialchars($line) . '</tspan>' . "\n";
        }
        return [$tspans, count($lines)];
    }
}
if (!function_exists('generate_personalized_svg')) {
    function generate_personalized_svg($data)
    {
        $templatePath = public_path('img/template/cocard/' . $data->kode . '.svg');

        if (!file_exists($templatePath)) {
            return null;
        }

        $svg = file_get_contents($templatePath);

        // Ganti placeholder
        $replacements = [
            '{nama}'       => $data->nama,
            '{kode}'       => $data->kode,
            '{kelas}'      => $data->kelas ?? '',
            '{keterangan}' => $data->keterangan ?? '',
            // Tambahkan jika perlu
        ];

        foreach ($replacements as $key => $value) {
            $svg = str_replace($key, $value, $svg);
        }

        // Buat folder jika belum ada
        $outputDir = storage_path('app/public/tmp');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        // Auto hapus file lama (lebih dari 5 menit)
        foreach (glob($outputDir . '/cocard-*.svg') as $file) {
            if (filemtime($file) < time() - 300) {
                unlink($file);
            }
        }

        // Simpan hasil SVG ke file baru
        $outputPath = $outputDir . '/cocard-' . $data->kode . '.svg';
        file_put_contents($outputPath, $svg);

        // Kembalikan URL yang bisa dipakai di <img>
        return asset('storage/tmp/cocard-' . $data->kode . '.svg');
    }
}
if (!function_exists('render_svg_inline')) {
    function render_svg_inline($kode, $variables = [])
    {
        $templatePath = public_path('img/template/cocard/' . $kode . '.svg');

        if (!file_exists($templatePath)) {
            return '<!-- SVG template not found -->';
        }

        $svg = file_get_contents($templatePath);

        foreach ($variables as $key => $value) {
            $svg = str_replace('{' . $key . '}', e($value), $svg); // `e()` buat amankan isi
        }

        return $svg; // nanti ditampilkan pakai `{!! ... !!}`
    }
}
if (!function_exists('render_svg_base64')) {
    function render_svg_base64($kode, $variables = [])
    {
        $templatePath = public_path('img/template/cocard/' . $kode . '.svg');

        if (!file_exists($templatePath)) {
            return '';
        }

        $svg = file_get_contents($templatePath);

        foreach ($variables as $key => $value) {
            // Cek apakah value adalah path gambar (png, jpg, jpeg, svg)
            if (is_string($value) && preg_match('/\.(png|jpe?g|svg)$/i', $value)) {
                $imagePath = public_path($value);
                if (file_exists($imagePath)) {
                    $mime = mime_content_type($imagePath);
                    $imageData = base64_encode(file_get_contents($imagePath));

                    // Default ukuran & posisi (bisa diganti kalau mau)
                    $value = 'data:' . $mime . ';base64,' . $imageData;
                } else {
                    $value = '<!-- Gambar ' . $key . ' tidak ditemukan -->';
                }
            } else {
                // Teks biasa
                $value = e($value);
            }

            $svg = str_replace('{' . $key . '}', $value, $svg);
        }

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
