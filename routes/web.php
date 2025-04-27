<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\PeminjamanController;

// Middleware
use App\Http\Middleware\RoleMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Role Admin
route::prefix('admin')->middleware('auth', RoleMiddleware::class)->group(function() {

    Route::resource('barang', BarangController::class);
    Route::get('barang-export', [BarangController::class, 'export'])->name('barang.export');
    Route::get('admin/barang-export-excel', [BarangController::class, 'exportExcel'])->name('barang.export.excel');

    Route::resource('brg-masuk', BarangMasukController::class);
    Route::get('brg-masuk-export', [BarangMasukController::class, 'export'])->name('brg-masuk.export');
    Route::get('admin/brg-masuk-export-excel', [BarangMasukController::class, 'exportExcel'])->name('brg-masuk.export.excel');

    Route::resource('karyawan', KaryawanController::class);
    Route::get('karyawan-export', [KaryawanController::class, 'export'])->name('karyawan.export');
    Route::get('admin/karyawan-export-excel', [KaryawanController::class, 'exportExcel'])->name('karyawan.export.excel');

    Route::resource('brg-keluar', BarangKeluarController::class);
    Route::get('brg-keluar-export', [BarangKeluarController::class, 'export'])->name('brg-keluar.export');
    Route::get('admin/brg-keluar-export-excel', [BarangKeluarController::class, 'exportExcel'])->name('brg-keluar.export.excel');

    Route::resource('peminjaman', PeminjamanController::class);

    Route::resource('pengembalian', BarangController::class);

});
