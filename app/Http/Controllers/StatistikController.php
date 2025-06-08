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
use Carbon\Carbon;

Carbon::setLocale('id');


class StatistikController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->status_user === 'admin';

        $satuBulanLalu = Carbon::now()->subDays(7);

        if ($isAdmin) {
            $barang = Barangs::count();
        } else {
            $barang = Barangs::where('status_barang', $user->status_user)->count();
        }

        $peminjaman = Peminjamans::where('tanggal_pinjam', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('ruangan', function ($q) use ($user) {
                    $q->where('deskripsi', $user->status_user);
                });
            })
            ->where('status', 'Sedang Dipinjam')
            ->count();
        
        $peminjamanStok = Peminjamans::where('tanggal_pinjam', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('ruangan', function ($q) use ($user) {
                    $q->where('deskripsi', $user->status_user);
                });
            })
            ->where('status', 'Sedang Dipinjam')
            ->sum('jumlah');


        // Pengembalian (dalam 1 bulan terakhir)
        $pengembalian = Pengembalians::where('tanggal_kembali', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('ruangan', function ($q) use ($user) {
                    $q->where('deskripsi', $user->status_user);
                });
            })
            ->count();
        
        $pengembalianStok = Pengembalians::where('tanggal_kembali', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('ruangan', function ($q) use ($user) {
                    $q->where('deskripsi', $user->status_user);
                });
            })
            ->sum('jumlah');

        // Ruangan
        $ruangan = Ruangans::when(!$isAdmin, function ($query) use ($user) {
            $query->where('deskripsi', $user->status_user);
        })->count();

        // Barang Masuk (dalam 1 bulan terakhir)
        $barangMasuk = BarangMasuks::where('tanggal_masuk', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('ruangan', function ($q) use ($user) {
                    $q->where('deskripsi', $user->status_user);
                });
            })
            ->count();

        // Barang Keluar (dalam 1 bulan terakhir)
        $barangKeluar = BarangKeluars::where('tanggal_keluar', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('ruangan', function ($q) use ($user) {
                    $q->where('deskripsi', $user->status_user);
                });
            })
            ->count();

        // Total Stok Masuk (dalam 1 bulan terakhir)
        $totalStokMasuk = BarangMasuks::where('tanggal_masuk', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('ruangan', function ($q) use ($user) {
                    $q->where('deskripsi', $user->status_user);
                });
            })
            ->sum('jumlah');

        // Total Stok Keluar (dalam 1 bulan terakhir)
        $totalStokKeluar = BarangKeluars::where('tanggal_keluar', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('ruangan', function ($q) use ($user) {
                    $q->where('deskripsi', $user->status_user);
                });
            })
            ->sum('jumlah');

        $total = $barang + $peminjaman + $pengembalian + $ruangan + $barangMasuk + $barangKeluar;

        $chartData = [
            'labels' => ['Barang', 'Ruangan', 'Barang Masuk', 'Barang Keluar', 'Peminjaman', 'Pengembalian'],
            'series' => [$barang, $ruangan, $barangMasuk, $barangKeluar, $peminjaman, $pengembalian],
            'pinjamkembali' => ['Peminjaman', 'Pengembalian'],
            'pinjamkembaliseries' => [$peminjaman, $pengembalian]
        ];

        return view('statistik', compact(
            'chartData',
            'barang',
            'peminjaman',
            'peminjamanStok',
            'pengembalian',
            'pengembalianStok',
            'ruangan',
            'barangMasuk',
            'barangKeluar',
            'total',
            'totalStokMasuk',
            'totalStokKeluar'
        ));
    }



}
