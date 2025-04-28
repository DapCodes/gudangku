<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjamans;
use App\Models\Pengembalians;
use App\Models\Barangs;

use Carbon\Carbon;

use App\Exports\PengembalianExport;
use RealRashid\SweetAlert\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

Carbon::setLocale('id');


class PengembalianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function export()
    {
        $pengembalian = Pengembalians::with('barang')->where('status', 'Sudah Dikembalikan')->get();

        $pdf = Pdf::loadView('pdf.pengembalian', ['pengembalian' => $pengembalian]);

        return $pdf->download('laporan-data-pengembalian.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new pengembalianExport, 'laporan-data-peminjaman.xlsx');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->input('search');

        $query = Pengembalians::with('barang')
                    ->where('status', 'Sudah Dikembalikan'); // filter status dulu

        if ($keyword) {
            $query->whereHas('barang', function ($q) use ($keyword) {
                $q->where('nama', 'like', "%$keyword%")
                ->orWhere('merek', 'like', "%$keyword%");
            });
        }

        $pengembalian = $query->get();

        return view('pengembalian.index', compact('pengembalian'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pengembalian = Pengembalians::findOrFail($id);
        $barang = Barangs::findOrFail($pengembalian->id_barang);
        return view('pengembalian.show', compact('pengembalian', 'barang'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
