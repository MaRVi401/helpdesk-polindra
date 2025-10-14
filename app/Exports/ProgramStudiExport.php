<?php

namespace App\Exports;

use App\Models\ProgramStudi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProgramStudiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $selectedIds;

    public function __construct(array $selectedIds)
    {
        $this->selectedIds = $selectedIds;
    }

    public function collection()
    {
        return ProgramStudi::withCount('mahasiswa')->whereIn('id', $this->selectedIds)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Program Studi',
            'Jumlah Mahasiswa',
            'Tanggal Dibuat',
        ];
    }

    public function map($prodi): array
    {
        return [
            $prodi->id,
            $prodi->program_studi,
            $prodi->mahasiswa_count,
            $prodi->created_at->format('d-m-Y H:i:s'),
        ];
    }
}