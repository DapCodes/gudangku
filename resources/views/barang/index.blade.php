@extends('layouts.admin')
@section('page-title', 'Data Barang')

@section('content')
    @include('sweetalert::alert')
    <div class="card">
        <div class="px-3 py-3 d-flex justify-content-between">
            <div>
                <form action="{{ route('barang.index') }}" method="GET" class="d-flex justify-content-between gap-1">
                <a href="{{ route('barang.create') }}">
                    <button type="button" class="btn btn-primary">
                        <i class="bx bx-folder-plus" style="position: relative; bottom: 2px;"></i>
                        Tambah Data
                    </button>
                </a>

                <!-- Tombol untuk PDF -->
                <button type="submit" name="export" class="btn btn-danger" value="pdf">
                    <i class="bx bxs-file-pdf" style="position: relative; bottom: 2px;"></i>
                </button>

                <!-- Tombol untuk Excel -->
                <button type="submit" name="export" class="btn btn-success" value="excel">
                    <i class="bx bx-spreadsheet" style="position: relative; bottom: 2px;"></i>
                </button>

            </div>

            <div class="d-flex align-items-center border-start ps-3 gap-1">
                <i class="bx bx-search fs-4 lh-0 me-2"></i>

                <input type="text" name="search" class="form-control border-0 shadow-none"
                    placeholder="Cari..." aria-label="Cari..." value="{{ request('search') }}" />

                <button class="btn btn-primary" type="submit">Cari</button>

                @if ((request()->has('search') && request()->search != '') || request()->has('start_date') || request()->has('end_date'))
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary">
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
                        <th>Merk</th>
                        <th>Foto</th>
                        <th>Stok</th>
                        @php
                            $status = Auth::user()->status_user;
                        @endphp
                        @if ($status == 'admin')
                            <th>Status</th>
                        @endif
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($barang as $barang)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $barang->kode_barang }}</td>
                            <td>{{ $barang->nama }}</td>
                            <td>{{ $barang->merek }}</td>
                            <td>
                                <a href="../image/barang/{{ $barang->foto }}" target="_blank">
                                    <img style="width: 50px; border-radius: 5px; box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.1);
                                    "
                                        src="{{ asset('/image/barang/' . $barang->foto) }}" alt="">
                                </a>
                            </td>
                            <td>{{ $barang->stok }}</td>
                            @if ($status == 'admin')
                            <td>{{ $barang->status_barang }}</td>
                            @endif
                            <td style="overflow: visible;">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <!-- Tombol Show -->
                                        <a class="dropdown-item" href="{{ route('barang.show', $barang->id) }}">
                                            <i class="bx bx-show me-1"></i> Lihat
                                        </a>
                                        <!-- Tombol Edit -->
                                        <a class="dropdown-item" href="{{ route('barang.edit', $barang->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        <!-- Form Delete (Disembunyikan) -->
                                        <form id="form-delete-{{ $barang->id }}"
                                            action="{{ route('barang.destroy', $barang->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <!-- Tombol Hapus (trigger SweetAlert) -->
                                        <a href="#" class="dropdown-item text-danger"
                                            onclick="confirmDelete({{ $barang->id }})">
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
