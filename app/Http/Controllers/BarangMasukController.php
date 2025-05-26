<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Barangs;
use App\Models\BarangMasuks;
use App\Models\Ruangans;
use App\Models\BarangRuangans;
use RealRashid\SweetAlert\Facades\Alert;
use App\Exports\BarangMasukExport;
use App\Exports\BarangExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

\Carbon\Carbon::setLocale('id');

class BarangMasukController extends Controller
{
     // cek auth
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user(); // Ambil user yang sedang login

        $keyword = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = BarangMasuks::with('barang')
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('barang', function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%$keyword%")
                    ->orWhere('merek', 'like', "%$keyword%");
                })
                ->orWhereHas('ruangan', function ($q) use ($keyword) {
                    $q->where('nama_ruangan', 'like', "%$keyword%");
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
            })
            ->when($user->status_user !== 'admin', function ($query) use ($user) {
                $query->whereHas('barang', function ($q) use ($user) {
                    $q->where('status_barang', $user->status_user);
                });
            });

        // Untuk export, ambil semua data
        $barangMasukForExport = $query->get();

        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return Excel::download(new BarangMasukExport($barangMasukForExport), 'laporan-data-barangMasuk.xlsx');
            } elseif ($request->export == 'pdf') {
                $pdf = Pdf::loadView('pdf.barangMasuk', ['barangMasuk' => $barangMasukForExport]);
                return $pdf->download('laporan-data-barangMasuk.pdf');
            }
        }

        // Jika tidak export, tampilkan dengan paginate
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
        $user = Auth::user();
        if ($user->status_user === 'admin' || $user->status_user === 'Umum') {
            $barang = Barangs::all(); 
            $ruangan = Ruangans::all();
        } else {
            $barang = Barangs::where('status_barang', $user->status_user)->get();
            $ruangan = Ruangans::where('deskripsi', $user->status_user)->get();
        }

        return view('barangmasuk.create', compact('barang', 'ruangan'));
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

        if ($request->deskripsi) {
            $barangRuangan = new BarangRuangans;

            $cek = BarangRuangans::where('barang_id', $request->id_barang)
            ->where('ruangan_id', $request->deskripsi)
            ->first();

            if ($cek) {
                $cek->stok += $request->jumlah;
                $cek->save();
            } else {
                $barangRuangan->barang_id = $request->id_barang;
                $barangRuangan->ruangan_id = $request->deskripsi;
                $barangRuangan->stok = $request->jumlah;
                $barangRuangan->save();
            }

        }

        $lastRecord = BarangMasuks::latest('id')->first();
        $lastId = $lastRecord ? $lastRecord->id : 0;
        $kodeBarang = 'BM-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        $barangMasuk->kode_barang = $kodeBarang;

        $barangMasuk->id_barang = $request->id_barang;
        $barangMasuk->jumlah = $request->jumlah;
        $barangMasuk->tanggal_masuk = $request->tanggal_masuk;
        $barangMasuk->keterangan = $request->keterangan;
        $barangMasuk->ruangan_id = $request->deskripsi;

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
        $user = Auth::user();
        $barangMasuk = BarangMasuks::findOrFail($id);

        if ($user->status_user === 'admin' || $user->status_user === 'Umum') {
            $barang = Barangs::all();
            $ruangan = Ruangans::all();
        } else {
            $barang = Barangs::where('status_barang', $user->status_user)->get();
            $ruangan = Ruangans::where('deskripsi', $user->status_user)->get();
        }

        // Cari barangRuangan berdasarkan barang_id yang ada di $barangMasuk
        $barangRuangan = BarangRuangans::where('barang_id', $barangMasuk->id_barang)->first();

        return view('barangmasuk.edit', compact('barangMasuk', 'barang', 'ruangan', 'barangRuangan'));
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
             'ruangan_id' => 'required|exists:ruangans,id',
         ], [
             'id_barang.required' => 'Barang harus dipilih',
             'jumlah.required' => 'Jumlah barang harus diisi',
             'jumlah.integer' => 'Jumlah barang harus berupa angka',
             'jumlah.min' => 'Jumlah barang minimal 1',
             'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
             'tanggal_masuk.date' => 'Format tanggal tidak valid',
             'keterangan.required' => 'Keterangan tidak boleh kosong',
             'keterangan.string' => 'Keterangan harus berupa teks',
             'keterangan.max' => 'Keterangan maksimal 255 karakter',
             'ruangan_id.required' => 'Ruangan harus dipilih',
             'ruangan_id.exists' => 'Ruangan tidak valid',
         ]);
     
         $barangMasuk = BarangMasuks::findOrFail($id);
     
         // Kurangi stok dari barang lama
         $barangLama = Barangs::findOrFail($barangMasuk->id_barang);
         if ($barangLama->stok < $barangMasuk->jumlah) {
             Alert::error('Gagal!', 'Stok barang kurang, data tidak bisa diubah.');
             return redirect()->route('brg-masuk.index');
         }
         $barangLama->stok -= $barangMasuk->jumlah;
         $barangLama->save();
     
         // Tambahkan stok ke barang baru
         $barangBaru = Barangs::findOrFail($request->id_barang);
         $barangBaru->stok += $request->jumlah;
         $barangBaru->save();
     
         // Kurangi stok lama dari barang_ruangans
         $oldBarangRuangan = BarangRuangans::where('barang_id', $barangMasuk->id_barang)
             ->where('ruangan_id', $barangMasuk->ruangan_id)
             ->first();
     
         if ($oldBarangRuangan) {
             $oldBarangRuangan->stok -= $barangMasuk->jumlah;
             if ($oldBarangRuangan->stok <= 0) {
                 $oldBarangRuangan->delete();
             } else {
                 $oldBarangRuangan->save();
             }
         }
     
         // Tambahkan stok ke barang_ruangans baru
         $newBarangRuangan = BarangRuangans::firstOrNew([
             'barang_id' => $request->id_barang,
             'ruangan_id' => $request->ruangan_id,
         ]);
     
         if (!$newBarangRuangan->exists) {
             $newBarangRuangan->stok = 0;
         }
     
         $newBarangRuangan->stok += $request->jumlah;
         $newBarangRuangan->save();
     
         // Update data barang masuk
         $barangMasuk->id_barang = $request->id_barang;
         $barangMasuk->jumlah = $request->jumlah;
         $barangMasuk->tanggal_masuk = $request->tanggal_masuk;
         $barangMasuk->keterangan = $request->keterangan;
         $barangMasuk->ruangan_id = $request->ruangan_id;
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

        // Update stok utama di tabel barangs
        $barangLama->stok = max(0, $barangLama->stok - $barangMasuk->jumlah);
        $barangLama->save();

        // Update stok di barang_ruangans (berdasarkan barang_id dan ruangan_id)
        $barangRuangan = BarangRuangans::where('barang_id', $barangMasuk->id_barang)
                        ->where('ruangan_id', $barangMasuk->ruangan_id)
                        ->first();

        if ($barangRuangan) {
            if ($barangRuangan->stok <= $barangMasuk->jumlah) {
                // Hapus entri jika stok akan jadi 0 atau kurang
                $barangRuangan->delete();
            } else {
                // Kurangi stok
                $barangRuangan->stok -= $barangMasuk->jumlah;
                $barangRuangan->save();
            }
        }

        // Hapus file gambar jika ada
        $barangMasuk->deleteImage();

        // Hapus data barang masuk
        $barangMasuk->delete();

        Alert::success('Success', 'Data berhasil dihapus')->autoClose(1500);
        return redirect()->route('brg-masuk.index');
    }

}