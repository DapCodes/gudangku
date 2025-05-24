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
                <th>Nama Ruangan</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ruangan as $i => $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_ruangan }}</td>
                    <td>{{ $item->deskripsi }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
