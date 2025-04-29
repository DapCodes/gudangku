@extends('layouts.admin')
@section('page-title', 'Data Peminjaman / Ubah Status')

@section('content')
    @include('sweetalert::alert')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Ubah Status Peminjaman</h5>
                <a href="{{ route('peminjaman.index') }}">
                    <button class="btn btn-outline-secondary">Kembali</button>
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('peminjaman.update', $peminjaman->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- ID Barang -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">ID Barang</label>
                        <div class="col-sm-10">
                            <select name="id_barang" class="form-control">
                                @foreach ($barang as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $item->id == $peminjaman->id_barang ? 'selected' : '' }}>
                                        {{ $item->nama }} - {{ $item->merek }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('id_barang')
                            <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                <i class="bx bx-error-circle"></i>
                                <p>{{ $message }}</p>
                            </div>
                        @enderror
                    </div>

                    <!-- Jumlah -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Jumlah</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" name="jumlah" value="{{ old('jumlah', $peminjaman->jumlah) }}">
                            @error('jumlah')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tanggal Pinjam -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tanggal Pinjam</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', $peminjaman->tanggal_pinjam) }}">
                            @error('tanggal_pinjam')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tanggal Kembali -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tanggal Kembali</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" name="tanggal_kembali" value="{{ old('tanggal_kembali', $peminjaman->tanggal_kembali) }}">
                            @error('tanggal_kembali')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Nama Peminjam -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama Peminjam</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="nama_peminjam" value="{{ old('nama_peminjam', $peminjaman->nama_peminjam) }}">
                            @error('nama_peminjam')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status (Bisa diubah) -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <select name="status" class="form-control">
                                <option value="Sedang Dipinjam" {{ $peminjaman->status == 'Sedang Dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                                <option value="Sudah Dikembalikan" {{ $peminjaman->status == 'Sudah Dikembalikan' ? 'selected' : '' }}>Sudah Dikembalikan</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Ubah</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
