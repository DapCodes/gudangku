@extends('layouts.admin')
@section('page-title', 'Data Barang')

@section('content')
    @include('sweetalert::alert')
    <div class="card mb-5">
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

                <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Cari..."
                    aria-label="Cari..." value="{{ request('search') }}" />

                <button class="btn btn-primary" type="submit">Cari</button>

                @if ((request()->has('search') && request()->search != '') || request()->has('start_date') || request()->has('end_date'))
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary">
                        <i class="bx bx-refresh"></i>
                    </a>
                @endif
                </form>
            </div>
        </div>
        <div class="d-flex align-items-center gap-1 my-2" style="position:relative; left: 17px;">
        @php
            $status = Auth::user()->status_user;
            @endphp
            @if ($status == 'admin')
        @php
                $statusList = ['TBSM', 'RPL', 'TKRO', 'UMUM']; // Sesuaikan dengan status yang ada
                $activeStatus = request()->get('status_barang');
            @endphp
            @foreach ($statusList as $s)
                <a href="{{ route('barang.index', array_merge(request()->query(), ['status_barang' => $s])) }}"
                    class="btn btn-sm {{ $activeStatus == $s ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ strtoupper($s) }}
                </a>
            @endforeach

            {{-- Tombol untuk reset filter status --}}
            @if (request()->has('status_barang'))
                <a href="{{ route('barang.index', array_merge(request()->except('status_barang'))) }}"
                    class="btn btn-sm btn-secondary">Reset</a>
            @endif
        @endif
        </div>

        <div class="table-responsive text-nowrap mb-2">
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
                    @foreach ($barang as $data)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $data->kode_barang }}</td>
                            <td>{{ $data->nama }}</td>
                            <td>{{ $data->merek }}</td>
                            <td>
                                <a href="../image/barang/{{ $data->foto }}" target="_blank">
                                    <img style="width: 50px; border-radius: 5px; box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.1);
                                    "
                                        src="{{ asset('/image/barang/' . $data->foto) }}" alt="">
                                </a>
                            </td>
                            <td>{{ $data->stok }}</td>
                            @if ($status == 'admin')
                                <td>{{ $data->status_barang }}</td>
                            @endif
                            <td style="overflow: visible;">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <!-- Tombol Show -->
                                        <a class="dropdown-item" href="{{ route('barang.show', $data->id) }}">
                                            <i class="bx bx-show me-1"></i> Lihat
                                        </a>
                                        <!-- Tombol Edit -->
                                        <a class="dropdown-item" href="{{ route('barang.edit', $data->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        <!-- Form Delete (Disembunyikan) -->
                                        <form id="form-delete-{{ $data->id }}"
                                            action="{{ route('barang.destroy', $data->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <!-- Tombol Hapus (trigger SweetAlert) -->
                                        <a href="#" class="dropdown-item text-danger"
                                            onclick="confirmDelete({{ $data->id }})">
                                            <i class="bx bx-trash me-1"></i> Hapus
                                        </a>
                                    </div>
                                </div>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="m-4">
            {{ $barang->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>

@endsection
