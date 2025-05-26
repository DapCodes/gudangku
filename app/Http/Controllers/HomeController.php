<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barangs;
use App\Models\Peminjamans;
use App\Models\Pengembalians;
use App\Models\User;
use App\Models\BarangMasuks;
use App\Models\BarangKeluars;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $barang = Barangs::count();
        $peminjaman = Peminjamans::where('status', 'Sedang Dipinjam')->count();
        $pengembalian = Pengembalians::count();
        $karyawan = User::where('is_admin', 0)->count();
        $barangMasuk = BarangMasuks::count();
        $barangKeluar = BarangKeluars::count();

        // Mengambil total stok masuk dan keluar
        $totalStokMasuk = BarangMasuks::sum('jumlah'); // Sesuaikan nama kolom jika bukan 'jumlah'
        $totalStokKeluar = BarangKeluars::sum('jumlah'); // Sesuaikan nama kolom juga

        $total = $barang + $peminjaman + $pengembalian + $karyawan + $barangMasuk + $barangKeluar;

        $chartData = [
            'labels' => ['Barang', 'Petugas', 'Barang Masuk', 'Barang Keluar', 'Peminjaman', 'Pengembalian'],
            'series' => [$barang, $karyawan, $barangMasuk, $barangKeluar, $peminjaman, $pengembalian],
            'pinjamkembali' => ['Peminjaman', 'Pengembalian'],
            'pinjamkembaliseries' => [$peminjaman, $pengembalian]
        ];

        return view('home', compact(
            'chartData',
            'barang',
            'peminjaman',
            'pengembalian',
            'karyawan',
            'barangMasuk',
            'barangKeluar',
            'total',
            'totalStokMasuk',
            'totalStokKeluar'
        ));
    }

}
