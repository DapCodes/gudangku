@extends('layouts.admin')
@section('page-title', 'Data Pengembalian')

@section('content')
    @include('sweetalert::alert')
    <div class="card">
        <div class="px-3 py-3 d-flex justify-content-between">
            <div>
                <a href="{{ route('pengembalian.create') }}">
                    <button type="button" class="btn btn-primary">
                        <i class="bx bx-folder-plus" style="position: relative; bottom: 2px;"></i>
                        Tambah Data
                    </button>
                </a>
                <a href="{{ route('pengembalian.export') }}">
                    <button type="button" class="btn btn-danger">
                        <i class="bx bx-file" style="position: relative; bottom: 2px;"></i>
                        Buat PDF
                    </button>
                </a>
                <a href="{{ route('pengembalian.export.excel') }}">
                    <button type="button" class="btn btn-success">
                        <i class="bx bx-file" style="position: relative; bottom: 2px;"></i>
                        Buat Excel
                    </button>
                </a>
            </div>
            <div class="d-flex align-items-center border-start ps-3">
                <i class="bx bx-search fs-4 lh-0 me-2"></i>

                <form action="{{ route('pengembalian.index') }}" method="get" class="d-flex align-items-center gap-2">
                    <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Cari..."
                        aria-label="Cari..." value="{{ request('search') }}" />

                    <button class="btn btn-primary" type="submit">Cari</button>

                    @if (request()->has('search') && request()->search != '')
                        <a href="{{ route('pengembalian.index') }}" class="btn btn-secondary">
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
                        <th>Kode pengembalian</th>
                        <th>Nama Barang</th>
                        <th>Kode Barang</th>
                        <th>Jumlah</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Nama Peminjam</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($pengembalian as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->barang->nama . " - " . $item->barang->merek }}</td>
                            <td>{{ $item->barang->kode_barang }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->translatedFormat('l, d F Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali)->translatedFormat('l, d F Y') }}</td>
                            <td>{{ $item->nama_peminjam }}</td>
                            <td class="{{ $item->status == 'Sedang Dipinjam' ? 'text-danger' : ($item->status == 'Sudah Dikembalikan' ? 'text-success' : '') }}">
                                {{ $item->status }}
                            </td>
                            <td style="overflow: visible;">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <!-- Tombol Detail -->
                                        <a class="dropdown-item" href="{{ route('pengembalian.show', $item->id) }}">
                                            <i class="bx bx-show-alt me-1"></i> Detail
                                        </a>        

                                        <!-- Form Delete (Disembunyikan) -->
                                        <form id="form-delete-{{ $item->id }}"
                                            action="{{ route('peminjaman.destroy', $item->id) }}" method="POST"
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
