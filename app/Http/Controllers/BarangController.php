<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barangs;
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
        $keyword = $request->input('search');
        $exportType = $request->input('export');

        $barangQuery = Barangs::when($keyword, function ($query) use ($keyword) {
            $query->where('nama', 'like', "%$keyword%")
                ->orWhere('merek', 'like', "%$keyword%");
        });

        $barang = $barangQuery->get();

        if ($exportType == 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new BarangExport($barang), 'laporan-data-barang.xlsx');
        }

        if ($exportType == 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.barang', ['barang' => $barang]);
            return $pdf->download('laporan-data-barang.pdf');
        }

        return view('barang.index', compact('barang', 'keyword'));
    }

    


    public function create()
    {
        return view('barang.create');
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
            'foto' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ],
        [
            'nama.required' => 'Nama Barang tidak boleh kosong',
            'merek.required' => 'Merek Barang tidak boleh kosong',
            'foto.image' => 'File yang diupload harus berupa gambar',
            'foto.mimes' => 'File yang diupload harus berupa jpeg, png, jpg, gif',
            'foto.max' => 'Ukuran file tidak boleh lebih dari 2MB',
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

        $barang->save(); 

        Alert::success('Berhasil!', 'Data Berhasil Ditambahkan');
        return redirect()->route('barang.index');

    }

    public function destroy($id)
    {
        $barang = Barangs::findOrFail($id);
        
        if ($barang->foto && file_exists(public_path('image/barang/' . $barang->foto))) {
            unlink(public_path('image/barang/' . $barang->foto));
        }

        $barang->delete();
        Alert::warning('Dihapus!', 'Data Berhasil Dihapus');
        return redirect()->route('barang.index');

    }

    public function show($id)
    {
        $barang = Barangs::findOrFail($id);
        return view('barang.show', compact('barang'));
    }

    public function edit($id)
    {
        $barang = Barangs::findOrFail($id);
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
        
        $barang->save(); 

        Alert::success('Berhasil!', 'Data Berhasil Diubah');
        return redirect()->route('barang.index');
    }
}
