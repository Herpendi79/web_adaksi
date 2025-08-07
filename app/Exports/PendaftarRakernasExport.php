<?php

namespace App\Exports;

use App\Models\PendaftarRakernasModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PendaftarRakernasExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $id_rk;

    public function __construct($id_rk)
    {
        $this->id_rk = $id_rk;
    }

    public function collection()
    {
        return \App\Models\PendaftarRakernasModel::with(['anggota', 'user'])
            ->where('id_rk', $this->id_rk)
            ->where('status', 'valid')
            ->orderBy('created_at', 'asc') // <-- tambahkan ini
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'ID ADAKSI',
            'Nama',
            'NO HP',
            'Baju',
            'Home Base',
            'Provinsi',
            'Perwakilan',
            'Tanggal Daftar',
        ];
    }

    public function map($data): array
    {
        static $row = 0;
        $row++;

        return [
            $row,
            $data->anggota->id_card ?? '-',
            $data->anggota->nama_anggota ?? '-',
            $data->user->no_hp ?? '-',
            $data->ukuran_baju ?? '-',
            $data->anggota->homebase_pt ?? '-',
            $data->anggota->provinsi ?? '-',
            $data->pengurus ?? '-',
            $data->created_at ?? '-',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:H1')->getFont()->setBold(true);
                foreach (range('A', 'H') as $col) {
                    $event->sheet->getDelegate()->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
