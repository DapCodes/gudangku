<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barangs;
use App\Models\BarangMasuks;

\Carbon\Carbon::setLocale('id');

use RealRashid\SweetAlert\Facades\Alert;
use App\Exports\BarangMasukExport;
use App\Exports\BarangExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;


class BarangMasukController extends Controller
{
     // cek auth
     public function __construct()
     {
         $this->middleware('auth');
     }
     
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function export()
    {
        $barangMasuk = BarangMasuks::all();

        $pdf = Pdf::loadView('pdf.barangMasuk', ['barangMasuk' => $barangMasuk]);

        return $pdf->download('laporan-data-barangMasuk.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new BarangMasukExport, 'laporan-data-barangMasuk.xlsx');
    }

    public function index(Request $request)
    {
        //Pertama, saya ambil data input dari form pencarian, yaitu:
        $keyword = $request->input('search');

        //Kemudian saya ambil data dari model BarangMasuks, beserta relasi-nya ke tabel Barang, menggunakan:
        $barangMasuk = BarangMasuks::with('barang')
            // "when()" — fungsinya untuk menjalankan filter hanya kalau user mengisi pencarian. Anonymous function ini akan mengembalikan query builder.
            ->when($keyword, function ($query) use ($keyword) {
                // "whereHas()" — fungsinya untuk memfilter relasi yang ada di model BarangMasuks, yaitu: id_barang
                //saya pakai whereHas() karena saya ingin mencari dari relasi, yaitu: Nama barang (nama) atau merek barang (merek)
                $query->whereHas('barang', function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%$keyword%")
                    ->orWhere('merek', 'like', "%$keyword%");
                });
            })
            ->get();

        return view('barangmasuk.index', compact('barangMasuk', 'keyword'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $barang = Barangs::all();
        return view('barangmasuk.create', compact('barang'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $barangMasuk = new BarangMasuks;

        $lastRecord = BarangMasuks::latest('id')->first();
        $lastId = $lastRecord ? $lastRecord->id : 0;
        $kodeBarang = 'BM-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        $barangMasuk->kode_barang = $kodeBarang;

        $barangMasuk->id_barang = $request->id_barang;
        $barangMasuk->jumlah = $request->jumlah;
        $barangMasuk->tanggal_masuk = $request->tanggal_masuk;
        $barangMasuk->keterangan = $request->keterangan;

        $barang = Barangs::findOrFail($request->id_barang);
        $barang->stok += $request->jumlah;
        $barang->save();

        $barangMasuk->save();
        Alert::success('Berhasil!', 'Data Berhasil Ditambahkan');
        return redirect()->route('brg-masuk.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $barangMasuk = BarangMasuks::findOrFail($id);
        return view('barangmasuk.show', compact('barangMasuk'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $barangMasuk = BarangMasuks::findOrFail($id);
        $barang = Barangs::all();
        return view('barangmasuk.edit', compact('barangMasuk', 'barang'));
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
         $barangMasuk = BarangMasuks::findOrFail($id);
         $barangLama = Barangs::findOrFail($barangMasuk->id_barang);
     
         // Hitung stok awal sebelum barang masuk
         // $stokSebelumMasuk = $barangLama->stok - $barangMasuk->jumlah;
     
         // Cek apakah stok barang masih valid
         if ($barangLama->stok < $barangMasuk->jumlah) {
             Alert::error('Gagal!', 'Stok barang kurang, data tidak bisa diubah.');
             return redirect()->route('brg-masuk.index');
         }
     
         // Update stok lama
         $barangLama->stok -= $barangMasuk->jumlah;
         $barangLama->save();
     
         // Update stok baru
         $barangBaru = Barangs::findOrFail($request->id_barang);
         $barangBaru->stok += $request->jumlah;
         $barangBaru->save();
     
         // Update data barang masuk
         $barangMasuk->id_barang = $request->id_barang;
         $barangMasuk->jumlah = $request->jumlah;
         $barangMasuk->tanggal_masuk = $request->tanggal_masuk;
         $barangMasuk->keterangan = $request->keterangan;
         $barangMasuk->save();
     
         Alert::success('Berhasil!', 'Data berhasil diperbarui.');
         return redirect()->route('brg-masuk.index');
     }
     



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $barangMasuk = BarangMasuks::findOrFail($id);
        $barangLama = Barangs::findOrFail($barangMasuk->id_barang);
    
        // Hitung stok awal sebelum barang masuk
        $stokSebelumMasuk = $barangLama->stok - $barangMasuk->jumlah;
    
        // Cek apakah stok barang masih valid
        if ($barangLama->stok < $barangMasuk->jumlah) {
            $barangLama->stok = 0;
            $barangLama->save();
        } else {
            // Update stok barang
            $barangLama->stok -= $barangMasuk->jumlah;
            $barangLama->save();
        }

        // Hapus data barang masuk
        $barangMasuk->delete();

        Alert::success('Success', 'Data berhasil dihapus')->autoClose(1500);
        return redirect()->route('brg-masuk.index');
    }

}