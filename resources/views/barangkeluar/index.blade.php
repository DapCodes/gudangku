@extends('layouts.admin')
@section('page-title', 'Data Barang Masuk')

@section('content')
    @include('sweetalert::alert')
    <div class="card">
        <div class="px-3 py-3 d-flex justify-content-between">
            <div>
                <form action="{{ route('brg-keluar.index') }}" method="GET" class="d-flex justify-content-between gap-1">
                    <a href="{{ route('brg-keluar.create') }}">
                        <button type="button" class="btn btn-primary">
                            <i class="bx bx-folder-plus" style="position: relative; bottom: 2px;"></i>
                            Tambah
                        </button>
                    </a>

                    <button type="submit" name="export" class="btn btn-danger" value="pdf">
                        <i class="bx bxs-file-pdf" style="position: relative; bottom: 2px;"></i>
                    </button>


                    <button type="submit" name="export" class="btn btn-success" value="excel">
                        <i class="bx bx-spreadsheet" style="position: relative; bottom: 2px;"></i>
                    </button>

            </div>

            <div class="d-flex align-items-center border-start ps-3 gap-1">
                <i class="bx bx-search fs-4 lh-0 me-2"></i>

                <input type="text" name="search" class="form-control border-0 border-bottom shadow-none"
                    placeholder="Cari..." aria-label="Cari..." value="{{ request('search') }}" />

                <!-- Tambahkan filter tanggal -->
                <input type="date" name="start_date" class="form-control border-0 shadow-none"
                    value="{{ request('start_date') }}" />
                <span class="mx-1"> - </span>
                <input type="date" name="end_date" class="form-control border-0 shadow-none"
                    value="{{ request('end_date') }}" />

                <button class="btn btn-primary" type="submit">Cari</button>

                @if ((request()->has('search') && request()->search != '') || request()->has('start_date') || request()->has('end_date'))
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
                        <th>Kode Barang Keluar</th>
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
