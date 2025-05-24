@extends('layouts.admin')
@section('page-title', 'Data Ruangan')

@section('content')
    @include('sweetalert::alert')
    <div class="card">
        <div class="px-3 py-3 d-flex justify-content-between">
            <div>
                <form action="{{ route('ruangan.index') }}" method="GET" class="d-flex justify-content-between gap-1">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahRuangan">
                        <i class="bx bx-folder-plus" style="position: relative; bottom: 2px;"></i>
                        Tambah
                    </button>

                    <button type="submit" name="export" class="btn btn-danger" value="pdf">
                        <i class="bx bxs-file-pdf" style="position: relative; bottom: 2px;"></i>
                    </button>


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
                    <a href="{{ route('ruangan.index') }}" class="btn btn-secondary">
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
                        <th>Nama Ruangan</th>
                        <th>deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($ruangan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_ruangan }}</td>
                            <td>{{ $item->deskripsi}}</td>
                            <td style="overflow: visible;">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">

                                        <!-- Tombol Edit -->
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalEditRuangan-{{ $item->id }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
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
            <div class="m-4">
                {{ $ruangan->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>




    <!-- Modal Tambah Ruangan -->
<div class="modal fade" id="modalTambahRuangan" tabindex="-1" aria-labelledby="modalTambahRuanganLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('ruangan.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahRuanganLabel">Tambah Ruangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_ruangan" class="form-label">Nama Ruangan</label>
                        <input type="text" class="form-control" id="nama_ruangan" name="nama_ruangan" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Jurusan</label>
                        <select name="deskripsi" id="" class="form-control">
                            <option value="Umum">Umum</option>
                            <option value="TBSM">TBSM</option>
                            <option value="RPL">RPL</option>
                            <option value="TKRO">TKRO</option>

                        </select>
                    </div>
                </div> 
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>


@foreach ($ruangan as $item)
    <!-- Modal Edit Ruangan -->
    <div class="modal fade" id="modalEditRuangan-{{ $item->id }}" tabindex="-1" aria-labelledby="modalEditRuanganLabel-{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('ruangan.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditRuanganLabel-{{ $item->id }}">Edit Ruangan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_ruangan" class="form-label">Nama Ruangan</label>
                            <input type="text" class="form-control" name="nama_ruangan" value="{{ $item->nama_ruangan }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Jurusan</label>
                            <select name="deskripsi" class="form-control">
                                <option value="Umum" {{ $item->deskripsi == 'Umum' ? 'selected' : '' }}>Umum</option>
                                <option value="TBSM" {{ $item->deskripsi == 'TBSM' ? 'selected' : '' }}>TBSM</option>
                                <option value="RPL" {{ $item->deskripsi == 'RPL' ? 'selected' : '' }}>RPL</option>
                                <option value="TKRO" {{ $item->deskripsi == 'TKRO' ? 'selected' : '' }}>TKRO</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach


@endsection
