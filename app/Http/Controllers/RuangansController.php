<?php

namespace App\Http\Controllers;

use App\Models\Ruangans;
use Illuminate\Http\Request;

class RuangansController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ruangan = Ruangans::orderBy('nama_ruangan', 'asc')->get();
        return view('ruangan.index', compact('ruangan'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
        ]);

        // Simpan ke database
        Ruangans::create([
            'nama_ruangan' => $request->nama_ruangan,
            'deskripsi' => $request->deskripsi,
        ]);

        // Redirect atau response sukses
        return redirect()->route('ruangan.index')->with('success', 'Data barang berhasil disimpan.');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ruangans  $ruangans
     * @return \Illuminate\Http\Response
     */
    public function show(Ruangans $ruangans)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ruangans  $ruangans
     * @return \Illuminate\Http\Response
     */
    public function edit(Ruangans $ruangans)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ruangans  $ruangans
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ruangans $ruangans)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ruangans  $ruangans
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ruangans $ruangans)
    {
        //
    }
}
