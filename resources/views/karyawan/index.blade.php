@extends('layouts.admin')
@section('page-title', 'Data Karyawan')

@section('content')
    @include('sweetalert::alert')
    <div class="card">
        <div class="px-3 py-3 d-flex justify-content-between">
            <div>
                <form action="{{ route('karyawan.index') }}" method="GET" class="d-flex justify-content-between gap-1">
                <a href="{{ route('karyawan.create') }}">
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
                    <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">
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
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($users as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('l, d F Y') }}</td>
                            <td style="overflow: visible;">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <!-- Tombol Edit -->
                                        <a class="dropdown-item" href="{{ route('karyawan.edit', $item->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        <!-- Form Delete (Disembunyikan) -->
                                        <form id="form-delete-{{ $item->id }}"
                                            action="{{ route('karyawan.destroy', $item->id) }}" method="POST"
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
