@extends('layouts.admin')
@section('page-title', 'Data Barang Masuk')

@section('content')
    @include('sweetalert::alert')

    <div class="card">
        <div class="px-3 py-3 d-flex justify-content-between">
            <div>
                <a href="{{ route('brg-masuk.create') }}">
                    <button type="button" class="btn btn-primary">
                        <i class="bx bx-folder-plus" style="position: relative; bottom: 2px;"></i>
                        Tambah Data
                    </button>
                </a>
                <a href="{{ route('brg-masuk.export') }}">
                    <button type="button" class="btn btn-danger">
                        <i class="bx bx-file" style="position: relative; bottom: 2px;"></i>
                        Buat PDF
                    </button>
                </a>
                <a href="{{ route('brg-masuk.export.excel') }}">
                    <button type="button" class="btn btn-success">
                        <i class="bx bx-file" style="position: relative; bottom: 2px;"></i>
                        Buat Excel
                    </button>
                </a>
            </div>
            <div class="d-flex align-items-center border-start ps-3">
                <i class="bx bx-search fs-4 lh-0 me-2"></i>

                <form action="{{ route('brg-masuk.index') }}" method="GET" class="d-flex align-items-center gap-2">
                    <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Cari..."
                        aria-label="Cari..." value="{{ request('search') }}" />

                    <button class="btn btn-primary" type="submit">Cari</button>

                    @if (request()->has('search') && request()->search != '')
                        <a href="{{ route('brg-masuk.index') }}" class="btn btn-secondary">
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
                        <th>Kode Barang Masuk</th>
                        <th>Nama Barang</th>
                        <th>Merek</th>
                        <th>Jumlah</th>
                        <th>Tanggal Masuk</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($barangMasuk as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->barang->nama }}</td>
                            <td>{{ $item->barang->merek }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->translatedFormat('l, d F Y') }}</td>
                            <td>{{ Str::limit($item->keterangan, 20) }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <!-- Tombol Show -->
                                        <a class="dropdown-item"
                                            href="{{ route('brg-masuk.show', $item->id, $item->id_barang) }}">
                                            <i class="bx bx-show me-1"></i> Lihat
                                        </a>

                                        <!-- Tombol Edit -->
                                        <a class="dropdown-item" href="{{ route('brg-masuk.edit', $item->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Ubah
                                        </a>

                                        <!-- Form Delete (Disembunyikan) -->
                                        <form id="form-delete-{{ $item->id }}"
                                            action="{{ route('brg-masuk.destroy', $item->id) }}" method="POST"
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
                            </td>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
