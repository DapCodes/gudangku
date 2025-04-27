@extends('layouts.admin')
@section('page-title', 'Data Barang Masuk')

@section('content')
    @include('sweetalert::alert')
    <div class="card">
        <div class="px-3 py-3 d-flex justify-content-between">
            <div>
                <a href="{{ route('brg-keluar.create') }}">
                    <button type="button" class="btn btn-primary">
                        <i class="bx bx-folder-plus" style="position: relative; bottom: 2px;"></i>
                        Tambah Data
                    </button>
                </a>
                <a href="{{ route('brg-keluar.export') }}">
                    <button type="button" class="btn btn-danger">
                        <i class="bx bx-file" style="position: relative; bottom: 2px;"></i>
                        Buat PDF
                    </button>
                </a>
                <a href="{{ route('brg-keluar.export.excel') }}">
                    <button type="button" class="btn btn-success">
                        <i class="bx bx-file" style="position: relative; bottom: 2px;"></i>
                        Buat Excel
                    </button>
                </a>
            </div>
            <div class="d-flex align-items-center border-start ps-3">
                <i class="bx bx-search fs-4 lh-0 me-2"></i>

                <form action="{{ route('brg-keluar.index') }}" method="get" class="d-flex align-items-center gap-2">
                    <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Cari..."
                        aria-label="Cari..." value="{{ request('search') }}" />

                    <button class="btn btn-primary" type="submit">Cari</button>

                    @if (request()->has('search') && request()->search != '')
                        <a href="{{ route('brg-keluar.index') }}" class="btn btn-secondary">
                            <i class="bx bx-refresh"></i>
                        </a>
                    @endif
                </form>
            </div>

        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Merek</th>
                        <th>Jumlah</th>
                        <th>Tanggal Keluar</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($barangKeluar as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->barang->nama }}</td>
                            <td>{{ $item->barang->merek }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_keluar)->translatedFormat('l, d F Y') }}</td>
                            <td>{{ Str::limit($item->keterangan, 20) }}</td>
                            <td style="overflow: visible;">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <!-- Tombol Show -->
                                        <a class="dropdown-item"
                                            href="{{ route('brg-keluar.show', $item->id, $item->id_barang) }}">
                                            <i class="bx bx-show me-1"></i> Lihat
                                        </a>
                                        <!-- Tombol Edit -->
                                        <a class="dropdown-item" href="{{ route('brg-keluar.edit', $item->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        <!-- Form Delete (Disembunyikan) -->
                                        <form id="form-delete-{{ $item->id }}"
                                            action="{{ route('brg-keluar.destroy', $item->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <!-- Tombol Hapus (trigger SweetAlert) -->
                                        <a href="#" class="dropdown-item text-danger"
                                            onclick="confirmDelete({{ $item->id }})">
                                            <i class="bx bx-trash me-1"></i> Hapus
                                        </a>
                                    </div>
                                </div>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
