<?php

namespace App\Exports;

use App\Models\InputTiket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping; // Tambahkan ini
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TiketExport implements FromCollection, WithHeadings, WithStyles, WithMapping
{
    protected $search;
    protected $tanggal;

    public function __construct($search, $tanggal)
    {
        $this->search = $search;
        $this->tanggal = $tanggal;
    }

    public function map($tiket): array
    {
        return [
            $tiket->no_tiket,
            $tiket->lokasi->lokasi ?? '-',
            $tiket->jenis_gangguan ?? '-',
            isset($tiket->lokasi->sid) ? ' ' . $tiket->lokasi->sid : '-', // Tambahkan spasi di depan SID
            $tiket->open_tiket_formatted,
            $tiket->link_up_formatted,
            $tiket->link_upGSM_formatted,
            $tiket->formatted_durasi,
            $tiket->stopclock ?? '-',
            $tiket->status_tiket,
            count($tiket->action_images_array),
        ];
    }

    public function collection()
    {
        $query = InputTiket::with('lokasi');

        if ($this->search) {
            $query->where('no_tiket', 'like', '%' . $this->search . '%')
                  ->orWhereHas('lokasi', function ($q) {
                      $q->where('lokasi', 'like', '%' . $this->search . '%')
                        ->orWhere('sid', 'like', '%' . $this->search . '%');
                  });
        }

        if ($this->tanggal) {
            $query->whereDate('created_at', $this->tanggal);
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No Tiket',
            'Lokasi',
            'Gangguan',
            'SID',
            'Open',
            'Link Up FO',
            'Link Up GSM',
            'Durasi',
            'Stopclock',
            'Status',
            'Jumlah Gambar',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Mengatur lebar kolom secara otomatis agar sesuai dengan isi
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Mengatur font seluruh sheet menjadi Times New Roman
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
            ->applyFromArray([
                'font' => [
                    'name' => 'Times New Roman',
                ],
            ]);

        // Memberi style pada baris judul (baris 1)
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // Warna putih
                'name' => 'Times New Roman', // Pastikan judul juga Times New Roman
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF087E8B'], // Warna latar belakang sesuai sky-700
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFDDDDDD'], // Warna border
                ],
            ],
        ]);

        // Mengatur format kolom SID (kolom D) menjadi text agar tidak berubah jadi notasi ilmiah
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('D2:D' . $highestRow)
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_TEXT);

        // Mengatur alignment text untuk semua data (baris 2 ke bawah)
        $sheet->getStyle('A2:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        
        // Mengatur border untuk semua sel data
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }
}