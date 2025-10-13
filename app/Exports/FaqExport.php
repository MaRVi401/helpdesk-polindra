<?php

namespace App\Exports;

use App\Models\Faq;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FaqExport implements FromCollection, WithHeadings, WithMapping
{
    protected $selectedFaqs;

    public function __construct(array $selectedFaqs)
    {
        $this->selectedFaqs = $selectedFaqs;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Faq::with(['user', 'layanan'])->whereIn('id', $this->selectedFaqs)->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Judul',
            'Deskripsi',
            'Layanan',
            'Status',
            'Pembuat',
            'Tanggal Dibuat',
        ];
    }

    /**
     * @param mixed $faq
     * @return array
     */
    public function map($faq): array
    {
        return [
            $faq->id,
            $faq->judul,
            $faq->deskripsi,
            $faq->layanan->nama_layanan ?? 'N/A',
            $faq->status,
            $faq->user->name ?? 'N/A',
            $faq->created_at->format('d-m-Y H:i:s'),
        ];
    }
}