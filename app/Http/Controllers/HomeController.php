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
        $peminjaman = Peminjamans::count();
        $pengembalian = Pengembalians::count();
        $karyawan = User::where('is_admin', 0)->count();
        $barangMasuk = BarangMasuks::count();
        $barangKeluar = BarangKeluars::count();

        $chartData = [
            'labels' => ['Barang', 'Peminjaman', 'Pengembalian', 'Karyawan', 'Barang Masuk', 'Barang Keluar'],
            'series' => [$barang, $peminjaman, $pengembalian, $karyawan, $barangMasuk, $barangKeluar]
        ];

        return view('home', compact('chartData', 'barang', 'peminjaman', 'pengembalian', 'karyawan', 'barangMasuk', 'barangKeluar'));
    }
}
