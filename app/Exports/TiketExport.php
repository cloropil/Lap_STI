<?php

namespace App\Exports;

use App\Models\InputTiket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles; // Tambahkan ini
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // Tambahkan ini

class TiketExport implements FromCollection, WithHeadings, WithStyles // Tambahkan WithStyles di sini
{
    protected $search;
    protected $tanggal;

    public function __construct($search, $tanggal)
    {
        $this->search = $search;
        $this->tanggal = $tanggal;
    }

    public function collection()
    {
        // ... (kode collection() yang sudah ada)
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
        
        return $query->get()->map(function ($tiket) {
            return [
                'No Tiket' => $tiket->no_tiket,
                'Lokasi' => $tiket->lokasi->lokasi ?? '-',
                'Gangguan' => $tiket->jenis_gangguan ?? '-',
                'SID' => $tiket->lokasi->sid ?? '-',
                'Open' => $tiket->open_tiket_formatted,
                'Link Up' => $tiket->link_up_formatted,
                'Durasi' => $tiket->formatted_durasi,
                'Stopclock' => $tiket->stopclock ?? '-',
                'Status' => $tiket->status_tiket,
                'Jumlah Gambar' => count($tiket->action_images_array),
            ];
        });
    }

    public function headings(): array
    {
        // ... (kode headings() yang sudah ada)
        return [
            'No Tiket',
            'Lokasi',
            'Gangguan',
            'SID',
            'Open',
            'Link Up',
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

        // Memberi style pada baris judul (baris 1)
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // Warna putih
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