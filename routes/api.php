<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiLaporanMasyarakatController; 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rute API untuk Laporan Masyarakat (publik)
// Ini tidak dilindungi oleh middleware 'auth' sehingga bisa diakses siapapun
Route::post('/laporan-masyarakat', [ApiLaporanMasyarakatController::class, 'store']);

// Contoh rute API yang dilindungi (misal untuk admin/relawan via API token)
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/laporan-masyarakat/{id}', [ApiLaporanMasyarakatController::class, 'show']);
//     Route::put('/laporan-masyarakat/{id}', [ApiLaporanMasyarakatController::class, 'update']);
//     Route::delete('/laporan-masyarakat/{id}', [ApiLaporanMasyarakatController::class, 'destroy']);
// });

Route::get('/test', function () {
    return response()->json(['message' => 'Hello Dunia!'], 200);
});