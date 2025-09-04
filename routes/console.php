 <?php

    use Illuminate\Support\Str;
    use Illuminate\Foundation\Inspiring;
    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Support\Facades\File;

    Artisan::command('inspire', function () {
        $this->comment(Inspiring::quote());
    })->purpose('Display an inspiring quote')->hourly();

    Artisan::command('make:custom-controller {name} {fillablin} {routename} {--force}', function ($name, $fillablin, $routename) {

        // Mendapatkan nama controller dan model
        $controllerClass = class_basename($name); // Misalnya 'FiturAplikasiController'

        $modelName = str_replace('Controller', '', $controllerClass); // Misalnya 'FiturAplikasi'
        // dd($modelName);
        $modelNamespace = "App\\Models\\" . str_replace('/', '\\', dirname($name)); // Misalnya 'App\\Models\\Aplikasi\\Fitur'
        $pathingviewcontroller = str_replace('/', '.', dirname($name)); // Misalnya 'App\\Models\\Aplikasi\\Fitur'
        // $pathingviewViewr = str_replace('/', '.', dirname($name)); // Misalnya 'App\\Models\\Aplikasi\\Fitur'
        $DirNmaes = dirname($name);
        // $this->info("Controller '{$pathingviewcontroller}' pathingviewcontroller.");
        // $this->info("Controller '{$DirNmaes}' pathingviewcontroller.");
        $Title = str_replace('/', ' ', $DirNmaes);

        $Breadcumber = Str::headline(class_basename($modelName));
        function ArrayTabel($fillablin)
        {
            $explode = explode('/', $fillablin);
            $hasil = '';

            foreach ($explode as $stringdata) {
                $keya = trim($stringdata);
                $key = ucwords(str_replace("_", " ", $keya));
                $hasil .= "'{$key}'," . PHP_EOL;
            }

            return $hasil;
        }
        $dataArray = ArrayTabel($fillablin);
        function Fillabel($fillablin)
        {
            $explode = explode('/', $fillablin);
            $hasil = '';

            foreach ($explode as $stringdata) {
                $key = trim($stringdata);
                $cekId = explode('_', $key);
                if (end($cekId) === 'id') {
                    $typeData = 'integer';
                } else {
                    $typeData = 'string|min:3|max:255';
                }
                $hasil .= "'{$key}' => 'required|{$typeData}'," . PHP_EOL;
            }

            return $hasil;
        }

        $fillableNew = Fillabel($fillablin);
        // dd($fillableNew);

        // Nama view sesuai model, dengan format huruf kecil dan pemisahan dengan '-'
        $viewName = strtolower(str_replace('\\', '-', $modelName)); // Misalnya 'fitur-aplikasi'

        // Membuat nama view untuk aksi index, show, dan edit
        function camelToKebab($string)
        {
            // Menambahkan tanda hubung sebelum huruf besar dan mengubahnya menjadi huruf kecil
            return strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $string));
        }

        $string = $modelName;
        $newString = camelToKebab($string);




        $viewIndex = $newString; // fitur-aplikasi (untuk index)
        // $this->info("Controller '{$viewIndex}' namaceker.");
        $viewShow = $newString . '-single'; // fitur-aplikasi-single (untuk show)
        $viewEdit = $newString . '-edit'; // fitur-aplikasi-edit (untuk edit)
        $viewCreate = $newString . '-create'; // fitur-aplikasi-single (untuk show)
        $viewCetak = $newString . '-cetak'; // fitur-aplikasi-single (untuk show)

        // Memecah nama view berdasarkan '-'
        $viewNameParts = explode('-', $viewName); // ['fitur', 'aplikasi']

        // Menyusun folder dan file view
        $viewFolder = implode('/', $viewNameParts); // fitur/aplikasi
        $viewFile = $viewName; // nama file view default
        $rootBlade = 'pages.' . strtolower($pathingviewcontroller) . '.' . $viewFolder . '.' . $newString;
        // $this->info("Controller '{$rootBlade}' rootBlade.");
        // Menyusun namespace controller
        $namespace = 'App\\Http\\Controllers\\' . str_replace('/', '\\', dirname($name)); // Misalnya 'App\\Http\\Controllers\\Aplikasi\\Fitur'

        // Membaca stub controller
        $stub = file_get_contents(base_path('stubs/custom-controller.stub'));

        // Pembuatan variabel Controller
        // Mengganti placeholder di stub dengan data yang sesuai ( bisa tambahkan variabel )
        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ model }}', '{{ view }}', '{{ title }}', '{{ view_create }}', '{{ view_show }}', '{{ view_cetak }}', '{{ view_edit }}', '{{ modelName }}',  '{{ Breadcumber }}', '{{ fillableNew }}', '{{ dataArray }}'],
            [$namespace, $controllerClass, $modelNamespace . '\\' . $modelName, $rootBlade, $Title, $rootBlade . '-create', $rootBlade . '-single', $rootBlade . '-cetak', $rootBlade . '-edit', $modelName, $Breadcumber, $fillableNew, $dataArray],
            $stub
        );


        // Menyimpan file controller yang dihasilkan
        $controllerPath = app_path('Http/Controllers/' . str_replace('\\', '/', $name) . '.php');
        if (!is_dir(dirname($controllerPath))) {
            mkdir(dirname($controllerPath), 0755, true);
        }

        // Menimpa file controller jika opsi --force diberikan atau file belum ada
        if ($this->option('force') || !file_exists($controllerPath)) {
            file_put_contents($controllerPath, $stub);
            $this->info("Controller '{$name}' berhasil dibuat.");
        } else {
            $this->error("Controller '{$name}' sudah ada. Gunakan opsi --force untuk menimpa.");
        }
        $Dirawal = 'pages.' . strtolower($pathingviewcontroller) . '.' . $viewFolder;
        $direktor = str_replace('.', '/', $Dirawal);
        $this->info("Controller '{$direktor}' direktor.");
        // Membuat folder dan file view untuk index, show, dan edit
        $bladePaths = [
            'index' => resource_path("views/" . $direktor . "/{$viewIndex}.blade.php"),
            'show'  => resource_path("views/" . $direktor . "/{$viewShow}.blade.php"),
            'edit'  => resource_path("views/" . $direktor . "/{$viewEdit}.blade.php"),
            'create'  => resource_path("views/" . $direktor . "/{$viewCreate}.blade.php"),
            'cetak'  => resource_path("views/" . $direktor . "/{$viewCetak}.blade.php"),
        ];

        // Membuat file view
        // Membuat folder jika belum ada
        foreach ($bladePaths as $fileKey => $bladePath) {
            if (!is_dir(dirname($bladePath))) {
                mkdir(dirname($bladePath), 0755, true);
            }

            // Membuat file blade jika belum ada atau jika opsi --force diberikan
            if (!file_exists($bladePath) || $this->option('force')) {
                // $content = "<h1>View untuk {{ \$title }} - $fileKey</h1>\n<p>Generated automatically</p>";
                switch ($fileKey) {
                    case 'index':
                        // ============================
                        // Konten khusus untuk INDEX
                        // ============================
                        $content = <<<'BLADE'
@php
//content
use Illuminate\Support\Carbon;
\Carbon\Carbon::setLocale('id');
@endphp
@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <section class='content mx-2 my-4'>
        {{-- Validator --}}
        @if ($errors->any())
            <div class='alert alert-danger'>
                <ul class='mb-0'>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Validator --}}
        <div class='card'>
            <!--Card Header-->
            <div class='card-header bg-primary mx-2'>
                <h3 class='card-title'>{{ $title }}</h3>
            </div>
            <!--Card Header-->
            <div class='row m-2'>
                {{-- blade-formatter-disable --}}
                <div class='col-xl-2'>
                    <button type='button' class='btn btn-block btn-default bg-primary btn-md' onclick='TambahData()'><i class='fa fa-plus'></i> Tambah Data</button>
                </div>
                {{-- blade-formatter-enable --}}
                <div class='col-xl-10'></div>
            </div>
            <div class='ml-2 my-4'>
                <div class='card'>
                    <div class='card-header bg-primary'>
                        <h3 class='card-title'>{{ $title }}</h3>
                    </div>
                    <div class='card-body'>
                        <!-- Konten -->
                                <table id='example1' width='100%' class='table table-responsive table-bordered table-hover'>
            <thead>
                   <tr class='text-center'>
                    <th width='1%'>ID</th>
                       @foreach ($arr_ths as $arr_th)
                    <th class='text-center'> {{ $arr_th }}</th>
                       @endforeach
                    <th>Action</th>
                   </tr>
            </thead>
            <tbody>
                   @foreach (${modename} as $data)
                   <tr>
                       <td class='text-center'>{{ $loop->iteration }}</td>
                       {varfillable}

                       <td width='10%'>
                                            {{-- blade-formatter-disable --}}
                                            <div class='d-flex justify-content-center'>
                                                <!-- Button untuk mengedit -->
                                                <button type='button' class='btn btn-warning btn-sm btn-equal-width' data-toggle='modal' data-target='#editModal{{ $data->id }}'><i class='fa fa-edit'></i> </button>
                                                <!-- Form untuk menghapus -->
                                                <form id='delete-form-{{ $data->id }}' action='{{ route('{routename}.destroy', $data->id) }}' method='POST' style='display: inline-block;'>
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <button type='button' class='btn btn-danger btn-sm btn-equal-width' onclick='confirmDelete({{ $data->id }})'> <i class='fa fa-trash'></i>  </button>
                                            </div>
                                            {{-- blade-formatter-enable --}}
                              {{-- Modal View Data Akhir --}}
                              <div class='modal fade' id='editModal{{ $data->id }}' tabindex='-1' aria-labelledby='EditModalLabel' aria-hidden='true'>
                                  <x-edit-modal>
                                      <x-slot:titleeditModal>{{ $titleeditModal }}</x-slot:titleeditModal>
                                      <section>
                                             <form id='update-{routename}' action='{{ route('{routename}.update', $data->id) }}' method='POST'>
                                                 @csrf
                                                 @method('PATCH')

                                                    contentEdit

                                                 <button id='kirim' type='submit' class='btn float-right btn-default bg-primary btn-xl mt-4'> Kirim</button>
                                             </form>
                                      </section>
                                  </x-edit-modal>
                              </div>
                              {{-- Modal Edit Data Akhir --}}
                              {{-- Modal View --}}
                              <div class='modal fade' id='viewModal{{ $data->id }}' tabindex='-1' aria-labelledby='ViewModalLabel' aria-hidden='true'>
                                  <x-view-modal>
                                      <x-slot:titleviewModal>{{ $titleviewModal }}</x-slot:titleviewModal>
                                      <section>
                                             //Content View
                                      </section>
                                  </x-view-modal>
                              </div>
                              {{-- Modal View Akhir --}}
                          </td>
                       </tr>
               @endforeach
            </tbody>
            <tfoot>
                   <tr class='text-center'>
                       <th width='1%'>ID</th>
                       @foreach ($arr_ths as $arr_th)
                       <th class='text-center'> {{ $arr_th }}</th>
                       @endforeach
                       <th class='text-center'>Action</th>
                   </tr>
            </tfoot>
        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
BLADE;
                        break;

                    case 'show':
                        // ============================
                        // Konten khusus untuk SHOW
                        // ============================
                        $content = <<<'BLADE'
@php
//content
use Illuminate\Support\Carbon;
\Carbon\Carbon::setLocale('id');
@endphp
@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <section class='content mx-2 my-4'>
        {{-- Validator --}}
        @if ($errors->any())
            <div class='alert alert-danger'>
                <ul class='mb-0'>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Validator --}}
        <div class='card'>
            <!--Card Header-->
            <div class='card-header bg-primary mx-2'>
                <h3 class='card-title'>{{ $title }}</h3>
            </div>
            <!--Card Header-->
            <div class='row m-2'>
                <div class='col-xl-2'>
                     <a href="{{ route('{routename}.index') }}" class="btn btn-secondary btn-md w-100"><i class="fa fa-arrow-left"></i> Kembali</a>
                </div>
                <div class='col-xl-10'></div>
            </div>
            <div class='ml-2 my-4'>
                <div class='card'>
                    <div class='card-header bg-primary'>
                        <h3 class='card-title'>{{ $title }}</h3>
                    </div>
                    <div class='card-body'>
                        <!-- Konten -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
