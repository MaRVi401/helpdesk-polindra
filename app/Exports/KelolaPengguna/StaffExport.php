<?php

namespace App\Exports\KelolaPengguna;

use App\Models\Staff;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StaffExport implements FromCollection, WithHeadings, WithMapping
{
    protected $staffIds;

    public function __construct(?array $staffIds = null)
    {
        $this->staffIds = $staffIds;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if (empty($this->staffIds)) {
            return Staff::with(['user', 'unit', 'jabatan'])->get();
        }
        
        return Staff::with(['user', 'unit', 'jabatan'])->whereIn('id', $this->staffIds)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Lengkap',
            'NIK',
            'Email',
            'Role',
            'Unit',
            'Jabatan',
            'Akun Dibuat',
        ];
    }

    public function map($staff): array
    {
        return [
            $staff->id,
            $staff->user->name,
            $staff->nik,
            $staff->user->email,
            $staff->user->role,
            $staff->unit->nama_unit ?? 'N/A',
            $staff->jabatan->nama_jabatan ?? 'N/A',
            $staff->created_at->format('d-m-Y H:i:s'),
        ];
    }
}