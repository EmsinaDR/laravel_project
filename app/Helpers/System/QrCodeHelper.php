<?php


// use QrCodeHelper;
// use QrCodeHelper;
use App\Models\Admin\Identitas;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;
use App\Models\User\Siswa\Detailsiswa;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\Facades\Image;

if (!function_exists('generateQrSiswa')) {
    function generateQrSiswa($nis, $filename = null, $folder = 'img/qrcode/siswa', $size = 300)
    {
        $filename = $filename ?? $nis . '.png';
        $folderPath = public_path($folder);

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        $fullPath = $folderPath . '/' . $filename;

        QrCode::format('png')
            ->size($size)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($nis, $fullPath);

        return $fullPath;
    }
}
// G ( Generate )
if (!function_exists('QrGen')) {
    function QrGen($judul, $isi, $path = 'img/qrcode/qrgenerator')
    {
        $filename = $judul . '.png';
        $Identitas = Identitas::first();
        $folderPath = public_path($path);
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        $fullPath = $folderPath . '/' . $filename;

        QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($isi, $fullPath);

        return $fullPath;
    }
}
// G ( Generate )
if (!function_exists('GQrNis')) {
    function GQrNis($nis)
    {
        $filename = $nis . '.png';
        $vendor = 'Ata Digital';
        $Identitas = Identitas::first();
        $code = Crypt::encryptString($nis . '|' . $vendor . '|' . $Identitas->nama_sek);
        $folderPath = public_path('img/qrcode/nis');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        $fullPath = $folderPath . '/' . $filename;

        QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($nis, $fullPath);

        return $fullPath;
    }
}
// G ( Generate )
if (!function_exists('GQrNisV')) {
    function GQrNisV($nis)
    {
        $filename = $nis . '.png';
        $vendor = 'Ata Digital';
        $Identitas = Identitas::first();
        $code = Crypt::encryptString($nis . '|' . $vendor . '|' . $Identitas->nama_sek);
        $folderPath = public_path('img/qrcode/nis');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        $fullPath = $folderPath . '/' . $filename;

        QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($code, $fullPath);

        return $fullPath;
    }
}
if (!function_exists('GQrGuru')) {
    function GQrGuru($kode_guru)
    {
        $filename = $kode_guru . '.png';
        $folderPath = public_path('img/qrcode/guru');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }
        $fullPath = $folderPath . '/' . $filename;

        QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($kode_guru, $fullPath);


        return $fullPath;
    }
}
if (!function_exists('GQrBrand')) {
    function GQrBrand($kode_guru)
    {
        $filename = $kode_guru . '.png';
        $vendor = 'Ata Digital';
        $Identitas = Identitas::first();
        $code = Crypt::encryptString($kode_guru . '|' . $vendor . '|' . $Identitas->nama_sek);
        $folderPath = public_path('img/qrcode/guru');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        $fullPath = $folderPath . '/' . $filename;

        QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($code);

        return $fullPath;
    }
}
if (!function_exists('cekDanGenerateQrSiswa')) {
    function cekDanGenerateQrSiswa()
    {
        $folder = public_path('qrcode/qrcode/siswa');
        $folderRelatif = 'qrcode/siswa';
        $size = 300;

        $siswaAktif = Detailsiswa::where('status_siswa', 'aktif')->get();
        $daftarNisAktif = [];

        foreach ($siswaAktif as $siswa) {
            $nis = $siswa->nis;
            $filename = "$nis.png";
            $path = $folder . '/' . $filename;

            $daftarNisAktif[] = $filename;

            // ❗ Generate hanya jika file TIDAK ADA
            if (!File::exists($path)) {
                generateQrSiswa($nis, $filename, $folderRelatif, $size);
            }
        }

        // Hapus QR yang bukan siswa aktif
        $fileQrSiswa = File::files($folder);

        foreach ($fileQrSiswa as $file) {
            $fileName = $file->getFilename();

            if (!in_array($fileName, $daftarNisAktif)) {
                File::delete($file->getRealPath());
            }
        }

        return 'QR code siswa disinkronisasi!';
    }
}
if (!function_exists('generateQrGeneral')) {
    function generateQrGeneral($isi, $filename, $folder = 'img/qrcode', $size = 300)
    {
        $folderPath = public_path($folder);

        if (!File::isDirectory($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        $fullPath = $folderPath . '/' . $filename;

        QrCode::format('png')
            ->size($size)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($isi, $fullPath);

        return $fullPath;
    }
}
if (!function_exists('QrBase64')) {
    /**
     * Menghasilkan QR Code base64 PNG dari string isi.
     *
     * @param string $isi Teks atau URL yang akan di-encode.
     * @param int $size Ukuran QR code (default: 300).
     * @param string $format Format gambar (png/svg).
     * @return string Data URI base64 siap dipakai di <img>
     */
    function QrBase64(string $isi, int $size = 300, string $format = 'png'): string
    {
        $mime = [
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
        ][$format] ?? 'image/png';

        $output = QrCode::format($format)
            ->size($size)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($isi);

        return 'data:' . $mime . ';base64,' . base64_encode($output);
    }
}
if (!function_exists('QrBranding')) {
    function QrBranding($isi, $filename, $folder = 'img/qrcode', $size = 300)
    {
        $folderPath = public_path($folder);

        if (!File::isDirectory($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        $fullPath = $folderPath . '/' . $filename;

        // 1. Generate QR sementara di memori
        $qrImage = QrCode::format('png')
            ->size($size)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($isi);

        // 2. Buat image dari QR
        $qr =  Image::make($qrImage);

        // 3. Tambah kanvas bawah (tinggi ekstra untuk teks)
        $heightWithText = $qr->height() + 30;
        $canvas = Image::canvas($qr->width(), $heightWithText, '#ffffff');

        // 4. Gabungkan QR di atas
        $canvas->insert($qr, 'top-left', 0, 0);

        // 5. Tambah teks "Ata Digital" di bawah tengah
        $canvas->text('Ata Digital', $qr->width() / 2, $qr->height() + 15, function ($font) {
            $font->file(public_path('fonts/arial.ttf')); // pastikan font ada
            $font->size(14);
            $font->color('#333333');
            $font->align('center');
            $font->valign('top');
        });

        // 6. Simpan ke file akhir
        $canvas->save($fullPath);

        return $fullPath;
    }
}

// Versi HashIds
// $id = 2301234567;
// encode : $hash = hashid_encode($id); // misal: 'Nj39LkPz'
// decode : $id_asli = hashid_decode($hash); // hasil: 2301234567
// Default
// if (!function_exists('hashid_encode')) {
//     function hashid_encode($id)
//     {
//         return Hashids::encode($id);
//     }
// }

// if (!function_exists('hashid_decode')) {
//     function hashid_decode($hash)
//     {
//         $decoded = Hashids::decode($hash);
//         return count($decoded) > 0 ? $decoded[0] : null;
//     }
// }
// Custom
// if (!function_exists('hashid_encode')) {
//     function hashid_encode($id, $salt = null, $length = 8)
//     {
//         // Gunakan salt dari config jika tidak disediakan
//         $salt = $salt ?? config('hashids.connections.main.salt');

//         $hashids = new Hashids($salt, $length);
//         return $hashids->encode($id);
//     }
// }

// if (!function_exists('hashid_decode')) {
//     function hashid_decode($hash, $salt = null, $length = 8)
//     {
//         $salt = $salt ?? config('hashids.connections.main.salt');
//         $hashids = new Hashids($salt, $length);
//         $decoded = $hashids->decode($hash);
//         return count($decoded) > 0 ? $decoded[0] : null;
//     }
// }
/*
➕ Default (NIS + folder default)

QrCodeHelper::generate($siswa->nis);

✏️ Dengan nama file khusus
QrCodeHelper::generate($siswa->nis, 'qr_' . $siswa->nama . '.png');

📁 Simpan ke folder yang berbeda
QrCodeHelper::generate($siswa->nis, null, 'img/qr/guru');

🔍 Semua dikustom
QrCodeHelper::generate('ALUMNI-12345', 'alumni_qr.png', 'img/qr/alumni', 400);

*/

/*
    |--------------------------------------------------------------------------
    | 📌 qrttd :
    |--------------------------------------------------------------------------
    |
    | Fitur :
    | - Merubah String / text menjadi qr sebagai ttd tetapi menggunakan format base64
    | - xxxxxxxxxxx
    |
    | Tujuan :
    | - Pembuatan ttd / validasi berbasis qr
    |
    |
    | Penggunaan :
    | - qrttd($text) pada ahref gambar langsung gunakan variabel
    |
    */
// Proses Coding
/*
$text = "Nama Sekolah: SMP Cipta IT\nNama Kepala: Farid Attallah\nNIP: 1234567890";
$qrBase64 = qrttd($text);

echo '<image href="' . $qrBase64 . '" x="100" y="100" width="100" height="100" />';
*/
// $qrImage = QrCode::format('png')
//     ->size($size)
//     ->margin(1)
//     ->errorCorrection('H')
//     ->generate($isi);

if (!function_exists('qrttd')) {
    function qrttd(string $text, int $size = 300): string
    {
        $qrImage = QrCode::format('png')
            ->size($size)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($text); // Langsung dari string input

        return 'data:image/png;base64,' . base64_encode($qrImage);
    }
}