BLADE;
                        break;

                    case 'edit':
                        // ============================
                        // Konten khusus untuk EDIT
                        // ============================
                        $content = <<<'BLADE'
@php
//content
use Illuminate\Support\Carbon;
\Carbon\Carbon::setLocale('id');
@endphp
@extends('layouts.app')
@section('title', $title)
@section('content')
    <section class='content mx-2 my-4'>
        {{-- Validator --}}
        @if ($errors->any())
            <div class='alert alert-danger'>
                <ul class='mb-0'>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Validator --}}
        <div class='card'>
            <!--Card Header-->
            <div class='card-header bg-primary mx-2'>
                <h3 class='card-title'>{{ $title }}</h3>
            </div>
            <!--Card Header-->
                <div class='row m-2'>
                <div class='col-xl-2'>
                     <a href="{{ route('{routename}.index') }}" class="btn btn-secondary btn-md w-100"><i class="fa fa-arrow-left"></i> Kembali</a>
                </div>
                <div class='col-xl-10'></div>
            </div>
            <div class='ml-2 my-4'>
                <div class='card'>
                    <div class='card-header bg-primary'>
                        <h3 class='card-title'>{{ $title }}</h3>
                    </div>
                    <div class='card-body'>
                        <form id='#id' action='{{route('{routename}.update', $data->id)}}' method='POST'>
                            @csrf
                            @method('PATCH')
                            content_form
                            <button type='submit' class='btn btn-block btn-default bg-primary btn-md float-right'></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
