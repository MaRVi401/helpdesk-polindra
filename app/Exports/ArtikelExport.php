<?php

namespace App\Exports;

use App\Models\Artikel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ArtikelExport implements FromCollection, WithHeadings, WithMapping
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
        return Artikel::with(['user', 'kategori'])->whereIn('id', $this->selectedIds)->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Judul',
            'Kategori',
            'Status',
            'Penulis',
            'Tanggal Dibuat',
        ];
    }

    /**
     * @param mixed $artikel
     * @return array
     */
    public function map($artikel): array
    {
        return [
            $artikel->id,
            $artikel->judul,
            $artikel->kategori->kategori ?? 'N/A',
            $artikel->status,
            $artikel->user->name ?? 'N/A',
            $artikel->created_at->format('d-m-Y H:i:s'),
        ];
    }
}
