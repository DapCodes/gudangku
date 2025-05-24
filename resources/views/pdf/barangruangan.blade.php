<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pengembalian</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }
    </style>
</head>

<body>
    <h2>Laporan Data Pengembalian</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Ruangan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($barangRuangan as $i => $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->barang->nama . " - " . $item->barang->merek }}</td>
                    <td>{{ $item->ruangan->nama_ruangan }}</td>
                    <td>{{ $item->stok}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