BLADE;
                        break;
                    case 'create':
                        // ============================
                        // Konten khusus untuk EDIT
                        // ============================
                        $content = <<<'BLADE'
@php
//content
use Illuminate\Support\Carbon;
\Carbon\Carbon::setLocale('id');
@endphp
@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <section class='content mx-2 my-4'>
        {{-- Validator --}}
        @if ($errors->any())
            <div class='alert alert-danger'>
                <ul class='mb-0'>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Validator --}}
        <div class='card'>
            <!--Card Header-->
            <div class='card-header bg-primary mx-2'>
                <h3 class='card-title'>{{ $title }}</h3>
            </div>
            <!--Card Header-->
                <div class='row m-2'>
                    <div class='col-xl-2'>
                         <a href="{{ route('{routename}.index') }}" class="btn btn-secondary btn-md w-100"><i class="fa fa-arrow-left"></i> Kembali</a>
                    </div>
                    <div class='col-xl-10'></div>
                </div>
            <div class='ml-2 my-4'>
                <div class='card'>
                    <div class='card-header bg-primary'>
                        <h3 class='card-title'>{{ $title }}</h3>
                    </div>
                    <div class='card-body'>
                        <form id='#id' action='{{route('{routename}.store', $data->id)}}' method='POST'>
                            @csrf
                            @method('POST')
                            content_form
                            {{-- blade-formatter-disable --}}
                            <button type='submit' class='btn btn-block btn-default bg-primary btn-md float-right'></button>
                            {{-- blade-formatter-enable --}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
BLADE;
                        break;
                    case 'cetak':
                        // ============================
                        // Konten khusus untuk EDIT
                        // ============================
                        $content = <<<'BLADE'
                    @php
    //content
    use Illuminate\Support\Carbon;
    \Carbon\Carbon::setLocale('id');
@endphp
@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

    <link rel='stylesheet' href='{{ asset('css/layout-cetak.css') }}'>
    {{-- Undangan Rapat --}}
    <div class='container'>
        <x-kop-surat-cetak></x-kop-surat-cetak>
        <div class="mt-4"></div>

    Isi Cetak
    </div>
@endsection
BLADE;
                        break;

                    default:
                        // ============================
                        // Fallback konten jika tidak dikenali
                        // ============================
                        $content = <<<'BLADE'
@php
//content
use Illuminate\Support\Carbon;
\Carbon\Carbon::setLocale('id');
    $activecrud = collect([2,4, 6, 8])->search(Auth::user()->id);
    $urlroot = app('request')->root();
@endphp
@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <section class='content mx-2 my-4'>
        {{-- Validator --}}
        @if ($errors->any())
            <div class='alert alert-danger'>
                <ul class='mb-0'>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Validator --}}
        <div class='card'>
            <!--Card Header-->
            <div class='card-header bg-primary mx-2'>
                <h3 class='card-title'>{{ $title }}</h3>
            </div>
            <!--Card Header-->
            <div class='row m-2'>
                <div class='col-xl-2'>
                </div>
                <div class='col-xl-10'></div>
            </div>
            <div class='ml-2 my-4'>
                <div class='card'>
                    <div class='card-header bg-primary'>
                        <h3 class='card-title'>{{ $title }}</h3>
                    </div>
                    <div class='card-body'>
                        <!-- Konten -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
