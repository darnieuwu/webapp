<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SenaraiTemplateController extends Controller
{
public function download()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row - pastikan header ini sesuai dengan yang diharapkan di SenaraiImport
        $headers = [
            'tarikh_aduan',
            'ppk',
            'cawangan',
            'peralatan',
            'aduan',
            'no_siri',
            'modelan',
            'penyelesaian',
            'tarikh_hantar_baikpulih',
            'vendor',
            'tarikh_selesai_baikpulih',
            'tarikh_hantar_cawangan',
            'status',
            'catatan',
            'kos'
        ];
    
        // Set header labels yang lebih user-friendly untuk ditampilkan
        $headerLabels = [
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
        
        // Set header actual dan label yang user-friendly
         $col = 1;
        foreach ($headers as $index => $header) {
            // Simpan header asli sebagai comment untuk referensi
            $cell = $sheet->getCellByColumnAndRow($col, 1);
            $cell->setValue($headerLabels[$index]);
            
        
            $col++;
        }   
        
        // Styling header row
        $headerRange = 'A1:O1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    
        // Set column widths
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create writer
        $writer = new Xlsx($spreadsheet);
        
        // Set headers for download
        $filename = 'template_import_senarai.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}