<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Barangs;
use App\Models\Peminjamans;
use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BarangExport;
use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;
Carbon::setLocale('id');



class BarangController extends Controller
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
    public function index(Request $request)
    {
        $user = Auth::user();
        $keyword = $request->input('search');
        $exportType = $request->input('export');
        $statusFilter = $request->input('status_barang');

        $barangQuery = Barangs::query();

        // Filter pencarian
        if ($keyword) {
            $barangQuery->where(function ($query) use ($keyword) {
                $query->where('nama', 'like', "%$keyword%")
                    ->orWhere('merek', 'like', "%$keyword%")
                    ->orWhere('kode_barang', 'like', "%$keyword%")
                    ->orWhere('status_barang', 'like', "%$keyword%");
            })->orWhereHas('user', function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%");
            });
        }

        // Filter berdasarkan status_barang dari dropdown
        if ($statusFilter) {
            $barangQuery->where('status_barang', $statusFilter);
        }

        // Filter tambahan jika user bukan admin
        if ($user->status_user !== 'admin') {
            $barangQuery->where('status_barang', $user->status_user);
        }

        // Ekspor data
        if ($exportType) {
            $barang = $barangQuery->get();

            if ($exportType == 'excel') {
                return Excel::download(new BarangExport($barang), 'laporan-data-barang.xlsx');
            }

            if ($exportType == 'pdf') {
                $pdf = Pdf::loadView('pdf.barang', ['barang' => $barang]);
                return $pdf->download('laporan-data-barang.pdf');
            }
        }

        $barang = $barangQuery->orderBy('nama')->paginate(10);
        $statusOptions = ['TBSM', 'RPL', 'TKRO', 'UMUM']; // Status yang tersedia

        return view('barang.index', compact('barang', 'keyword', 'statusFilter', 'statusOptions'));
    }




    public function create()
    {
        $user = Auth::user();
        if ($user->status_user === 'admin') {
            $barang = Barangs::all(); 
        } else {
            $barang = Barangs::where('status_barang', $user->status_user)->get();
        }

        return view('barang.create', compact('barang'));
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
            'nama' => 'required',
            'merek' => 'required',
            'foto' => 'image|required|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status_barang' => 'required',
        ],
        [
            'nama.required' => 'Nama Barang tidak boleh kosong',
            'merek.required' => 'Merek Barang tidak boleh kosong',
            'foto.required' => 'Gambar tidak boleh kosong.',
            'foto.image' => 'File yang diupload harus berupa gambar',
            'foto.mimes' => 'File yang diupload harus berupa jpeg, png, jpg, gif',
            'foto.max' => 'Ukuran file tidak boleh lebih dari 2MB',
            'status_barang.required' => 'Pilih status barang dengan benar (RPL, TBSM, TKRO, Umum)',
        ]);


        $barang = new Barangs;

        $lastRecord = Barangs::latest('id')->first();   
        $lastId = $lastRecord ? $lastRecord->id : 0;
        $kodeBarang = 'B-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        $barang->kode_barang = $kodeBarang;
        $barang->nama = $request->nama;
        $barang->merek = $request->merek;

        if($request->hasFile('foto')) {
            $img = $request->file('foto');
            $name = rand(1000,9999) . $img->getClientOriginalName();
            $img->move('image/barang', $name);
            $barang->foto = $name;
        }

        $barang->status_barang = $request->status_barang;

        $userId = Auth::user();
        $barang->id_user = $userId->id;

        $barang->save(); 

        Alert::success('Berhasil!', 'Data Berhasil Ditambahkan');
        return redirect()->route('barang.index');

    }

    
    public function show($id)
    {
        $barang = Barangs::findOrFail($id);
        return view('barang.show', compact('barang'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        if ($user->status_user === 'admin') {
            $barang = Barangs::findOrFail($id);
        } else {
            $barang = Barangs::where('status_barang', $user->status_user)
                        ->where('id', $id)
                        ->firstOrFail();
                    }

        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'merek' => 'required',
            'foto' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ],
        [
            'nama.required' => 'Nama Barang tidak boleh kosong',
            'merek.required' => 'Merek Barang tidak boleh kosong',
            'foto.image' => 'File yang diupload harus berupa gambar',
            'foto.mimes' => 'File yang diupload harus berupa jpeg, png, jpg, gif',
            'foto.max' => 'Ukuran file tidak boleh lebih dari 2MB',
        ]);

        $barang = Barangs::findOrFail($id);
        $barang->kode_barang = $request->kode_barang;
        $barang->nama = $request->nama;
        $barang->merek = $request->merek;

        if ($request->hasFile('foto')) {

            if ($barang->foto && file_exists(public_path('image/barang/' . $barang->foto))) {
                unlink(public_path('image/barang/' . $barang->foto));
            }

            $img = $request->file('foto');
            $name = rand(1000,9999) . $img->getClientOriginalName();
            $img->move('image/barang', $name);
            $barang->foto = $name;
        }

        $barang->status_barang = $request->status_barang;

        $barang->id_user = $barang->id_user;        
        
        $barang->save(); 

        Alert::success('Berhasil!', 'Data Berhasil Diubah');
        return redirect()->route('barang.index');
    }
    
    public function destroy($id)
    {
        $barang = Barangs::findOrFail($id);
        $pinjaman = Peminjamans::where('id_barang', $barang->id)->where('status', 'Sedang Dipinjam')->get();

        if (count($pinjaman) > 0) {
            Alert::warning('Gagal!', 'Data tidak dihapus. Karena beberapa stok sedang dipinjam!');
            return redirect()->route('barang.index');
        }
        
        if ($barang->foto && file_exists(public_path('image/barang/' . $barang->foto))) {
            unlink(public_path('image/barang/' . $barang->foto));
        }
    
        $barang->delete();
        Alert::success('Dihapus!', 'Data Berhasil Dihapus');
        return redirect()->route('barang.index');
    
    }
}