BLADE;
                }

                $routename = $this->argument('routename');
                $modelName = $modelName; // pastikan ini sudah di-set sebelumnya
                $dataFIlLable = explode('/', $fillablin);
                $hasilFill = '';
                foreach ($dataFIlLable as $datafil) {
                    $hasilFill .=  "<td class='text-center'> {{ \$data->$datafil}}</td>";
                }
                // Ganti placeholder sekaligus pakai array
                $replacements = [
                    '{routename}' => $routename,
                    '{modename}' => $modelName,
                    '{filable}' => $fillablin,
                    '{varfillable}' => $hasilFill,
                ];

                $content = str_replace(array_keys($replacements), array_values($replacements), $content);

                file_put_contents($bladePath, $content);
                $this->info("File view 'resources/views/{$viewFolder}/{$fileKey}.blade.php' berhasil dibuat.");
            } else {
                $this->error("File view 'resources/views/{$viewFolder}/{$fileKey}.blade.php' sudah ada. Gunakan opsi --force untuk menimpa.");
            }
        }




        // Membuat model secara otomatis
        $modelPath = app_path('Models/' . str_replace('App\\Models\\', '', $modelNamespace) . '/' . $modelName . '.php');
        $tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $modelName));
        if (!file_exists($modelPath) || $this->option('force')) {
            $modelStub = file_get_contents(base_path('stubs/custom-model.stub'));

            $fillableNew = "'" . Str::replace("/", "','", $fillablin) . "'"; // Ganti '/' jadi spasi
            // Ganti placeholder model
            $modelStub = str_replace(
                ['{{ namespace }}', '{{ model }}', '{{ table }}', '{{ fillableNew }}'],
                [$modelNamespace, $modelName, $tableName, $fillableNew],
                $modelStub
            );

            // Menyimpan file model
            if (!is_dir(dirname($modelPath))) {
                mkdir(dirname($modelPath), 0755, true);
            }

            file_put_contents($modelPath, $modelStub);
            $this->info("Model '{$modelNamespace}\\{$modelName}' berhasil dibuat.");
        } else {
            $this->error("Model '{$modelNamespace}\\{$modelName}' sudah ada. Gunakan opsi --force untuk menimpa.");
        }

        // ==== MAPPING VIEW CUSTOM ====
        $customViewMap = [
            'Program/DataTestter/DataTesteredController' => 'DataTestered',
            // Tambahkan mapping lain jika dibutuhkan
        ];

        // Nama controller dalam format path
        $controllerPath = str_replace('\\', '/', $name);
        $controllerKey = trim($controllerPath, '/');

        // Gunakan custom mapping jika tersedia
        if (isset($customViewMap[$controllerKey])) {
            $viewFolder = $customViewMap[$controllerKey];
        } else {
            // Default: gunakan nama controller tanpa 'Controller'
            $controllerBaseName = class_basename($name); // e.g. DataTesteredController
            $viewFolder = str_replace('Controller', '', $controllerBaseName);
        }

        // === SEEDER ===
        $seederClass = $modelName . 'Seeder';
        $modelPath = app_path('Models/' . str_replace('App\\Models\\', '', $modelName));
        // $this->info("Seeder '{$modelPath}\\{$seederClass}' modelPath");
        $relativeSeederNamespace = str_replace('/', '\\', dirname($name));
        $seederNamespace = 'Database\\Seeders' . ($relativeSeederNamespace ? '\\' . $relativeSeederNamespace : '');
        $seederDirectory = base_path('database/seeders/' . ($relativeSeederNamespace ? str_replace('\\', '/', $relativeSeederNamespace) . '/' : ''));
        $seederFullPath = $seederDirectory . $seederClass . '.php';


        function FillabelSeeder($fillablin)
        {
            $explode = explode('/', $fillablin);
            $hasil = '';

            foreach ($explode as $stringdata) {
                $key = trim($stringdata);
                $hasil .= "'{$key}' => \$Data['$key']," . PHP_EOL;
            }

            return $hasil;
        }

        function PembentukArray($fillablin)
        {
            $explode = explode('/', $fillablin);
            $hasil = '';

            foreach ($explode as $stringdata) {
                $key = trim($stringdata);
                $hasil .= "'{$key}' => '{$key}'," . PHP_EOL;
            }

            return "[" . $hasil . "],";
        }
        //Array Create
        function ArrayFillable($fillablin)
        {
            $explode = explode('/', $fillablin);
            $hasil = '';

            foreach ($explode as $stringdata) {
                $key = trim($stringdata);
                $hasil .= "'{$key}' => '$key'," . PHP_EOL;
            }

            return $hasil;
        }

        $fillableSeeder = FillabelSeeder($fillablin);
        $ArraySeeder = ArrayFillable($fillablin);
        $PembentukArray = PembentukArray($fillablin);

        if (!file_exists($seederFullPath) || $this->option('force')) {
            $seederStub = file_get_contents(base_path('stubs/custom-seeder.stub'));

            $seederStub = str_replace(
                ['{{ model_namespace }}', '{{ model }}', '{{ class }}', '{{ model_class }}', '{{ data }}', '{{ PembentukArray }}'],
                [$seederNamespace, $modelPath, $seederClass, '\\' . $modelNamespace . '\\' . $modelName, $fillableSeeder, $PembentukArray],
                $seederStub
            );

            if (!is_dir(dirname($seederFullPath))) {
                mkdir(dirname($seederFullPath), 0755, true);
            }

            file_put_contents($seederFullPath, $seederStub);
            $this->info("Seeder '{$seederNamespace}\\{$seederClass}' berhasil dibuat.");
        } else {
            $this->error("Seeder '{$seederNamespace}\\{$seederClass}' sudah ada. Gunakan opsi --force untuk menimpa.");
        }





        // === MIGRASI ===

        // 1. Buat nama tabel dari model (snake_case)
        $tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $modelName));
        // Output: rincian_daftar_ulang

        // 2. Format nama class migrasi
        $modelNameFormatted = preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $modelName);
        $modelNameFormatted = ucwords($modelNameFormatted); // Rincian_Daftar_Ulang
        $classMigrationName = 'Migration_' . $modelNameFormatted; // Migration_Rincian_Daftar_Ulang

        // 3. Buat nama file migrasi dengan format timestamp Laravel
        $timestamp = date('Y_m_d_His');
        $fileMigrationName = $timestamp . '_migration_' . strtolower($modelNameFormatted) . '.php';
        // Contoh: 2025_07_18_235959_migration_rincian_daftar_ulang.php

        // 4. Path file migrasi
        $migrationPath = database_path('migrations/' . $fileMigrationName);

        // 5. Baca stub dan ganti placeholder
        $migrationStub = file_get_contents(base_path('stubs/custom-migration.stub'));

        //Bagian fillabel diruba ke tabel
        function FillabelMigration($fillablin)
        {
            $explode = explode('/', $fillablin);
            $hasil = '';

            // Mapping foreign keys: kolom => [referensi_tabel, onDelete]
            $foreignMap = [
                'tapel_id' => ['etapels', 'set null'],
                'detailsiswa_id' => ['detailsiswas', 'cascade'],
                'detailguru_id' => ['detailgurus', 'set null'],
                // Tambah sesuai kebutuhan
            ];
            foreach ($explode as $key) {
                $key = trim($key);
                $tipe = 'string'; // default

                // Tentukan tipe data otomatis berdasarkan nama kolom
                if (preg_match('/_id$/', $key)) {
                    $tipe = 'unsignedInteger';
                } elseif (preg_match('/^tanggal_/', $key)) {
                    $tipe = 'date';
                } elseif (preg_match('/^waktu_/', $key)) {
                    $tipe = 'time';
                } elseif (preg_match('/^(jumlah|total)_/', $key)) {
                    $tipe = 'double';
                } elseif (preg_match('/^(is_|status_)/', $key)) {
                    $tipe = 'boolean';
                }

                // Tambahkan definisi kolom
                $hasil .= "\$table->$tipe('$key')->nullable();" . PHP_EOL;

                // Tambahkan foreign key jika ada dalam mapping
                if (isset($foreignMap[$key])) {
                    [$referensi, $onDelete] = $foreignMap[$key];
                    $hasil .= "\$table->foreign('$key')->references('id')->on('$referensi')->onDelete('$onDelete');" . PHP_EOL;
                }
            }

            return $hasil;
        }

        $fillableNew = FillabelMigration($fillablin);
        $migrationStub = str_replace(
            ['{{ class }}', '{{ table }}', '{{ data }}', '{{ fileMigrationName }}'],
            [$classMigrationName, $tableName, $fillableNew, $fileMigrationName], // contoh: Migration_Rincian_Daftar_Ulang, rincian_daftar_ulang
            $migrationStub
        );

        // 6. Simpan file migrasi
        if (!file_exists($migrationPath) || $this->option('force')) {
            file_put_contents($migrationPath, $migrationStub);
            $this->info("✅ Migrasi '{$fileMigrationName}' berhasil dibuat.");
        } else {
            $this->error("⚠️ Migrasi '{$fileMigrationName}' sudah ada. Gunakan opsi --force untuk menimpa.");
        }
    });
    //

    /*
        |--------------------------------------------------------------------------
        | 📌 Menyisipkan ke kernel :
        |--------------------------------------------------------------------------
        |
        | Fitur :
        | - Pembuatan Custom Command
        | - Menambahkan command ke kernel otomatis
        |
        | Tujuan :
        | - Mempersingkat proses
        |
        |
        | Penggunaan :
        | - php artisan make:custom-command namacommand "Deskripsi" "Nama Modul Terhubung : Contoh : Perpustakaan"
        |
        */
    Artisan::command('make:custom-command {signature} {name} {deskripsi} {namaapps} {--force}', function ($signature, $name, $deskripsi, $namaapps) {
        $force = $this->option('force'); // true = dipakai, false = tidak
        $className = ucfirst($name);
        $filePath = app_path("Console/Commands/{$className}.php");

        // Cegah overwrite kalau file sudah ada (kecuali pakai --force)
        if (File::exists($filePath) && !$force) {
            $this->error("File '{$className}.php' sudah ada. Gunakan --force untuk menimpa.");
            return;
        }

        // Template file command
        $template = <<<PHP
<?php

namespace App\Console\Commands;

use App\Models\WhatsApp;
use Illuminate\Console\Command;

class {$className} extends Command
{
    protected \$signature = '{$signature}';
    protected \$description = '{$deskripsi}';

    /*
        |--------------------------------------------------------------------------
        | 📌 {$namaapps}
        |--------------------------------------------------------------------------
        |
        | Fitur :
        | -
        |
        | Tujuan :
        | - Jelaskan tujuan command ini
        | ScheduleServiceProvider :
        | \$schedule->command('{$signature}')->everyMinute();               // Setiap menit dijalankan
        | \$schedule->command('{$signature}')->everyTwoMinutes();           // Setiap 2 menit dijalankan
        | \$schedule->command('{$signature}')->everyThreeMinutes();         // Setiap 3 menit dijalankan
        | \$schedule->command('{$signature}')->everyFiveMinutes();          // Setiap 5 menit dijalankan
        | \$schedule->command('{$signature}')->everyTenMinutes();           // Setiap 10 menit dijalankan
        | \$schedule->command('{$signature}')->everyFifteenMinutes();       // Setiap 15 menit dijalankan
        | \$schedule->command('{$signature}')->everyThirtyMinutes();        // Setiap 30 menit dijalankan
        | \$schedule->command('{$signature}')->hourly();                    // Setiap jam dijalankan
        | \$schedule->command('{$signature}')->hourlyAt(15);                // Setiap jam, tepat di menit ke-15
        | \$schedule->command('{$signature}')->daily();                     // Setiap hari pukul 00:00
        | \$schedule->command('{$signature}')->dailyAt('07:30');            // Setiap hari pukul 07:30
        | \$schedule->command('{$signature}')->twiceDaily(1, 13);           // Setiap hari pukul 01:00 dan 13:00
        | \$schedule->command('{$signature}')->weekly();                    // Setiap minggu pada Senin pukul 00:00
        | \$schedule->command('{$signature}')->weeklyOn(1, '08:00');        // Setiap minggu pada hari Senin pukul 08:00
        | \$schedule->command('{$signature}')->monthly();                   // Setiap bulan tanggal 1 pukul 00:00
        | \$schedule->command('{$signature}')->monthlyOn(15, '09:00');      // Setiap bulan tanggal 15 pukul 09:00
        | \$schedule->command('{$signature}')->quarterly();                 // Setiap 3 bulan
        | \$schedule->command('{$signature}')->yearly();                    // Setiap tahun pada 1 Januari pukul 00:00
        | \$schedule->command('{$signature}')->timezone('Asia/Jakarta');    // Menentukan timezone
        | \$schedule->command('{$signature}')->runInBackground();           // Menjalankan command di background
        | \$schedule->command('{$signature}')->withoutOverlapping();        // Mencegah command berjalan bersamaan
        | \$schedule->command('{$signature}')->onOneServer();               // Menjalankan hanya di satu server
        |
        | Penggunaan :
        | - Jelaskan penggunaannya dimana atau hubungannya
        | -
        |
    */

    public function handle()
    {
        // Tuliskan logika command di sini
        \$this->info("Command '{$className}' berhasil dijalankan.");
    }
}
PHP;

        // Pastikan foldernya ada lalu simpan file
        File::ensureDirectoryExists(app_path('Console/Commands'));
        File::put($filePath, $template);

        $this->info("✅ Custom command '{$className}' berhasil dibuat di: {$filePath}");

        // Auto-register ke Kernel.php
        $kernelPath = app_path('Console/Kernel.php');
        $commandClass = "\\App\\Console\\Commands\\{$className}::class";

        $kernelContents = File::get($kernelPath);

        if (!str_contains($kernelContents, $commandClass)) {
            $updatedContents = preg_replace(
                '/protected \$commands\s*=\s*\[[^\]]*/',
                '$0' . "\n        {$commandClass},\n",
                $kernelContents
            );

            File::put($kernelPath, $updatedContents);
            $this->info("✅ Command '{$className}' berhasil di-register ke app/Console/Kernel.php!");
        } else {
            $this->warn("ℹ️  Command '{$className}' sudah terdaftar di app/Console/Kernel.php.");
        }

        // Auto-register ke ScheduleServiceProvider.php
        $servicePath = app_path('Providers/ScheduleServiceProvider.php');
        $serviceContents = File::get($servicePath);

        $newCommand = "\\App\\Console\\Commands\\{$className}::class";

        if (!str_contains($serviceContents, $newCommand)) {
            $updatedContents = preg_replace(
                '/\$this->commands\(\s*\[((?:.|\s)*?)\]\s*\)/s',
                '$this->commands([$1' . "\n            {$newCommand}," . "\n        ])",
                $serviceContents
            );

            File::put($servicePath, $updatedContents);
            $this->info("✅ Command '{$className}' berhasil ditambahkan ke ScheduleServiceProvider.php!");
        } else {
            $this->warn("ℹ️ Command '{$className}' sudah terdaftar di ScheduleServiceProvider.php.");
        }

        // Membuat Helper
        $helperDir = app_path('Helpers');
        $helperFile = $helperDir . '/' . strtolower($name) . '.php';

        File::ensureDirectoryExists($helperDir);

        if (File::exists($helperFile) && !$force) {
            $this->warn("❌ Helper '{$name}' sudah ada. Gunakan --force untuk menimpa.");
        } else {
            if ($force) $this->warn("⚠️ Menimpa helper '{$name}' karena --force dipakai.");

            $stub = <<<PHP
<?php

/*
    |----------------------------------------------------------------------
    | 📌 Helper {$name}
    |----------------------------------------------------------------------
    |
    | Fitur :
    | - xxxxx
    | Tujuan :
    | - xxxxx
    | Penggunaan :
    | - xxxxx
    |
*/

// Proses Coding
if (!function_exists('{$name}Helper')) {
    function {$name}Helper(\$param = null) {
        // TODO: Implement logic
        return "Helper {$name} dijalankan dengan param: " . json_encode(\$param);
    }
}
PHP;

            File::put($helperFile, $stub);
            $this->info("✅ Helper '{$name}Helper' berhasil dibuat di: {$helperFile}");
        }

        // Auto-inject ke Helpers.php
        $helpersIndexFile = $helperDir . '/Helpers.php';
        $requireLine = "require_once __DIR__ . '/" . strtolower($name) . ".php';";

        if (File::exists($helpersIndexFile)) {
            $currentContents = File::get($helpersIndexFile);
            if (!str_contains($currentContents, $requireLine)) {
                File::append($helpersIndexFile, "\n$requireLine");
                $this->info("📌 Baris require '$name.php' ditambahkan ke Helpers.php");
            } else {
                $this->warn("⚠️ Baris require '$name.php' sudah ada di Helpers.php");
            }
        } else {
            $stubHelpers = "<?php\n$requireLine\n";
            File::put($helpersIndexFile, $stubHelpers);
            $this->info("📌 Helpers.php dibuat dan require '$name.php' ditambahkan");
        }
    })->describe('Membuat file command baru dengan komentar custom');

//php artisan make:custom-command program:shalat-berjamaah ShalatBerjamaahCommand "Untuk mengelola jadwal shalat berjamaah dan informasi terkait shalat berjamaah" ShalatBerjamaah --force
//php artisan make:custom-command my:command MyCommand "Deskripsi command" MyApp --force