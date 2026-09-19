<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateBankSoalExport implements FromArray, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'No',
            'Kategori',
            'Level',
            'Soal',
            'Jawaban A',
            'Jawaban B',
            'Jawaban C',
            'Jawaban D',
            'Jawaban E',
            'Kunci',
            'Poin',
            'Pembahasan'
        ];
    }

    // Handling dummy data
    public function array(): array
    {
        return [
            [
                '1',
                'Mata rantai perumahan',
                '2',
                'Manakah yang paling tepat menggambarkan tujuan utama Penyelenggaraan mata rantai perumahan dan kawasan permukiman?',
                'Mengutamakan kecepatan meskipun bukti dan dokumentasi belum tersedia.',
                'Menilai kondisi secara objektif, menetapkan prioritas berbasis risiko dan dampak, lalu melakukan tindak lanjut yang terukur.',
                'Menyerahkan seluruh proses kepada satu pihak tanpa koordinasi.',
                'Langsung melaksanakan kegiatan tanpa memastikan kebutuhan dan kewenangan pihak terkait.',
                'Memilih tindakan berdasarkan kebiasaan tanpa memeriksa data dan ketentuan.',
                'b',
                '2',
                'pembahasan soal',
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
