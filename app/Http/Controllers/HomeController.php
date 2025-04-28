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
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $barang = Barangs::count();
        $peminjaman = Peminjamans::count();
        $pengembalian = Pengembalians::count();
        $karyawan = User::where('is_admin', 0)->count();
        $barangMasuk = BarangMasuks::count();
        $barangKeluar = BarangKeluars::count();
        $total = $barang + $peminjaman + $pengembalian + $karyawan + $barangMasuk + $barangKeluar;
        return view('home', compact('barang', 'peminjaman', 'pengembalian', 'karyawan', 'barangMasuk', 'barangKeluar', 'total'));
    }
}
