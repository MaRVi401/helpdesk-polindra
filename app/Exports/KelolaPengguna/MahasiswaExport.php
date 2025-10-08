<?php

namespace App\Exports\KelolaPengguna;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MahasiswaExport implements FromCollection, WithHeadings, WithMapping
{
    protected $mahasiswaIds;

    public function __construct(?array $mahasiswaIds = null)
    {
        $this->mahasiswaIds = $mahasiswaIds;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if (empty($this->mahasiswaIds)) {
            return Mahasiswa::with(['user', 'programStudi'])->get();
        }

        return Mahasiswa::with(['user', 'programStudi'])->whereIn('id', $this->mahasiswaIds)->get();
    }

    /**
     * Menentukan judul kolom di file Excel.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama Lengkap',
            'NIM',
            'Email',
            'Program Studi',
            'Tahun Masuk',
            'Akun Dibuat',
        ];
    }

    /**
     * Memetakan data untuk setiap baris di Excel.
     */
    public function map($mahasiswa): array
    {
        return [
            $mahasiswa->id,
            $mahasiswa->user->name,
            $mahasiswa->nim,
            $mahasiswa->user->email,
            $mahasiswa->programStudi->program_studi ?? 'N/A',
            $mahasiswa->tahun_masuk,
            $mahasiswa->created_at->format('d-m-Y H:i:s'),
        ];
    }
}