<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Peminjamans;
use App\Models\Barangs;

use RealRashid\SweetAlert\Facades\Alert;

use Carbon\Carbon;
Carbon::setLocale('id');


class PeminjamanController extends Controller
{
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

        // Ambil barang lama dan barang baru
        $barangLama = Barangs::findOrFail($peminjaman->id_barang);
        $barangBaru = Barangs::findOrFail($request->id_barang);

        // Status pengembalian
        if ($request->status == "Sudah Dikembalikan") {
            // Tambahkan stok barang lama terlebih dahulu
            $barangLama->stok += $peminjaman->jumlah;
            $barangLama->save();
        }

        // Logic perubahan saat update
        $jumlahBaru = $request->jumlah; // Jumlah yang baru dimasukkan

        if ($barangBaru->stok < $jumlahBaru) {
            Alert::warning('Warning', 'Stok Tidak Cukup')->autoClose(1500);
            return redirect()->route('peminjaman.index');
        } else {
            // Jika barang berubah, kurangi stok barang lama dan tambah stok barang baru
            if ($peminjaman->id_barang != $request->id_barang) {
                // Mengembalikan stok barang lama sebelum perubahan
                $barangLama->stok += $peminjaman->jumlah;
                $barangLama->save();

                // Kurangi stok barang baru sesuai jumlah yang baru
                $barangBaru->stok -= $jumlahBaru;
                $barangBaru->save();
            } else {
                // Jika barang tetap sama, cukup perbarui stok barang yang lama
                // Mengembalikan stok barang lama terlebih dahulu
                $barangLama->stok += $peminjaman->jumlah;

                // Mengurangi stok barang lama sesuai jumlah baru
                $barangLama->stok -= $jumlahBaru;
                $barangLama->save();
            }
        }

        // Update data peminjaman
        // Pastikan update hanya dilakukan jika ada perubahan pada request
        $peminjaman->update($request->all());

        // Menampilkan pesan sukses
        Alert::success('Success', 'Data Berhasil Diubah')->autoClose(1500);

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
        //
    }
}
