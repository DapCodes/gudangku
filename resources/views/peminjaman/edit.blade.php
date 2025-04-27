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
                                        {{ $item->id == $peminjaman->id_barang ? 'selected' : '' }}>{{ $item->nama }}
                                        - {{ $item->merek }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Jumlah -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Jumlah</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" value="{{ $peminjaman->jumlah }}">
                        </div>
                    </div>

                    <!-- Tanggal Pinjam -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tanggal Pinjam</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" value="{{ $peminjaman->tanggal_pinjam }}">
                        </div>
                    </div>

                    <!-- Tanggal Kembali -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tanggal Kembali</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" value="{{ $peminjaman->tanggal_kembali }}">
                        </div>
                    </div>

                    <!-- Nama Peminjam -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama Peminjam</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $peminjaman->nama_peminjam }}">
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
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Ubah Status</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
