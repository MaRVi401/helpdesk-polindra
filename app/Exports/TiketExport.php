<?php

namespace App\Exports;

use App\Models\Tiket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TiketExport implements FromCollection, WithHeadings, WithMapping
{
    protected $selectedIds;

    public function __construct(?array $selectedIds = null)
    {
        $this->selectedIds = $selectedIds;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Tiket::with(['pemohon', 'layanan.unit', 'statusTerbaru']);

        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No Tiket',
            'Layanan',
            'Pemohon',
            'Email Pemohon',
            'Unit',
            'Status',
            'Dibuat Pada',
        ];
    }

    /**
     * @param mixed $tiket
     * @return array
     */
    public function map($tiket): array
    {
        return [
            $tiket->no_tiket,
            $tiket->layanan->nama ?? 'N/A',
            $tiket->pemohon->name ?? 'N/A',
            $tiket->pemohon->email ?? 'N/A',
            $tiket->layanan->unit->nama_unit ?? 'N/A',
            $tiket->statusTerbaru->status ?? 'Draft',
            $tiket->created_at->format('d-m-Y H:i'),
        ];
    }
}
