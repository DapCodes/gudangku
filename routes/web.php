<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\HomeController;

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
    return redirect()->route('login');
});

// Logout route
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// Disable registration
Auth::routes(['register' => false]);

// Role Admin
Route::prefix('admin')->middleware('auth', RoleMiddleware::class)->group(function() {

    // Admin Home route (changed from resource)
    Route::get('home', [HomeController::class, 'index'])->name('admin.home');

    // Barang Routes
    Route::resource('barang', BarangController::class);
    Route::get('barang-export', [BarangController::class, 'export'])->name('barang.export');
    Route::get('admin/barang-export-excel', [BarangController::class, 'exportExcel'])->name('barang.export.excel');

    // Barang Masuk Routes
    Route::resource('brg-masuk', BarangMasukController::class);
    Route::get('brg-masuk-export', [BarangMasukController::class, 'export'])->name('brg-masuk.export');
    Route::get('admin/brg-masuk-export-excel', [BarangMasukController::class, 'exportExcel'])->name('brg-masuk.export.excel');

    // Karyawan Routes
    Route::resource('karyawan', KaryawanController::class);
    Route::get('karyawan-export', [KaryawanController::class, 'export'])->name('karyawan.export');
    Route::get('admin/karyawan-export-excel', [KaryawanController::class, 'exportExcel'])->name('karyawan.export.excel');

    // Barang Keluar Routes
    Route::resource('brg-keluar', BarangKeluarController::class);
    Route::get('brg-keluar-export', [BarangKeluarController::class, 'export'])->name('brg-keluar.export');
    Route::get('admin/brg-keluar-export-excel', [BarangKeluarController::class, 'exportExcel'])->name('brg-keluar.export.excel');

    // Peminjaman Routes
    Route::resource('peminjaman', PeminjamanController::class);
    Route::get('peminjaman-export', [PeminjamanController::class, 'export'])->name('peminjaman.export');
    Route::get('admin/peminjaman-export-excel', [PeminjamanController::class, 'exportExcel'])->name('peminjaman.export.excel');

    // Pengembalian Routes
    Route::resource('pengembalian', PengembalianController::class);
    Route::get('pengembalian-export', [PengembalianController::class, 'export'])->name('pengembalian.export');
    Route::get('admin/pengembalian-export-excel', [PengembalianController::class, 'exportExcel'])->name('pengembalian.export.excel');
});