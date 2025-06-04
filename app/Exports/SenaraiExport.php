<?php

namespace App\Exports;

use App\Models\Senarai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SenaraiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function collection()
    {
        if ($this->query) {
            return $this->query->get();
        }
        return Senarai::with(['ppk', 'cawangan', 'peralatan', 'modelan', 'vendor', 'status'])->get();
    }

    public function headings(): array
    {
        return [
            'Tarikh Aduan*',
            'PPK*',
            'Cawangan*',
            'Peralatan*',
            'Aduan*',
            'No. Siri',
            'Modelan',
            'Penyelesaian',
            'Tarikh Hantar Baikpulih',
            'Vendor',
            'Tarikh Selesai Baikpulih',
            'Tarikh Hantar Cawangan',
            'Status',
            'Catatan',
            'Kos'
        ];
    }

    public function map($senarai): array
    {
        return [
            $senarai->tarikh_aduan->format('d/m/Y'),
            $senarai->ppk_name ?? '',  // Uses snapshot data with fallback
            $senarai->cawangan_name ?? '',  // Uses snapshot data with fallback
            $senarai->peralatan_name ?? '',  // Uses snapshot data with fallback
            $senarai->aduan,
            $senarai->no_siri,
            $senarai->modelan_name ?? '',  // Uses snapshot data with fallback
            $senarai->penyelesaian,
            $senarai->tarikh_hantar_baikpulih ? $senarai->tarikh_hantar_baikpulih->format('d/m/Y') : '',
            $senarai->vendor_name ?? '',  // Uses snapshot data with fallback
            $senarai->tarikh_selesai_baikpulih ? $senarai->tarikh_selesai_baikpulih->format('d/m/Y') : '',
            $senarai->tarikh_hantar_cawangan ? $senarai->tarikh_hantar_cawangan->format('d/m/Y') : '',
            $senarai->status ? $senarai->status->nama : '',  // Status unchanged (no snapshot needed)
            $senarai->catatan,
            number_format($senarai->kos, 2)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => 'dd/mm/yyyy', // Tarikh Aduan in dd/mm/yyyy format
            'I' => 'dd/mm/yyyy', // Tarikh Hantar Baikpulih in dd/mm/yyyy format
            'K' => 'dd/mm/yyyy', // Tarikh Selesai Baikpulih in dd/mm/yyyy format
            'L' => 'dd/mm/yyyy', // Tarikh Hantar Cawangan in dd/mm/yyyy format
            'O' => NumberFormat::FORMAT_NUMBER_00, // Kos with 2 decimal places
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Set all date columns to text format to prevent Excel auto-conversion
                $sheet = $event->sheet->getDelegate();
                
                // Get the highest row number
                $highestRow = $sheet->getHighestRow();
                
                // Set date columns as text format to preserve dd/mm/yyyy
                $sheet->getStyle('A2:A' . $highestRow)->getNumberFormat()->setFormatCode('@'); // Text format
                $sheet->getStyle('I2:I' . $highestRow)->getNumberFormat()->setFormatCode('@'); // Text format
                $sheet->getStyle('K2:K' . $highestRow)->getNumberFormat()->setFormatCode('@'); // Text format
                $sheet->getStyle('L2:L' . $highestRow)->getNumberFormat()->setFormatCode('@'); // Text format
                
                // Auto-size columns for better readability
                foreach(range('A','O') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                
                // Set minimum width for date columns
                $sheet->getColumnDimension('A')->setWidth(12); // Tarikh Aduan
                $sheet->getColumnDimension('I')->setWidth(12); // Tarikh Hantar Baikpulih
                $sheet->getColumnDimension('K')->setWidth(12); // Tarikh Selesai Baikpulih
                $sheet->getColumnDimension('L')->setWidth(12); // Tarikh Hantar Cawangan
            },
        ];
    }
}
