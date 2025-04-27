@extends('layouts.admin')
@section('page-title', 'Data Barang / Ubah')

@section('content')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Tambah data barang</h5>
                <a href="{{ route('barang.index') }}">
                    <button class="btn btn-outline-secondary">
                        Kembali
                    </button>
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('barang.update', $barang->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="text" hidden name="kode_barang" value="{{ $barang->kode_barang }}">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">Nama Barang</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                        class="bx bx-collection"></i></span>
                                <input value="{{ $barang->nama }}" name="nama" type="text" class="form-control"
                                    id="basic-icon-default-fullname" placeholder="Handphone" aria-label="Handphone"
                                    aria-describedby="basic-icon-default-fullname2" />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-company">Merek</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text"><i
                                        class="bx bx-buildings"></i></span>
                                <input value="{{ $barang->merek }}" name="merek" type="text"
                                    id="basic-icon-default-company" class="form-control" placeholder="Samsung"
                                    aria-label="Samsung" aria-describedby="basic-icon-default-company2" />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">foto</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-image-add"></i></span>
                                <input value="{{ $barang->foto }}" type="file" name="foto"
                                    id="basic-icon-default-phone" class="form-control phone-mask" placeholder="658 799 8941"
                                    aria-label="658 799 8941" aria-describedby="basic-icon-default-phone2" />
                            </div>
                        </div>
                    </div>
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
