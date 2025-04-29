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

    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = BarangMasuks::with('barang')
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('barang', function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%$keyword%")
                      ->orWhere('merek', 'like', "%$keyword%");
                });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_masuk', [$startDate, $endDate]);
            })
            ->when($startDate && !$endDate, function ($query) use ($startDate) {
                $query->whereDate('tanggal_masuk', '>=', $startDate);
            })
            ->when(!$startDate && $endDate, function ($query) use ($endDate) {
                $query->whereDate('tanggal_masuk', '<=', $endDate);
            });

        $barangMasuk = $query->get();

        // Kalau tombol export ditekan
        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return Excel::download(new BarangMasukExport($barangMasuk), 'laporan-data-barangMasuk.xlsx');
            } elseif ($request->export == 'pdf') {
                $pdf = Pdf::loadView('pdf.barangMasuk', ['barangMasuk' => $barangMasuk]);
                return $pdf->download('laporan-data-barangMasuk.pdf');
            }
        }

        $barangMasuk = $query->paginate(10)->withQueryString();

        return view('barangmasuk.index', compact('barangMasuk', 'keyword', 'startDate', 'endDate'));
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
        $request->validate([
            'id_barang' => 'required',
            'jumlah' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
            'keterangan' => 'required|string|max:255',
        ],
        [
            'id_barang.required' => 'Barang harus dipilih',
            'jumlah.required' => 'Jumlah barang harus diisi',
            'jumlah.integer' => 'Jumlah barang harus berupa angka',
            'jumlah.min' => 'Jumlah barang minimal 1',
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
            'tanggal_masuk.date' => 'Format tanggal tidak valid',
            'keterangan.required' => 'Keterangan harus diisi',
            'keterangan.string' => 'Keterangan harus berupa teks',
            'keterangan.max' => 'Keterangan maksimal 255 karakter',
        ]);

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
            $request->validate([
                'id_barang' => 'required',
                'jumlah' => 'required|integer|min:1',
                'tanggal_masuk' => 'required|date',
                'keterangan' => 'required|string|max:255',
            ],
            [
                'id_barang.required' => 'Barang harus dipilih',
                'jumlah.required' => 'Jumlah barang harus diisi',
                'jumlah.integer' => 'Jumlah barang harus berupa angka',
                'jumlah.min' => 'Jumlah barang minimal 1',
                'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
                'tanggal_masuk.date' => 'Format tanggal tidak valid',
                'keterangan.required' => 'Keterangan tidak boleh kosong',
                'keterangan.string' => 'Keterangan harus berupa teks',
                'keterangan.max' => 'Keterangan maksimal 255 karakter',
            ]);
            
         $barangMasuk = BarangMasuks::findOrFail($id);
         $barangLama = Barangs::findOrFail($barangMasuk->id_barang);
     
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