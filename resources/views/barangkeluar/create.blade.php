@extends('layouts.admin')
@section('page-title', 'Data Barang Keluar / Tambah')

@section('content')
    @include('sweetalert::alert')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Tambah data barang</h5>
                <a href="{{ route('brg-masuk.index') }}">
                    <button class="btn btn-outline-secondary">Kembali</button>
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('brg-keluar.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Pilih Ruangan --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">Pilih Ruangan</label>
                        <div class="col-sm-10">
                            <select name="deskripsi" id="ruanganSelect" class="form-control">
                                <option value="">Pilih Ruangan</option>
                                @foreach ($ruangan as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_ruangan }}</option>
                                @endforeach
                            </select>
                            @error('deskripsi')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Pilih Barang --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Pilih Barang</label>
                        <div class="col-sm-10">
                            <div class="dropdown">
                                <button style="text-align: left;" class="w-100 btn btn-outline-secondary dropdown-toggle"
                                    type="button" id="dropdownBarang" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-box" style="margin-right: 5px;"></i> Pilih Barang
                                </button>
                                <ul class="dropdown-menu w-100" id="barangList" aria-labelledby="dropdownBarang">
                                    <li><span class="dropdown-item">Silakan pilih ruangan terlebih dahulu</span></li>
                                </ul>
                            </div>
                            <input type="hidden" name="id_barang" id="id_barang">
                            <div id="barangTerpilih" class="mt-2 text-muted"></div>

                            @error('id_barang')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Jumlah --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Jumlah</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text"><i
                                        class="bx bx-hash"></i></span>
                                <input name="jumlah" type="number" class="form-control" placeholder="0" aria-label="0"
                                    aria-describedby="basic-icon-default-company2" />
                            </div>
                            @error('jumlah')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Tanggal Keluar --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tanggal Keluar</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text"><i
                                        class="bx bx-calendar"></i></span>
                                <input name="tanggal_keluar" type="date" class="form-control"
                                    placeholder="tanggal keluar" />
                            </div>
                            @error('tanggal_keluar')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text"><i
                                        class="bx bx-note"></i></span>
                                <input name="keterangan" type="text" class="form-control"
                                    placeholder="Barang dalam keadaan baik" />
                            </div>
                            @error('keterangan')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Kirim</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT BARANG DINAMIS --}}
    <script>
        document.getElementById('ruanganSelect').addEventListener('change', function() {
            const ruanganId = this.value;
            const barangList = document.getElementById('barangList');
            barangList.innerHTML = '<li><span class="dropdown-item">Memuat data...</span></li>';

            fetch(`{{ url('admin/get-barang-by-ruangan') }}/${ruanganId}`)
                .then(response => response.json())
                .then(data => {
                    barangList.innerHTML = '';

                    if (!data || data.length === 0) {
                        barangList.innerHTML =
                            '<li><span class="dropdown-item">Tidak ada barang di ruangan ini</span></li>';
                        return;
                    }
                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.innerHTML = `
                        <a class="dropdown-item d-flex align-items-start" href="#"
                            onclick="pilihBarang('${item.id}', '${item.nama}', '${item.merek}')">
                            <div>
                                <div><strong>${item.nama}</strong> - ${item.merek}</div>
                                <small class="text-muted">Stok: ${item.stok}</small>
                            </div>
                        </a>
                        <hr class="my-1">
                    `;
                        barangList.appendChild(li);
                    });
                })
                .catch(() => {
                    barangList.innerHTML =
                        '<li><span class="dropdown-item text-danger">Gagal mengambil data barang</span></li>';
                });
        });

        function pilihBarang(id, nama, merek) {
            document.getElementById('id_barang').value = id;
            document.getElementById('barangTerpilih').innerText = `Barang dipilih: ${nama} (${merek})`;
        }
    </script>
@endsection
