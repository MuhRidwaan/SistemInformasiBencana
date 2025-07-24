<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PermissionModuleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JenisBencanaController;
use App\Http\Controllers\ProvinsiController;
use App\Http\Controllers\KotaController;
use App\Http\Controllers\KecamatanController;
use App\Http\Controllers\KelurahanController;
use App\Http\Controllers\BencanaController;
use App\Http\Controllers\LokasiPoskoController;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use App\Http\Controllers\KebutuhanLogistikController;
use App\Http\Controllers\RelawanController;
use App\Http\Controllers\UpayaPenangananController;
use App\Http\Controllers\KerusakanController;
use App\Http\Controllers\KorbanController;
use App\Http\Controllers\LaporanMasyarakatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rute untuk menampilkan form login
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
// Rute untuk memproses permintaan login
Route::post('login', [AuthController::class, 'login'])->name('login.post');

Route::middleware(['auth'])->group(function () {
    Route::get('/relawan/get-data-by-user', [RelawanController::class, 'getRelawanDataByUser'])->name('relawan.get-data-by-user');
});

// Grup rute yang memerlukan otentikasi (middleware 'auth')
Route::middleware(['auth'])->group(function () {

    // Rute root aplikasi. Jika user sudah login, arahkan ke dashboard.
    // Jika tidak login, middleware 'auth' akan mengarahkan ke rute 'login'.
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Rute Dashboard
    // Menggunakan nama 'dashboard' sesuai dengan yang ada di AuthController
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Rute untuk manajemen Role
    Route::resource('roles', RoleController::class);

    // Rute untuk manajemen User (kecuali 'create' dan 'show' yang mungkin ditangani via AJAX/modal)
    Route::resource('users', UserController::class)->except(['create', 'show']);
    // Rute khusus untuk export users
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');


    // Rute untuk manajemen Permission
    Route::resource('permissions', PermissionController::class)->except(['create', 'show']);

    // Rute untuk manajemen Modul Izin
    Route::resource('permission-modules', PermissionModuleController::class)->except(['create', 'show']);

    // Rute untuk manajemen Jenis Bencana
    Route::resource('jenis-bencana', JenisBencanaController::class)->except(['create', 'show']);

    // Rute untuk manajemen Provinsi
    Route::resource('provinsi', ProvinsiController::class)->except(['create', 'show']);

    // Rute untuk manajemen Kota/Kabupaten
    Route::resource('kota', KotaController::class)->except(['create', 'show']);
    // Rute AJAX untuk mendapatkan kota berdasarkan provinsi
    Route::get('get-kota-by-provinsi/{provinsi_id}', [KotaController::class, 'getKotaByProvinsi']);


    // Rute untuk manajemen Kecamatan
    Route::resource('kecamatan', KecamatanController::class)->except(['create', 'show']);
    // Rute AJAX untuk mendapatkan kecamatan berdasarkan kota
    Route::get('get-kecamatan-by-kota/{kota_id}', [KecamatanController::class, 'getKecamatanByKota']);


    // Rute untuk manajemen Kelurahan
    Route::resource('kelurahan', KelurahanController::class)->except(['create', 'show']);
    // Rute AJAX untuk mendapatkan kelurahan berdasarkan kecamatan
    Route::get('get-kelurahan-by-kecamatan/{kecamatan_id}', [KelurahanController::class, 'getKelurahanByKecamatan']);

    // Rute untuk manajemen Data Bencana
    Route::resource('bencana', BencanaController::class);

    // Rute untuk manajemen Kebutuhan Logistik
    Route::resource('kebutuhan-logistik', KebutuhanLogistikController::class);

    // Rute untuk manajemen Lokasi Posko
    Route::resource('lokasi-posko', LokasiPoskoController::class);

    // Rute untuk manajemen Relawan (modul baru)
    Route::resource('relawan', RelawanController::class);

    // Rute untuk manajemen Upaya Penanganan
    Route::resource('upaya-penanganan', UpayaPenangananController::class);
    // Route::resource('relawan', RelawanController::class);

    Route::resource('kerusakan', KerusakanController::class);
    Route::resource('korban', KorbanController::class);
    Route::resource('laporan-masyarakat', LaporanMasyarakatController::class);
    Route::post('laporan-masyarakat/{laporanMasyarakat}/status/{status}', [LaporanMasyarakatController::class, 'updateStatus'])->name('laporan-masyarakat.updateStatus');

    Route::get('kerusakan/export', [KerusakanController::class, 'export'])->name('kerusakan.export');
    
    


    // Rute untuk logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

// Anda bisa menambahkan rute lain yang tidak memerlukan otentikasi di luar grup middleware 'auth'
// Contoh: Route::get('/about', function() { return view('about'); });
