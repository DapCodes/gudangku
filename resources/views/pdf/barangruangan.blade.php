<!DOCTYPE html>
<html>

<head>
    <title>Laporan Barang Setiap Ruangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 40px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header img {
            width: 120px;
        }

        .header h2 {
            flex-grow: 1;
            text-align: center;
            margin: 0;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('admin/assets/img/icons/brands/gudangku-icon.png') }}" alt="Logo">
        <h2>Laporan Data Barang Setiap Ruangan</h2>
    </div>

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
    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
    </div>
    
</body>

</html>
