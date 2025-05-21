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

class BarangRuangansController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ruangan = Ruangans::all();
        $barangRuangan = BarangRuangans::select('barang_ruangans.*')
        ->join('ruangans', 'barang_ruangans.ruangan_id', '=', 'ruangans.id')
        ->orderBy('ruangans.nama_ruangan', 'asc')
        ->get();

        return view('barangruangan.index', compact('barangRuangan', 'ruangan'));
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BarangRuangans  $barangRuangans
     * @return \Illuminate\Http\Response
     */
    public function destroy(BarangRuangans $barangRuangans)
    {
        //
    }
}
