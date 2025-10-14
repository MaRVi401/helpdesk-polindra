<?php

namespace App\Exports;

use App\Models\Jurusan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JurusanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $selectedIds;

    public function __construct(array $selectedIds)
    {
        $this->selectedIds = $selectedIds;
    }

    public function collection()
    {
        return Jurusan::withCount('programStudis')->whereIn('id', $this->selectedIds)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Jurusan',
            'Total Program Studi',
            'Tanggal Dibuat',
        ];
    }

    public function map($jurusan): array
    {
        return [
            $jurusan->id,
            $jurusan->nama_jurusan,
            $jurusan->program_studis_count,
            $jurusan->created_at->format('d-m-Y H:i:s'),
        ];
    }
}