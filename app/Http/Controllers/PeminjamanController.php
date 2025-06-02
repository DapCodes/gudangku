<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Peminjamans;
use App\Models\Barangs;
use App\Models\BarangRuangans;
use App\Models\Ruangans;
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


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $keyword = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $exportType = $request->input('export');

        $query = Peminjamans::with(['barang', 'ruangan'])
        ->where('status', 'Sedang Dipinjam')
        ->when($keyword, function ($query) use ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('barang', function ($q2) use ($keyword) {
                    $q2->where('nama', 'like', "%$keyword%")
                       ->orWhere('merek', 'like', "%$keyword%");
                })
                ->orWhereHas('ruangan', function ($q2) use ($keyword) {
                    $q2->where('nama_ruangan', 'like', "%$keyword%");
                });
            });
        })
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('tanggal_pinjam', [$startDate, $endDate]);
        })
        ->when($startDate && !$endDate, function ($query) use ($startDate) {
            $query->whereDate('tanggal_pinjam', '>=', $startDate);
        })
        ->when(!$startDate && $endDate, function ($query) use ($endDate) {
            $query->whereDate('tanggal_pinjam', '<=', $endDate);
        })
        ->when($user->status_user !== 'admin', function ($query) use ($user) {
            $query->whereHas('ruangan', function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                $q2->Where('deskripsi', $user->status_user);
                });
            });
        });
    

        // Mengecek jika ada permintaan untuk export
        if ($exportType) {
            $peminjaman = $query->get(); // Mengambil data yang sudah difilter

            // Menambahkan 'tenggat' di sini sebelum export
            $peminjaman->transform(function ($item) {
                $now = Carbon::now(); // Ambil waktu sekarang
                $tanggalKembali = Carbon::parse($item->tanggal_kembali); // Parse tanggal_kembali

                // Cek jika status 'Sedang Dipinjam' dan tanggal sekarang lebih besar dari tanggal_kembali
                if ($item->status === 'Sedang Dipinjam' && $now->gt($tanggalKembali)) {
                    $item->tenggat = 'Terlambat';
                } else {
                    $item->tenggat = 'Dalam Masa Pinjam';
                }

                return $item;
            });

            if ($exportType == 'excel') {
                return Excel::download(new PeminjamanExport($peminjaman), 'laporan-data-peminjaman.xlsx');
            } elseif ($exportType == 'pdf') {
                $pdf = Pdf::loadView('pdf.peminjaman', ['peminjaman' => $peminjaman]);
                return $pdf->download('laporan-data-peminjaman.pdf');
            }
        }

        // Jika tidak export, lakukan pagination dan tambahkan tenggat pada data yang ditampilkan
        $peminjaman = $query->paginate(10)->withQueryString();

        // Menambahkan 'tenggat' di sini sebelum ditampilkan di view
        $peminjaman->getCollection()->transform(function ($item) {
            $now = Carbon::now(); // Ambil waktu sekarang
            $tanggalKembali = Carbon::parse($item->tanggal_kembali); // Parse tanggal_kembali

            // Cek jika status 'Sedang Dipinjam' dan tanggal sekarang lebih besar dari tanggal_kembali
            if ($item->status === 'Sedang Dipinjam' && $now->gt($tanggalKembali)) {
                $item->tenggat = 'Terlambat';
            } else {
                $item->tenggat = 'Dalam Masa Pinjam';
            }

            return $item;
        });

        return view('peminjaman.index', compact('peminjaman', 'keyword', 'startDate', 'endDate'));
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        // Ambil ID ruangan yang memiliki barang
        $ruanganIdsWithBarang = BarangRuangans::distinct()->pluck('ruangan_id');

        // Ambil data barang sesuai status user
        if ($user->status_user === 'admin') {
            $barang = Barangs::all();
        } else {
            $barang = Barangs::whereIn('status_barang', ['Umum', $user->status_user])->get();
        }

        // Ambil ruangan yang memiliki barang dan sesuai status user
        if ($user->status_user === 'admin') {
            $ruangan = Ruangans::whereIn('id', $ruanganIdsWithBarang)->get();
        } else {
            $ruangan = Ruangans::whereIn('id', $ruanganIdsWithBarang)
                        ->where('deskripsi', $user->status_user)
                        ->get();
        }

        return view('peminjaman.create', compact('barang', 'ruangan'));
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
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'nama_peminjam' => 'required|string|max:255',
            'id_barang' => 'required|exists:barangs,id',
        ],
        [
            'jumlah.required' => 'Jumlah tidak boleh kosong',
            'tanggal_pinjam.required' => 'Tanggal pinjam tidak boleh kosong',
            'tanggal_kembali.required' => 'Tanggal kembali tidak boleh kosong',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali harus setelah atau sama dengan tanggal pinjam',
            'nama_peminjam.required' => 'Nama peminjam tidak boleh kosong',
            'id_barang.required' => 'Barang harus dipilih',
            'id_barang.exists' => 'Barang tidak ditemukan',]);

        $peminjaman = new Peminjamans;

        if ($request->deskripsi) {
            $barangRuangan = BarangRuangans::where('barang_id', $request->id_barang)
                ->where('ruangan_id', $request->deskripsi)
                ->first();

            if ($barangRuangan) {
                if ($barangRuangan->stok < $request->jumlah) {
                    return back()->with('error', 'Jumlah melebihi stok tersedia di ruangan.');
                }

                $barangRuangan->stok -= $request->jumlah;
                $barangRuangan->save();
            } else {
                Alert::error('Gagal!', 'Barang tidak tersedia di ruangan ini.');
                return back();
            }
        }

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
        $peminjaman->ruangan_id = $request->deskripsi;

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
        $user = Auth::user();
        $peminjaman = Peminjamans::findOrFail($id);

        // Ambil ID ruangan yang memiliki barang
        $ruanganIdsWithBarang = BarangRuangans::distinct()->pluck('ruangan_id');

        // Ambil data barang
        if ($user->status_user === 'admin') {
            $barang = Barangs::all();
            $ruangan = Ruangans::whereIn('id', $ruanganIdsWithBarang)->get();
        } else {
            $barang = Barangs::whereIn('status_barang', ['Umum', $user->status_user])->get();
            $ruangan = Ruangans::whereIn('id', $ruanganIdsWithBarang)
                        ->where('deskripsi', $user->status_user)
                        ->get();
        }

        return view('peminjaman.edit', compact('peminjaman', 'barang', 'ruangan'));
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
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'nama_peminjam' => 'required|string|max:255',
            'id_barang' => 'required|exists:barangs,id',
            'ruangan_id' => 'required|exists:ruangans,id',
            'status' => 'required|in:Sedang Dipinjam,Sudah Dikembalikan'
        ], [
            'jumlah.required' => 'Jumlah tidak boleh kosong',
            'tanggal_pinjam.required' => 'Tanggal pinjam tidak boleh kosong',
            'tanggal_kembali.required' => 'Tanggal kembali tidak boleh kosong',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali harus setelah atau sama dengan tanggal pinjam',
            'nama_peminjam.required' => 'Nama peminjam tidak boleh kosong',
            'id_barang.required' => 'Barang harus dipilih',
            'id_barang.exists' => 'Barang tidak ditemukan',
            'ruangan_id.required' => 'Ruangan harus dipilih',
            'ruangan_id.exists' => 'Ruangan tidak ditemukan',
            'status.in' => 'Status tidak valid',
        ]);

        $peminjaman = Peminjamans::findOrFail($id);

        $barangLama = Barangs::findOrFail($peminjaman->id_barang);
        $barangBaru = Barangs::findOrFail($request->id_barang);

        $barangRuanganLama = BarangRuangans::where('barang_id', $peminjaman->id_barang)
            ->where('ruangan_id', $peminjaman->ruangan_id)
            ->first();

        $barangRuanganBaru = BarangRuangans::where('barang_id', $request->id_barang)
            ->where('ruangan_id', $request->ruangan_id)
            ->first();

        // === Jika status menjadi "Sudah Dikembalikan" ===
        if ($request->status == "Sudah Dikembalikan") {
            // Kembalikan stok
            $barangLama->stok += $peminjaman->jumlah;
            $barangLama->save();

            if ($barangRuanganLama) {
                $barangRuanganLama->stok += $peminjaman->jumlah;
                $barangRuanganLama->save();
            }

            // Catat ke pengembalian
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
            $pengembalian->ruangan_id = $request->ruangan_id; // Diperbaiki dari 'deskripsi'
            $pengembalian->save();

            $peminjaman->status = 'Sudah Dikembalikan';
            $peminjaman->save();

            Alert::success('Success', 'Data Berhasil Dikembalikan')->autoClose(1500);
            return redirect()->route('peminjaman.index');
        }

        // === Jika status masih "Sedang Dipinjam" ===
        if ($request->status == "Sedang Dipinjam") {
            $jumlahBaru = $request->jumlah;

            // Validasi stok
            if ($barangBaru->stok < $jumlahBaru) {
                Alert::warning('Warning', 'Stok barang utama tidak cukup')->autoClose(1500);
                return redirect()->back();
            }

            if (!$barangRuanganBaru || $barangRuanganBaru->stok < $jumlahBaru) {
                Alert::warning('Warning', 'Stok barang di ruangan tidak cukup')->autoClose(1500);
                return redirect()->back();
            }

            // Barang/ruangan berubah
            if ($peminjaman->id_barang != $request->id_barang || $peminjaman->ruangan_id != $request->ruangan_id) {
                $barangLama->stok += $peminjaman->jumlah;
                $barangLama->save();

                if ($barangRuanganLama) {
                    $barangRuanganLama->stok += $peminjaman->jumlah;
                    $barangRuanganLama->save();
                }

                $barangBaru->stok -= $jumlahBaru;
                $barangBaru->save();

                $barangRuanganBaru->stok -= $jumlahBaru;
                $barangRuanganBaru->save();
            } else {
                // Barang & ruangan sama
                $selisih = $jumlahBaru - $peminjaman->jumlah;

                if ($selisih != 0) {
                    if ($selisih > 0) {
                        if ($barangBaru->stok < $selisih || $barangRuanganBaru->stok < $selisih) {
                            Alert::warning('Warning', 'Stok tambahan tidak mencukupi')->autoClose(1500);
                            return redirect()->back();
                        }

                        $barangBaru->stok -= $selisih;
                        $barangBaru->save();

                        $barangRuanganBaru->stok -= $selisih;
                        $barangRuanganBaru->save();
                    } else {
                        $barangBaru->stok += abs($selisih);
                        $barangBaru->save();

                        $barangRuanganBaru->stok += abs($selisih);
                        $barangRuanganBaru->save();
                    }
                }
            }

            // Simpan perubahan
            $peminjaman->update([
                'id_barang' => $request->id_barang,
                'jumlah' => $request->jumlah,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'status' => $request->status,
                'nama_peminjam' => $request->nama_peminjam,
                'ruangan_id' => $request->ruangan_id, // Diperbaiki dari 'deskripsi'
            ]);

            Alert::success('Success', 'Data Berhasil Diubah')->autoClose(1500);
            return redirect()->route('peminjaman.index');
        }
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

        // Tidak bisa menghapus jika status masih "Sedang Dipinjam"
        if ($peminjaman->status === "Sedang Dipinjam") {
            Alert::warning('Warning', 'Data tidak bisa dihapus karena masih dalam status "Sedang Dipinjam"')->autoClose(5000);
            return redirect()->route('peminjaman.index');
        }

        DB::beginTransaction();

        try {
            $barang = Barangs::findOrFail($peminjaman->id_barang);

            // Kembalikan stok ke gudang utama
            $barang->stok += $peminjaman->jumlah;
            $barang->save();

            // Kembalikan stok ke barang_ruangans
            $barangRuangan = BarangRuangans::firstOrNew([
                'barang_id' => $peminjaman->id_barang,
                'ruangan_id' => $peminjaman->ruangan_id,
            ]);

            $barangRuangan->stok += $peminjaman->jumlah;
            $barangRuangan->save();

            // Hapus data peminjaman
            $peminjaman->delete();

            DB::commit();

            Alert::success('Success', 'Data berhasil dihapus')->autoClose(5000);
            return redirect()->route('pengembalian.index');
        } catch (\Exception $e) {
            DB::rollBack();

            Alert::error('Error', 'Terjadi kesalahan saat menghapus data')->autoClose(5000);
            return redirect()->route('peminjaman.index');
        }
    }


    public function getBarangByRuangan($ruanganId)
    {
        $barang = BarangRuangans::with('barang')
            ->where('ruangan_id', $ruanganId)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->barang->id,
                    'nama' => $item->barang->nama,
                    'merek' => $item->barang->merek,
                    'foto' => asset('image/barang/' . $item->barang->foto),
                    'stok' => $item->stok,
                ];
            });

        return response()->json($barang);
    }

}
