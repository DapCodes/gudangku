<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Peminjamans;
use App\Models\Barangs;
use App\Exports\PeminjamanExport;
use App\Models\Pengembalians;

use RealRashid\SweetAlert\Facades\Alert;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
Carbon::setLocale('id');


class PeminjamanController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function export()
    {
        $peminjaman = Peminjamans::with('barang')->where('status', 'Sedang Dipinjam')->get();

        $pdf = Pdf::loadView('pdf.peminjaman', ['peminjaman' => $peminjaman]);

        return $pdf->download('laporan-data-peminjaman.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new PeminjamanExport, 'laporan-data-peminjaman.xlsx');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->input('search');

        $query = Peminjamans::with('barang')
                    ->where('status', 'Sedang Dipinjam'); // filter status dulu

        if ($keyword) {
            $query->whereHas('barang', function ($q) use ($keyword) {
                $q->where('nama', 'like', "%$keyword%")
                ->orWhere('merek', 'like', "%$keyword%");
            });
        }

        $peminjaman = $query->get();

        return view('peminjaman.index', compact('peminjaman'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get all barang data
        $barang = Barangs::all();

        // Return the view with barang data
        return view('peminjaman.create', compact('barang'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $peminjaman = new Peminjamans;

        $lastRecord = Peminjamans::latest('id')->first();
        $lastId = $lastRecord ? $lastRecord->id : 0;
        $kodeBarang = 'BP-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        $peminjaman->kode_barang = $kodeBarang;

        $peminjaman->jumlah = $request->jumlah;
        $peminjaman->tanggal_pinjam = $request->tanggal_pinjam;
        $peminjaman->tanggal_kembali = $request->tanggal_kembali;
        $peminjaman->nama_peminjam = $request->nama_peminjam;
        $peminjaman->id_barang = $request->id_barang;
        $peminjaman->status = "Sedang Dipinjam";

        $barang = Barangs::findOrFail($request->id_barang);
        if ($barang->stok < $request->jumlah) {
            Alert::warning('Warning', 'Stok Tidak Cukup')->autoClose(1500);
            return redirect()->route('peminjaman.create');
        } else {
            $barang->stok -= $request->jumlah;
            $barang->save();
        }

        $peminjaman->save();
        Alert::success('Success', 'Data Berhasil Ditambahkan')->autoclose(1500);
        return redirect()->route('peminjaman.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $peminjaman = Peminjamans::findOrFail($id);
        $barang = Barangs::findOrFail($peminjaman->id_barang);
        return view('peminjaman.show', compact('peminjaman', 'barang'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $peminjaman = Peminjamans::findOrFail($id);
        $barang = Barangs::all();
        return view('peminjaman.edit', compact('peminjaman', 'barang'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $peminjaman = Peminjamans::findOrFail($id);

        $barangLama = Barangs::findOrFail($peminjaman->id_barang);
        $barangBaru = Barangs::findOrFail($request->id_barang);

        // Jika status menjadi "Sudah Dikembalikan"
        if ($request->status == "Sudah Dikembalikan") {
            // Tambahkan stok barang lama
            $barangLama->stok += $peminjaman->jumlah;
            $barangLama->save();

            // Buat record di tabel pengembalian
            $lastRecord = Pengembalians::latest('id')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $kodeBarang = 'BB-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

            $pengembalian = new Pengembalians();
            $pengembalian->kode_barang = $kodeBarang;
            $pengembalian->jumlah = $peminjaman->jumlah;
            $pengembalian->tanggal_kembali = $request->tanggal_kembali;
            $pengembalian->nama_peminjam = $peminjaman->nama_peminjam;
            $pengembalian->status = $request->status;
            $pengembalian->id_peminjam = $peminjaman->id;
            $pengembalian->id_barang = $peminjaman->id_barang;
            $pengembalian->save();

            // Update status peminjaman menjadi "Sudah Dikembalikan"
            $peminjaman->status = 'Sudah Dikembalikan';
            $peminjaman->save();

            Alert::success('Success', 'Data Berhasil Dikembalikan')->autoClose(1500);
            return redirect()->route('peminjaman.index');
        }

        // Jika status masih "Sedang Dipinjam"
        if ($request->status == "Sedang Dipinjam") {
            $jumlahBaru = $request->jumlah;

            if ($barangBaru->stok < $jumlahBaru) {
                Alert::warning('Warning', 'Stok Tidak Cukup')->autoClose(1500);
                return redirect()->route('peminjaman.index');
            }

            if ($peminjaman->id_barang != $request->id_barang) {
                // Barang dipinjam berubah
                $barangLama->stok += $peminjaman->jumlah;
                $barangLama->save();

                $barangBaru->stok -= $jumlahBaru;
                $barangBaru->save();
            } else {
                // Barang sama, update stok
                $barangLama->stok += $peminjaman->jumlah;
                $barangLama->stok -= $jumlahBaru;
                $barangLama->save();
            }

            // Update data peminjaman
            $peminjaman->update([
                'id_barang' => $request->id_barang,
                'jumlah' => $request->jumlah,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'status' => $request->status,
                'nama_peminjam' => $request->nama_peminjam,
            ]);

            Alert::success('Success', 'Data Berhasil Diubah')->autoClose(1500);
            return redirect()->route('peminjaman.index');
        }

        // Jika status tidak valid
        Alert::error('Error', 'Status tidak valid')->autoClose(1500);
        return redirect()->route('peminjaman.index');
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $peminjaman = Peminjamans::findOrFail($id);
        $barang = Barangs::findOrFail($peminjaman->id_barang);

        if ($peminjaman->status == "Sedang Dipinjam") {
            Alert::warning('Warning', 'Data Tidak Bisa Dihapus')->autoClose(2500);
            return redirect()->route('peminjaman.index');
        }
        // Hapus data peminjaman
        $peminjaman->delete();

        Alert::success('Success', 'Data Berhasil Dihapus')->autoClose(2500);
        return redirect()->route('pengembalian.index');
    }
}
