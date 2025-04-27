<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BarangKeluars;
use App\Models\Barangs;
use App\Exports\BarangKeluarExport;

use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;
Carbon::setLocale('id');

class BarangKeluarController extends Controller
{

    public function export()
    {
        $barangKeluar = BarangKeluars::all();

        $pdf = Pdf::loadView('pdf.barangKeluar', ['barangKeluar' => $barangKeluar]);

        return $pdf->download('laporan-data-barangkeluar.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new BarangKeluarExport, 'laporan-data-barangkeluar.xlsx');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
         //Pertama, saya ambil data input dari form pencarian, yaitu:
            $keyword = $request->input('search');

            //Kemudian saya ambil data dari model barangKeluars, beserta relasi-nya ke tabel Barang, menggunakan:
            $barangKeluar = BarangKeluars::with('barang')
                // "when()" — fungsinya untuk menjalankan filter hanya kalau user mengisi pencarian. Anonymous function ini akan mengembalikan query builder.
                ->when($keyword, function ($query) use ($keyword) {
                    // "whereHas()" — fungsinya untuk memfilter relasi yang ada di model barangKeluars, yaitu: id_barang
                    //saya pakai whereHas() karena saya ingin mencari dari relasi, yaitu: Nama barang (nama) atau merek barang (merek)
                    $query->whereHas('barang', function ($q) use ($keyword) {
                        $q->where('nama', 'like', "%$keyword%")
                        ->orWhere('merek', 'like', "%$keyword%");
                    });
                })
                ->get();
    
            return view('barangkeluar.index', compact('barangKeluar', 'keyword'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Ambil data dari model Barangs
        $barang = Barangs::all();

        //Tampilkan data ke view barang_keluar.create
        return view('barangkeluar.create', compact('barang'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $barangKeluar = new BarangKeluars;

        $lastRecord = BarangKeluars::latest('id')->first();
        $lastId = $lastRecord ? $lastRecord->id : 0;
        $kodeBarang = 'BK-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        $barangKeluar->kode_barang = $kodeBarang;

        $barangKeluar->jumlah = $request->jumlah;
        $barangKeluar->tanggal_keluar = $request->tanggal_keluar;
        $barangKeluar->keterangan = $request->keterangan;
        $barangKeluar->id_barang = $request->id_barang;


        $barang = Barangs::findOrFail($request->id_barang);

        if ($barang->stok < $request->jumlah) {
            Alert::error('Gagal!', 'Stok tidak mencukupi');
            return redirect()->back();
        }
        // Update stok barang
        $barang->stok -= $request->jumlah;
        $barang->save();

        $barangKeluar->save();
        Alert::success('Berhasil!', 'Data Berhasil Ditambahkan');
        return redirect()->route('brg-keluar.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $barangKeluar = BarangKeluars::findOrFail($id);
        return view('barangkeluar.show', compact('barangKeluar'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $barangKeluar = BarangKeluars::findOrFail($id);
        $barang = Barangs::all();

        return view('barangkeluar.edit', compact('barangKeluar', 'barang'));
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
        $barangKeluar = BarangKeluars::findOrFail($id);

        $barangKeluar->jumlah = $request->jumlah;
        $barangKeluar->tanggal_keluar = $request->tanggal_keluar;
        $barangKeluar->keterangan = $request->keterangan;
        $barangKeluar->id_barang = $request->id_barang;

        // Ambil stok lama dan update stok di barang lama
        $barangLama = Barangs::findOrFail($barangKeluar->id_barang);
        $barangLama->stok += $barangKeluar->jumlah;
        $barangLama->save();

        // Update stok barang
        $barang = Barangs::findOrFail($request->id_barang);
        if ($barang->stok < $request->jumlah) {
            Alert::error('Gagal!', 'Stok tidak mencukupi');
            return redirect()->back();
        }

        $barang->stok -= $request->jumlah;
        $barang->save();

        $barangKeluar->save();
        Alert::success('Berhasil!', 'Data Berhasil Diperbarui');
        return redirect()->route('brg-keluar.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $barangKeluar = BarangKeluars::findOrFail($id);
        $barang = Barangs::findOrFail($barangKeluar->id_barang);

        // Update stok barang
        $barang->stok += $barangKeluar->jumlah;
        $barang->save();

        $barangKeluar->delete();
        Alert::success('Berhasil!', 'Data Berhasil Dihapus');
        return redirect()->route('brg-keluar.index');
    }
}
