<?php

use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

if (!function_exists('DomExport')) {
    /**
     * Mengekspor view ke file PDF dan menyimpannya.
     *
     * @param string $filename Nama file PDF (tanpa .pdf)
     * @param array $data Data yang dikirim ke view
     * @param string $view Nama view Blade
     * @param string|null $folder Lokasi penyimpanan (default: base_path('whatsapp/uploads'))
     * @return string Path file yang dihasilkan
     */
    function DomExport($filename, $data, $view, $folder = null)
    {
        // 📂 Lokasi simpan PDF
        $folder = $folder ?? base_path('whatsapp/uploads');
        $filepath = $folder . '/' . $filename . '.pdf';

        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }
        $pdf = Pdf::loadView($view, $data)->setOptions(['isRemoteEnabled' => true]);
        $pdf->save($filepath);
        return $filepath;
    }
}

if (!function_exists('DomExportFile')) {
    /**
     * Mengekspor view ke file PDF dan mengatur cara outputnya.
     *
     * @param string $filename Nama file PDF (tanpa .pdf)
     * @param array $data Data yang dikirim ke view
     * @param string $view Nama view Blade
     * @param string $mode Mode output PDF:
     *     - 'save'     : Simpan ke folder lokal (default: base_path('whatsapp/uploads'))
     *     - 'download' : Download langsung oleh user
     *     - 'stream'   : Tampilkan PDF di browser (preview)
     *     - 'storage'  : Simpan ke Laravel Storage (default: disk 'local')
     * @param string|null $folder Lokasi penyimpanan jika mode 'save'
     * @param string|null $disk Disk Laravel jika mode 'storage'
     * @return mixed Path file (string) untuk mode 'save' / 'storage', atau response download/stream (StreamedResponse)
     *
     * 📘 Contoh Pemakaian:
     *  view = pdf.siswa
     * // Simpan ke folder lokal
     * $path = DomExportFile('Laporan-Siswa', $data, 'pdf.siswa');
     *
     * // Download langsung ke browser
     * return DomExportFile('Laporan-Siswa', $data, 'pdf.siswa', 'download');
     *
     * // Stream / tampilkan langsung di browser
     * return DomExportFile('Laporan-Siswa', $data, 'pdf.siswa', 'stream');
     *
     * // Simpan ke storage Laravel default ('local')
     * $path = DomExportFile('Laporan-Siswa', $data, 'pdf.siswa', 'storage');
     *
     * // Simpan ke storage disk 'public'
     * $url = Storage::disk('public')->url(
     *     DomExportFile('Laporan-Siswa', $data, 'pdf.siswa', 'storage', null, 'public')
     * );
     */

    function DomExportFile($filename, $data, $view, $mode = 'save', $folder = null, $disk = 'local')
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $data)->setOptions(['isRemoteEnabled' => true]);

        switch ($mode) {
            case 'download':
                return $pdf->download($filename . '.pdf');

            case 'stream':
                return $pdf->stream($filename . '.pdf');

            case 'storage':
                $output = $pdf->output();
                $storagePath = 'laporan/' . $filename . '.pdf';
                \Illuminate\Support\Facades\Storage::disk($disk)->put($storagePath, $output);
                return $storagePath;

            case 'save':
            default:
                $folder = $folder ?? base_path('whatsapp/uploads');
                if (!\Illuminate\Support\Facades\File::exists($folder)) {
                    \Illuminate\Support\Facades\File::makeDirectory($folder, 0755, true);
                }
                $filepath = $folder . '/' . $filename . '.pdf';
                $pdf->save($filepath);
                return $filepath;
        }
    }
}
/*
    |--------------------------------------------------------------------------
    | 📌 PdfToImage :
    |--------------------------------------------------------------------------
    |
    | Fitur :
    | - Merubah Pdf Ke Image untuk kebutuhan surat yang 1 file / Halaman
    | - xxxxxxxxxxx
    |
    | Tujuan :
    | - Memudahkan pengguna dalam mengkonversi pdf ke image
    | - Tujuan pengembangan konveri pdf to image yang dilanjutkan kirim ke whatsapp pengguna / group
    |
    |
    | Penggunaan :
    | - $namaFile = 'sertifikat'; //sertifikat.pdf
    | - $pdf_to_jpg = pdf_to_image_wa($namaFile); // => 'abc123'
    |
    */
// Proses Coding
if (!function_exists('pdf_to_image_wa')) {
    /**
     * Mengonversi file PDF (halaman pertama) menjadi JPG di folder whatsapp/uploads
     * Perlu Instalasi Imagick Exe
     * @param string $namaFileTanpaExt Nama file tanpa ekstensi (misal: 'abc123')
     * @return string Nama file tanpa ekstensi
     * Cara pakai :
     *
     *
     */
    function pdf_to_image_wa(string $namaFileTanpaExt): string
    {
        $basePath     = base_path('whatsapp/uploads');
        $magickPath   = '"C:\Program Files\ImageMagick-7.1.2-Q16-HDRI\magick.exe"';
        $pdfPath      = $basePath . DIRECTORY_SEPARATOR . $namaFileTanpaExt . '.pdf';
        $outputPath   = $basePath . DIRECTORY_SEPARATOR . $namaFileTanpaExt . '.jpg';

        // Buat perintah convert PDF ke JPG (halaman pertama [0])
        $command = "$magickPath -density 150 \"$pdfPath\"[0] -quality 100 \"$outputPath\"";

        exec($command, $output, $returnVar);

        // Bisa tambahin pengecekan sukses kalau perlu

        return $namaFileTanpaExt;
    }
}
