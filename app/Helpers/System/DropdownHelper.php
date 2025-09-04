<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Whatsapp\WhatsApp;
use App\Models\User\Guru\Detailguru;
use Illuminate\Support\Facades\Auth;
use App\Models\User\Siswa\Detailsiswa;

/*
|--------------------------------------------------------------------------
| 📌 DropdownHelper :
|--------------------------------------------------------------------------
|
| Fitur :
| - Generate elemen HTML <select> secara dinamis dari array key-value.
| - Mendukung pemilihan item default (selected).
| - Bisa menambahkan atribut tambahan (class, multiple, dsb).
| - Aman dari XSS dengan htmlspecialchars().
|
| Tujuan :
| - Menghindari penulisan manual elemen <select> di Blade.
| - Menstandarisasi cara membuat dropdown di seluruh proyek.
| - Mendukung reusabilitas dan clean code.
|
| Penggunaan :
| 1. Data statis:
|    echo dropdown('jenis_kelamin', ['L' => 'Laki-laki', 'P' => 'Perempuan'], 'L');
|
| 2. Data dari Eloquent Collection:
|    $options = $siswa->pluck('nama_siswa', 'id')->toArray();
|    echo dropdown('siswa_id', $options, old('siswa_id'), ['class' => 'form-control']);
|   {!! dropdown('siswa_id', $options, old('siswa_id'), ['class' => 'form-control']) !!}
|
*/
//
if (!function_exists('dropdown')) {
    /**
     * Membuat elemen <select> HTML dari array key-value.
     *
     * @param string $name      Atribut name dan id dari select
     * @param array  $options   Data option dalam bentuk [value => label]
     * @param mixed  $selected  Nilai yang terpilih (default: null)
     * @param array  $attrs     Atribut tambahan untuk <select> (contoh: ['class' => 'form-control'])
     *
     * @return string HTML <select> yang siap digunakan
     */
    function dropdown(string $name, array $options, $selected = null, array $attrs = []): string
    {
        // Siapkan atribut tambahan (class, multiple, dsb)
        $attrString = '';
        foreach ($attrs as $key => $val) {
            $attrString .= ' ' . $key . '="' . htmlspecialchars($val) . '"';
        }

        // Buka tag <select>
        $html = '<select name="' . $name . '" id="' . $name . '"' . $attrString . '>';

        // Tambahkan setiap option
        foreach ($options as $value => $label) {
            $isSelected = (is_array($selected) && in_array($value, $selected)) || $value == $selected;
            $html .= '<option value="' . htmlspecialchars($value) . '"' . ($isSelected ? ' selected' : '') . '>';
            $html .= htmlspecialchars($label) . '</option>';
        }

        // Tutup tag <select>
        $html .= '</select>';

        return $html;
    }
}
/*
|--------------------------------------------------------------------------
| 📌 DropdownHelper : formSelect()
|--------------------------------------------------------------------------
|
| Fitur :
| - Generate elemen HTML <select> secara dinamis dari array atau collection.
| - Mendukung mode single dan multiple select secara otomatis.
| - Bisa menentukan value & label dari properti collection (valueKey & labelKey).
| - Otomatis set selected (baik 1 nilai atau array untuk multiple).
| - Bisa tambah atribut tambahan: class, required, placeholder, multiple, dll.
| - Aman dari XSS karena semua label dan value di-escape dengan htmlspecialchars().
|
| Tujuan :
| - Menghindari duplikasi HTML select di banyak tempat.
| - Mempermudah pembuatan form dinamis dari data database atau array.
| - Mendukung clean code & DRY principle di Blade view.
|
| Penggunaan :
|
| 1. Dengan array statis:
|    {!! formSelect('jenis_kelamin', ['L' => 'Laki-laki', 'P' => 'Perempuan'], null, null, 'L', [
|        'label' => 'Jenis Kelamin',
|        'class' => 'form-control select2',
|        'placeholder' => '-- Pilih Jenis Kelamin --',
|        'required' => true
|    ]) !!}
|
| 2. Dengan Eloquent Collection:
|    $options = $siswas; // collection dari model Siswa
|    {!! formSelect('siswa_id', $options, 'id', 'nama_siswa', old('siswa_id'), [
|        'label' => 'Nama Siswa',
|        'placeholder' => '-- Pilih Siswa --',
|        'class' => 'form-control select2',
|        'required' => true
|    ]) !!}
|
| 3. Untuk multiple select:
|    {!! formSelect('siswa_id', $siswas, 'id', 'nama_siswa', old('siswa_id', []), [
|        'label' => 'Pilih Beberapa Siswa',
|        'class' => 'form-control select2',
|        'multiple' => true, // Single gunakan false
|        'required' => true // ⬅️ ini bikin fieldnya TIDAK wajib
|    ]) !!}
|
*/

if (!function_exists('formSelect')) {
    /**
     * Membuat dropdown lengkap (single atau multiple) dengan label & form-group.
     *
     * @param string $name         Nama dan ID elemen <select>
     * @param iterable $data       Data (collection/array) [value => label] atau objek
     * @param string|null $valueKey Properti value (untuk object)
     * @param string|null $labelKey Properti label (untuk object)
     * @param mixed $selected      Nilai yang dipilih (boleh array atau single)
     * @param array $options       Opsi tambahan (label, class, placeholder, required, multiple)
     * @return string              HTML hasil render
     */
    function formSelect(
        string $name,
        iterable $data,
        string $valueKey = null,
        string $labelKey = null,
        $selected = null,
        array $options = []
    ): string {
        $label       = $options['label'] ?? ucfirst(str_replace('_', ' ', $name));
        $class       = $options['class'] ?? 'form-control';
        $placeholder = $options['placeholder'] ?? null;
        $required    = !empty($options['required']) ? 'required' : '';
        $multiple    = !empty($options['multiple']) || is_array($selected);
        $id          = $name;

        // Jika multiple, tambahkan []
        $selectName = $multiple ? $name . '[]' : $name;
        $multipleAttr = $multiple ? 'multiple' : '';

        // Mulai HTML options
        $htmlOptions = '';

        // Placeholder hanya jika single select
        if ($placeholder && !$multiple) {
            $htmlOptions .= "<option value=''>{$placeholder}</option>";
        }

        foreach ($data as $key => $item) {
            if (is_object($item) && $valueKey && $labelKey) {
                $value = $item->{$valueKey};
                $labelItem = $item->{$labelKey};
            } elseif (is_array($item) && $valueKey && $labelKey) {
                $value = $item[$valueKey];
                $labelItem = $item[$labelKey];
            } else {
                $value = $key;
                $labelItem = $item;
            }

            $isSelected = (
                (is_array($selected) && in_array($value, $selected)) ||
                (!is_array($selected) && $value == $selected)
            ) ? 'selected' : '';

            $htmlOptions .= "<option value='" . htmlspecialchars($value) . "' $isSelected>" . htmlspecialchars($labelItem) . "</option>";
        }

        return "
        <div class='form-group'>
            <label for='{$id}'>{$label}</label>
            <select name='{$selectName}' id='{$id}' data-placeholder='{$placeholder}' class='{$class}' {$required} {$multipleAttr}>
                {$htmlOptions}
            </select>
        </div>
        ";
    }
}
