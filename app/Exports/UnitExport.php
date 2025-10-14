<?php

namespace App\Exports;

use App\Models\Unit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UnitExport implements FromCollection, WithHeadings, WithMapping
{
    protected $selectedIds;

    public function __construct(array $selectedIds)
    {
        $this->selectedIds = $selectedIds;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Ambil data unit yang dipilih beserta relasi kepala unit dan user
        return Unit::with('kepalaUnit.user')->whereIn('id', $this->selectedIds)->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama Unit',
            'Kepala Unit',
            'Tanggal Dibuat',
        ];
    }

    /**
     * @param mixed $unit
     * @return array
     */
    public function map($unit): array
    {
        return [
            $unit->id,
            $unit->nama_unit,
            $unit->kepalaUnit->user->name ?? 'Belum Ditentukan',
            $unit->created_at->format('d-m-Y H:i:s'),
        ];
    }
}