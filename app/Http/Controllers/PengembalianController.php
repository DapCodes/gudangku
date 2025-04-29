<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengembalians;
use App\Models\Barangs;
use App\Models\Peminjamans;

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

    /**
     * Tampilkan data pengembalian, filter, dan export
     */
    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Pengembalians::with('barang')
            ->where('status', 'Sudah Dikembalikan')
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('barang', function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%$keyword%")
                        ->orWhere('merek', 'like', "%$keyword%");
                });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_kembali', [$startDate, $endDate]);
            })
            ->when($startDate && !$endDate, function ($query) use ($startDate) {
                $query->whereDate('tanggal_kembali', '>=', $startDate);
            })
            ->when(!$startDate && $endDate, function ($query) use ($endDate) {
                $query->whereDate('tanggal_kembali', '<=', $endDate);
            });

        // Export jika ada request export
        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return Excel::download(new PengembalianExport($query->get()), 'laporan-data-pengembalian.xlsx');
            } elseif ($request->export == 'pdf') {
                $pdf = Pdf::loadView('pdf.pengembalian', ['pengembalian' => $query->get()]);
                return $pdf->download('laporan-data-pengembalian.pdf');
            }
        }

        // Jika tidak export, tampilkan view biasa dengan paginasi
        $pengembalian = $query->paginate(10)->withQueryString();

        return view('pengembalian.index', compact('pengembalian', 'keyword', 'startDate', 'endDate'));
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $pengembalian = Pengembalians::findOrFail($id);
        $barang = Barangs::findOrFail($pengembalian->id_barang);
        return view('pengembalian.show', compact('pengembalian', 'barang'));
    }
}
