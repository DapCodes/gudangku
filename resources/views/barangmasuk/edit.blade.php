@extends('layouts.admin')
@section('page-title', 'Data Barang Masuk / Ubah')

@section('content')
    @include('sweetalert::alert')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Tambah data barang</h5>
                <a href="{{ route('brg-masuk.index') }}">
                    <button class="btn btn-outline-secondary">
                        Kembali
                    </button>
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('brg-masuk.update', $barangMasuk->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">ID Barang</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                        class="bx bx-collection"></i></span>
                                <select name="id_barang" class="form-control">
                                    @foreach ($barang as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $item->id == $barangMasuk->id_barang ? 'selected' : '' }}>{{ $item->nama }}
                                            - {{ $item->merek }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-company">Jumlah</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text"><i
                                        class="bx bx-hash"></i></span>
                                <input name="jumlah" type="number" class="form-control" placeholder="0"
                                    aria-label="Samsung" aria-describedby="basic-icon-default-company2"
                                    value="{{ $barangMasuk->jumlah }}" />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-company">Tanggal Masuk</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text"><i
                                        class="bx bx-calendar"></i></span>
                                <input name="tanggal_masuk" type="date" class="form-control" placeholder="0"
                                    value="{{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('Y-m-d') }}" />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-company">Keterangan</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text"><i
                                        class="bx bx-note"></i></span>
                                <input name="keterangan" type="text" class="form-control"
                                    placeholder="Barang dalam keadaan baik" value="{{ $barangMasuk->keterangan }}" />
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
