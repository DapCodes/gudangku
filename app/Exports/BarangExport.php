<?php

namespace App\Exports;

use App\Models\Barangs;
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

class BarangExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting
{
    /**
    * Mendapatkan data dari model Barangs.
    *
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Barangs::select('kode_barang', 'nama', 'merek', 'stok')
            ->get()
            ->map(function($item) {
                // Ubah nilai stok menjadi string jika 0, agar tetap tampil
                $item->stok = (string) $item->stok;
                return $item;
            });
    }

    /**
    * Menambahkan heading (judul kolom) ke file Excel.
    *
    * @return array
    */
    public function headings(): array
    {
        return ['Kode Barang', 'Nama Barang', 'Merek', 'Stok'];
    }

    /**
    * Styling untuk tabel Excel.
    *
    * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
    * @return array
    */
    public function styles($sheet)
    {
        // Styling Header
        $sheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(12)
            ->setColor(new Color('FFFFFF')); // Set font color to white
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:D1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4CAF50'); // Background green color

        // Styling seluruh baris data
        $sheet->getStyle('A2:D' . $sheet->getHighestRow())
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); // Border seluruh tabel

        // Menambahkan padding dan font yang lebih baik
        $sheet->getStyle('A1:D' . $sheet->getHighestRow())->getAlignment()->setWrapText(true); // Membungkus teks dalam sel

        // Menambahkan alignment dan ukuran font pada data
        $sheet->getStyle('A2:D' . $sheet->getHighestRow())
            ->getFont()->setSize(10);
        
        // Penyesuaian ukuran kolom agar pas
        $sheet->getColumnDimension('A')->setAutoSize(true); // Ukuran kolom Kode Barang
        $sheet->getColumnDimension('B')->setAutoSize(true); // Ukuran kolom Nama Barang
        $sheet->getColumnDimension('C')->setAutoSize(true); // Ukuran kolom Merek
        $sheet->getColumnDimension('D')->setAutoSize(true); // Ukuran kolom Stok

        return [
            // Ukuran lebar kolom jika perlu disesuaikan lebih lanjut
            'A' => ['width' => 15],
            'B' => ['width' => 30],
            'C' => ['width' => 20],
            'D' => ['width' => 15],
        ];
    }

    /**
    * Menambahkan format untuk kolom tertentu.
    *
    * @return array
    */
    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT,  // Menjadikan angka dalam kolom stok sebagai teks
        ];
    }
}
