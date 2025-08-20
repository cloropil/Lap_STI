<?php

namespace App\Exports;

use App\Models\InputTiket;
use App\Models\Lokasi;
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
        // Hitung total durasi stopclock dalam menit
        $totalStopclockMenit = 0;
        if (isset($tiket->stopclocks) && is_iterable($tiket->stopclocks)) {
            $totalStopclockMenit = collect($tiket->stopclocks)->sum('durasi');
        } elseif (is_numeric($tiket->stopclock)) {
            $totalStopclockMenit = $tiket->stopclock;
        }

        // Format status koneksi
        $statusKoneksi = '-';
        if (strtolower($tiket->status_koneksi) === 'up') {
            $statusKoneksi = 'Link Up FO';
        } elseif (strtolower($tiket->status_koneksi) === 'gsm') {
            $statusKoneksi = 'Link Up GSM';
        } elseif (strtolower($tiket->status_koneksi) === 'down') {
            $statusKoneksi = 'Down';
        } else {
            $statusKoneksi = $tiket->status_koneksi ?? '-';
        }

        return [
            $tiket->no_tiket,
            $tiket->lokasi->lokasi ?? '-',
            $tiket->lokasi->product ?? '-',      // Jenis Layanan dari relasi lokasi
            $tiket->jenis_gangguan ?? '-',       // Jenis Gangguan (jika ada di InputTiket)
            isset($tiket->lokasi->sid) ? ' ' . $tiket->lokasi->sid : '-',
            $tiket->open_tiket_formatted,
            $tiket->link_upGSM_formatted,
            $tiket->link_up_formatted,
            $tiket->durasi_GSM,
            $tiket->formatted_durasi,
            $totalStopclockMenit . ' menit',
            $statusKoneksi,
            $tiket->penyebab ?? '-',
            $tiket->action ?? '-',
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
        
        $tikets = $query->get();

        // Tambahkan pemrosesan status_koneksi_formatted
        foreach ($tikets as $tiket) {
            $status = strtolower($tiket->status_koneksi ?? '');
            if ($status === 'Up') {
                $tiket->status_koneksi_formatted = 'Link Up FO';
            } elseif ($status === 'Down') {
                $tiket->status_koneksi_formatted = 'Down';
            } elseif ($status === 'GSM') {
                $tiket->status_koneksi_formatted = 'Link Up GSM';
            } else {
                $tiket->status_koneksi_formatted = $tiket->status_koneksi ?? '-';
            }
        }

        return $tikets;
    }

    public function headings(): array
    {
        return [
            'No Tiket',
            'Layanan',
            'Jenis Layanan',
            'Jenis Gangguan',
            'SID',
            'Open',
            'Link Up GSM',
            'Link Up FO',
            'Durasi GSM',
            'Durasi FO',
            'Stopclock',
            'Status Koneksi',
            'Penyebab', // Tambahkan di sini
            'Action',   // Tambahkan di sini
            'Status Tiket',
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
        $sheet->getStyle('A1:P1')->applyFromArray([
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

        // Mendapatkan jumlah baris data
        $highestRow = $sheet->getHighestRow();

        // Kolom Status Koneksi (misal kolom L, urutan ke-12: A=1, L=12)
        $statusCol = 'L';

        // Loop setiap baris data (mulai dari baris 2)
        for ($row = 2; $row <= $highestRow; $row++) {
            $cell = $statusCol . $row;
            $status = $sheet->getCell($cell)->getValue();

            if ($status === 'Link Up FO') {
                // Hijau
                $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF22C55E');
                $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FF000000');
            } elseif ($status === 'Link Up GSM') {
                // Kuning
                $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFACC15');
                $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FF000000');
            } elseif ($status === 'Down') {
                // Merah
                $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFEF4444');
                $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FFFFFFFF');
            }
        }
    }
}