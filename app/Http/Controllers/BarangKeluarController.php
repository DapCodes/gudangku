<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $keyword = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $exportType = $request->input('export');

        $query = BarangKeluars::with('barang')
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('barang', function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%$keyword%")
                    ->orWhere('merek', 'like', "%$keyword%");
                });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_keluar', [$startDate, $endDate]);
            })
            ->when($startDate && !$endDate, function ($query) use ($startDate) {
                $query->whereDate('tanggal_keluar', '>=', $startDate);
            })
            ->when(!$startDate && $endDate, function ($query) use ($endDate) {
                $query->whereDate('tanggal_keluar', '<=', $endDate);
            })
            ->when($user->status_user !== 'admin', function ($query) use ($user) {
                $query->whereHas('barang', function ($q) use ($user) {
                    $q->where('status_barang', $user->status_user);
                });
            });

        $barangKeluar = $query->get();

        // Export jika ada request
        if ($exportType == 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\BarangKeluarExport($barangKeluar),
                'laporan-data-barangkeluar.xlsx'
            );
        }

        if ($exportType == 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.barangKeluar', ['barangKeluar' => $barangKeluar]);
            return $pdf->download('laporan-data-barangkeluar.pdf');
        }

        return view('barangkeluar.index', compact('barangKeluar', 'keyword', 'startDate', 'endDate'));
    }


    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->status_user === 'admin') {
            $barang = Barangs::all(); 
        } else {
            $barang = Barangs::where('status_barang', $user->status_user)->get();
        }

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
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'tanggal_keluar' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'id_barang' => 'required|exists:barangs,id',
        ],
        [
            'jumlah.required' => 'Jumlah barang harus diisi',
            'jumlah.integer' => 'Jumlah barang harus berupa angka',
            'jumlah.min' => 'Jumlah barang minimal 1',
            'tanggal_keluar.required' => 'Tanggal keluar harus diisi',
            'tanggal_keluar.date' => 'Format tanggal tidak valid',
            'keterangan.required' => 'Keterangan harus diisi',
            'keterangan.string' => 'Keterangan harus berupa teks',
            'keterangan.max' => 'Keterangan maksimal 255 karakter',
            'id_barang.required' => 'ID barang harus diisi',
            'id_barang.exists' => 'ID barang tidak ditemukan',
        ]);

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
        $user = Auth::user();
        if ($user->status_user === 'admin') {
            $barangKeluar = BarangKeluars::findOrFail($id);
            $barang = Barangs::all();
        } else {
            $barangKeluar = BarangKeluars::findOrFail($id);
            $barang = Barangs::where('status_barang', $user->status_user)->get();
        }

        return view('barangkeluar.edit', compact('barang', 'barangKeluar'));
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
            'jumlah' => 'required|integer|min:1',
            'tanggal_keluar' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
            'id_barang' => 'required|exists:barangs,id',
        ],
        [
            'jumlah.required' => 'Jumlah barang harus diisi',
            'jumlah.integer' => 'Jumlah barang harus berupa angka',
            'jumlah.min' => 'Jumlah barang minimal 1',
            'tanggal_keluar.required' => 'Tanggal keluar harus diisi',
            'tanggal_keluar.date' => 'Format tanggal tidak valid',
            'keterangan.string' => 'Keterangan harus berupa teks',
            'keterangan.max' => 'Keterangan maksimal 255 karakter',
            'id_barang.required' => 'ID barang harus diisi',
            'id_barang.exists' => 'ID barang tidak ditemukan',
        ]);

        $barangKeluar = BarangKeluars::findOrFail($id);
        $barangLama = Barangs::findOrFail($barangKeluar->id_barang); // Barang lama (sebelum diedit)

        // Kembalikan stok barang lama
        $barangLama->stok += $barangKeluar->jumlah;
        $barangLama->save();

        // Ambil barang baru (yang dipilih pada form)
        $barangBaru = Barangs::findOrFail($request->id_barang);

        // Cek stok cukup atau tidak
        if ($barangBaru->stok < $request->jumlah) {
            Alert::error('Gagal!', 'Stok barang tidak mencukupi untuk dikeluarkan.');
            return redirect()->back();
        }

        // Kurangi stok barang baru
        $barangBaru->stok -= $request->jumlah;
        $barangBaru->save();

        // Update data barang keluar
        $barangKeluar->id_barang = $request->id_barang;
        $barangKeluar->jumlah = $request->jumlah;
        $barangKeluar->tanggal_keluar = $request->tanggal_keluar;
        $barangKeluar->keterangan = $request->keterangan;
        $barangKeluar->save();

        Alert::success('Berhasil!', 'Data berhasil diperbarui.');
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
