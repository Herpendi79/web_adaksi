<?php

namespace App\Models;


use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RekapGabunganExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Rekap Per Provinsi' => new RekapPerProvinsiExport(),
            'Data Mentah Anggota' => new AnggotaExport(),
        ];
    }
}
