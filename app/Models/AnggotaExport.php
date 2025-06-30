<?php

namespace App\Models;

use App\Models\AnggotaModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AnggotaExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return AnggotaModel::join('users', 'anggota.id_user', '=', 'users.id_user')
            ->where('anggota.status_anggota', 'aktif') // ✅ Filter di sini
            ->select(
                'anggota.id_card',
                'anggota.nama_anggota',
                'users.email',
                'anggota.nip_nipppk',
                'users.no_hp',
                'anggota.status_dosen',
                'anggota.homebase_pt',
                'anggota.provinsi',
                'anggota.created_at'
            )
            ->orderBy('anggota.id_card', 'asc') // 🔁 Urutkan dari kecil ke besar
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Anggota',
            'Nama Anggota',
            'Email',
            'NIP / NIPPPK',
            'No HP',
            'Status Dosen',
            'Nama PT',
            'Provinsi',
            'Daftar'
        ];
    }
}
