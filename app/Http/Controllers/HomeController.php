<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barangs;
use App\Models\Peminjamans;
use App\Models\Pengembalians;
use App\Models\User;
use App\Models\BarangMasuks;
use App\Models\Ruangans;
use App\Models\BarangKeluars;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // Cek apakah user adalah admin
        $isAdmin = $user->status_user === 'admin';

        // Barang (semua tetap dihitung)
        $barang = Barangs::count();

        // Peminjaman
        $peminjaman = Peminjamans::when(!$isAdmin, function ($query) use ($user) {
            $query->whereHas('ruangan', function ($q) use ($user) {
                $q->where('deskripsi', $user->status_user);
            });
        })->where('status', 'Sedang Dipinjam')->count();

        // Pengembalian
        $pengembalian = Pengembalians::when(!$isAdmin, function ($query) use ($user) {
            $query->whereHas('ruangan', function ($q) use ($user) {
                $q->where('deskripsi', $user->status_user);
            });
        })->count();

        // Ruangan
        $ruangan = Ruangans::when(!$isAdmin, function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->Where('deskripsi', $user->status_user);
            });
        })->count();

        // Barang Masuk
        $barangMasuk = BarangMasuks::when(!$isAdmin, function ($query) use ($user) {
            $query->whereHas('ruangan', function ($q) use ($user) {
                $q->where('deskripsi', $user->status_user);
            });
        })->count();

        // Barang Keluar
        $barangKeluar = BarangKeluars::when(!$isAdmin, function ($query) use ($user) {
            $query->whereHas('ruangan', function ($q) use ($user) {
                $q->where('deskripsi', $user->status_user);
            });
        })->count();

        // Total Stok Masuk
        $totalStokMasuk = BarangMasuks::when(!$isAdmin, function ($query) use ($user) {
            $query->whereHas('ruangan', function ($q) use ($user) {
                $q->where('deskripsi', $user->status_user);
            });
        })->sum('jumlah');

        // Total Stok Keluar
        $totalStokKeluar = BarangKeluars::when(!$isAdmin, function ($query) use ($user) {
            $query->whereHas('ruangan', function ($q) use ($user) {
                $q->where('deskripsi', $user->status_user);
            });
        })->sum('jumlah');

        $total = $barang + $peminjaman + $pengembalian + $ruangan + $barangMasuk + $barangKeluar;

        $chartData = [
            'labels' => ['Barang', 'Ruangan', 'Barang Masuk', 'Barang Keluar', 'Peminjaman', 'Pengembalian'],
            'series' => [$barang, $ruangan, $barangMasuk, $barangKeluar, $peminjaman, $pengembalian],
            'pinjamkembali' => ['Peminjaman', 'Pengembalian'],
            'pinjamkembaliseries' => [$peminjaman, $pengembalian]
        ];

        return view('home', compact(
            'chartData',
            'barang',
            'peminjaman',
            'pengembalian',
            'ruangan',
            'barangMasuk',
            'barangKeluar',
            'total',
            'totalStokMasuk',
            'totalStokKeluar'
        ));
    }


}
