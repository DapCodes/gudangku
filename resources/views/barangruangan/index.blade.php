@extends('layouts.admin')
@section('page-title', 'Data Ruangan')

@section('content')
    @include('sweetalert::alert')
    <div class="card">
        <div class="px-3 py-3 d-flex justify-content-between">
            <div>
                <form action="{{ route('brg-ruangan.index') }}" method="GET" class="d-flex justify-content-between gap-1">

                    <button type="submit" name="export" class="btn btn-danger" value="pdf">
                        <i class="bx bxs-file-pdf" style="position: relative; bottom: 2px;"></i>
                        PDF
                    </button>


                    <button type="submit" name="export" class="btn btn-success" value="excel">
                        <i class="bx bx-spreadsheet" style="position: relative; bottom: 2px;"></i>
                        EXCEL
                    </button>

            </div>

            <select name="byClass" id="byClassSelect" class="form-select w-25 text-center" aria-label="Filter Ruangan">
                <option value="">Semua Ruangan</option>
                @foreach ($ruangan as $item)
                    <option value="{{ $item->id }}" {{ isset($byClass) && $byClass == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_ruangan }}
                    </option>
                @endforeach
            </select>
            <div class="d-flex align-items-center border-start ps-3 gap-1">
                <i class="bx bx-search fs-4 lh-0 me-2"></i>


                <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Cari..."
                    aria-label="Cari..." value="{{ request('search') }}" />

                <button class="btn btn-primary" type="submit">Cari</button>

                @if (
                    (request()->has('search') && request()->search != '') ||
                        request()->has('start_date') ||
                        request()->has('end_date') ||
                        request()->has('byClass'))
                    <a href="{{ route('brg-ruangan.index') }}" class="btn btn-secondary">
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
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($barangRuangan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->ruangan->nama_ruangan }}</td>
                            <td>{{ $item->barang->nama }}</td>
                            <td>{{ $item->stok }}</td>
                            <td style="overflow: visible;">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <!-- Tombol Show -->
                                        <a class="dropdown-item"
                                            href="{{ route('brg-ruangan.show', $item->id, $item->id_barang) }}">
                                            <i class="bx bx-show me-1"></i> Lihat
                                        </a>

                                    </div>
                                </div>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="m-4">
            {{ $barangRuangan->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

@endsection
