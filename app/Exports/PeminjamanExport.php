<?php

namespace App\Exports;

use App\Models\Peminjamans;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PeminjamanExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting
{
    public function collection()
    {
        return Peminjamans::with('barang')
            ->where('status', 'Sedang Dipinjam') // Tambah filter status
            ->get()
            ->map(function ($item, $index) {
                return [
                    'No' => $index + 1,
                    'Kode Barang' => $item->kode_barang,
                    'Nama Barang' => $item->barang->nama . ' - ' . $item->barang->merek,
                    'Kode Barang (Barang)' => $item->barang->kode_barang,
                    'Jumlah' => $item->jumlah,
                    'Tanggal Pinjam' => \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d-m-Y'),
                    'Tanggal Kembali' => \Carbon\Carbon::parse($item->tanggal_kembali)->format('d-m-Y'),
                    'Nama Peminjam' => $item->nama_peminjam,
                    'Status' => $item->status,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Barang',
            'Nama Barang',
            'Kode Barang (Barang)',
            'Jumlah',
            'Tanggal Pinjam',
            'Tanggal Kembali',
            'Nama Peminjam',
            'Status',
        ];
    }

    public function styles($sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Header
        $sheet->getStyle('A1:I1')->getFont()->setBold(true)->setSize(12)->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:I1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:I1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4CAF50');

        // Border dan font data
        $sheet->getStyle('A2:I' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A2:I' . $highestRow)->getFont()->setSize(10);
        $sheet->getStyle('A1:I' . $highestRow)->getAlignment()->setWrapText(true);

        // Auto-size kolom
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Tanggal Pinjam
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Tanggal Kembali
        ];
    }
}
