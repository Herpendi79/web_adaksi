<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RekapPerProvinsiExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('anggota')
            ->select('provinsi', 'homebase_pt', DB::raw('COUNT(*) as jumlah'))
            ->where('status_anggota', 'aktif')
            ->groupBy('provinsi', 'homebase_pt')
            ->orderBy('provinsi')
            ->orderBy('homebase_pt')
            ->get();
    }

    public function headings(): array
    {
        return ['Provinsi', 'Perguruan Tinggi', 'Jumlah Anggota'];
    }
}
