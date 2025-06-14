<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\RuangansController;
use App\Http\Controllers\BarangRuangansController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StatistikController;

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

    // Admin Statistik
    Route::get('statistik', [StatistikController::class, 'index'])->name('admin.statistik');

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
    Route::get('/get-barang-by-ruangan/{ruanganId}', [BarangKeluarController::class, 'getBarangByRuangan']);

    // Peminjaman Routes
    Route::resource('peminjaman', PeminjamanController::class);
    Route::get('peminjaman-export', [PeminjamanController::class, 'export'])->name('peminjaman.export');
    Route::get('admin/peminjaman-export-excel', [PeminjamanController::class, 'exportExcel'])->name('peminjaman.export.excel');
    Route::get('/get-barang-by-ruangan/{ruanganId}', [BarangKeluarController::class, 'getBarangByRuangan']);
    

    // Pengembalian Routes
    Route::resource('pengembalian', PengembalianController::class);
    Route::get('pengembalian-export', [PengembalianController::class, 'export'])->name('pengembalian.export');
    Route::get('admin/pengembalian-export-excel', [PengembalianController::class, 'exportExcel'])->name('pengembalian.export.excel');

    // Ruangan Routes
    Route::resource('ruangan', RuangansController::class);
    Route::get('ruangan-export', [RuangansController::class, 'export'])->name('ruangan.export');
    Route::get('admin/ruangan-export-excel', [RuangansController::class, 'exportExcel'])->name('ruangan.export.excel');

    // Barang Ruangan Routes
    Route::resource('brg-ruangan', BarangRuangansController::class);
    Route::get('brg-ruangan-export', [BarangRuangansController::class, 'export'])->name('brg-ruangan.export');
    Route::get('admin/brg-ruangan-export-excel', [BarangRuangansController::class, 'exportExcel'])->name('brg-ruangan.export.excel');
    
});