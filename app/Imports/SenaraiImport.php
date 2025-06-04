<?php

namespace App\Imports;

use App\Models\Senarai;
use App\Models\Ppk;
use App\Models\Cawangan;
use App\Models\Peralatan;
use App\Models\Modelan;
use App\Models\Vendor;
use App\Models\Status;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class SenaraiImport implements ToCollection, WithHeadingRow, WithValidation, WithEvents
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        Log::info('Memulakan import data dengan ' . count($rows) . ' baris');
        
        foreach ($rows as $row) {
            // Skip jika baris kosong atau tidak memiliki semua kolom wajib
            if (empty($row['tarikh_aduan']) || empty($row['ppk']) || 
                empty($row['cawangan']) || empty($row['peralatan']) || 
                empty($row['aduan'])) {
                Log::warning('Melewati baris tanpa data wajib', ['row' => $row]);
                continue;
            }
            Log::info('Memproses baris', ['data' => $row]);
            
            try {
                // Cari atau buat Cawangan terlebih dahulu
                $cawangan = Cawangan::firstOrCreate(['nama' => $row['cawangan']]);
                
                // Cari atau buat PPK dengan dependency kepada Cawangan
                $ppk = Ppk::firstOrCreate(
                    ['nama' => $row['ppk']], 
                    ['cawangan_id' => $cawangan->id]
                );
                
                // Update cawangan_id jika PPK sudah ada tapi belum ada cawangan_id
                if (!$ppk->cawangan_id) {
                    $ppk->update(['cawangan_id' => $cawangan->id]);
                }
                // Cari atau buat Peralatan
                $peralatan = Peralatan::firstOrCreate(['nama' => $row['peralatan']]);
                // Cari atau buat Model Peralatan
                $modelan = null;
                if (!empty($row['modelan'])) {
                    $modelan = Modelan::firstOrCreate(
                        ['nama' => $row['modelan']], 
                        ['peralatan_id' => $peralatan->id]
                    );
                    
                    // Update peralatan_id jika model sudah ada tapi belum ada peralatan_id
                    if (!$modelan->peralatan_id) {
                        $modelan->update(['peralatan_id' => $peralatan->id]);
                    }
                } else {
                    // Buat model default jika tidak ada
                    $modelan = Modelan::firstOrCreate(
                        ['nama' => 'Default'], 
                        ['peralatan_id' => $peralatan->id]
                    );
                }
                // Cari atau buat Vendor (jika ada)
                $vendor = null;
                if (!empty($row['vendor'])) {
                    $vendor = Vendor::firstOrCreate(
                        ['nama' => $row['vendor']], 
                        ['peralatan_id' => $peralatan->id]
                    );
                    
                    // Update peralatan_id jika vendor sudah ada tapi belum ada peralatan_id
                    if (!$vendor->peralatan_id) {
                        $vendor->update(['peralatan_id' => $peralatan->id]);
                    }
                }
                
                // Cari atau buat Status
                $status = null;
                if (!empty($row['status'])) {
                    $status = Status::firstOrCreate(['nama' => $row['status']]);
                } else {
                    $status = Status::firstOrCreate(['nama' => 'Baru']);
                }
                
                // Parse dates
                $tarikh_aduan = $this->parseDate($row['tarikh_aduan']);
                $tarikh_hantar_baikpulih = !empty($row['tarikh_hantar_baikpulih']) ? $this->parseDate($row['tarikh_hantar_baikpulih']) : null;
                $tarikh_selesai_baikpulih = !empty($row['tarikh_selesai_baikpulih']) ? $this->parseDate($row['tarikh_selesai_baikpulih']) : null;
                $tarikh_hantar_cawangan = !empty($row['tarikh_hantar_cawangan']) ? $this->parseDate($row['tarikh_hantar_cawangan']) : null;

                // Buat rekod Aduan
                $senarai = new Senarai([
                    'tarikh_aduan' => $tarikh_aduan,
                    'ppk_id' => $ppk->id,
                    'cawangan_id' => $cawangan->id,
                    'peralatan_id' => $peralatan->id,
                    'aduan' => $row['aduan'],
                    'no_siri' => $row['no_siri'] ?? null,
                    'modelan_id' => $modelan ? $modelan->id : null,
                    'penyelesaian' => $row['penyelesaian'] ?? null,
                    'tarikh_hantar_baikpulih' => $tarikh_hantar_baikpulih,
                    'vendor_id' => $vendor ? $vendor->id : null,
                    'tarikh_selesai_baikpulih' => $tarikh_selesai_baikpulih,
                    'tarikh_hantar_cawangan' => $tarikh_hantar_cawangan,
                    'status_id' => $status->id,
                    'catatan' => $row['catatan'] ?? null,
                    'kos' => !empty($row['kos']) ? (float)$row['kos'] : null,
                ]);
                
                // Simpan record - snapshot akan ditangkap otomatis via Model boot() method
                // Ini akan memanggil captureSnapshots() secara otomatis melalui Eloquent saving event
                $senarai->save();
                Log::info('Berjaya menyimpan aduan dengan snapshot data', [
                    'id' => $senarai->id,
                    'cawangan_snapshot' => $senarai->cawangan_nama_snapshot,
                    'peralatan_snapshot' => $senarai->peralatan_nama_snapshot,
                    'modelan_snapshot' => $senarai->modelan_nama_snapshot,
                    'vendor_snapshot' => $senarai->vendor_nama_snapshot,
                    'ppk_snapshot' => $senarai->ppk_nama_snapshot
                ]);
                
            } catch (\Exception $e) {
                Log::error('Gagal menyimpan senarai aduan: ' . $e->getMessage(), [
                    'row' => $row,
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        }
    }
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }
        
        // Jika dateString adalah angka (Excel date serial number)
        if (is_numeric($dateString)) {
            try {
                // Format Excel date yang dimulai dari 1 Januari 1900
                // Tambahkan angka hari ke tanggal 1899-12-30 
                // (Excel menganggap 1900 sebagai tahun kabisat, jadi perlu dikurangi 2 hari)
                return Carbon::createFromDate(1899, 12, 30)->addDays((int)$dateString);
            } catch (\Exception $e) {
                Log::warning('Gagal mengurai Excel date: ' . $dateString . ', error: ' . $e->getMessage());
                return null;
            }
        }
        
        try {
            // Coba parse dengan Carbon secara langsung
            return Carbon::parse($dateString);
        } catch (\Exception $e) {
            try {
                // Coba format DD/MM/YYYY
                return Carbon::createFromFormat('d/m/Y', $dateString);
            } catch (\Exception $e2) {
                try {
                    // Coba format YYYY-MM-DD
                    return Carbon::createFromFormat('Y-m-d', $dateString);
                } catch (\Exception $e3) {
                    // Jika gagal semua, log dan kembalikan null
                    Log::warning('Gagal mengurai tarikh: ' . $dateString);
                    return null;
                }
            }
        }
    }

    public function rules(): array
    {
        return [
            'tarikh_aduan' => 'required',
            'ppk' => 'required',
            'cawangan' => 'required',
            'peralatan' => 'required',
            'aduan' => 'required',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterImport::class => function(AfterImport $event) {
                Log::info('Import selesai. Memulakan proses auto-populate kos data...');
                
                try {
                    // Jalankan command populate kos data secara automatik
                    $exitCode = Artisan::call('app:populate-kos-data');
                    
                    if ($exitCode === 0) {
                        Log::info('Auto-populate kos data berjaya selesai');
                    } else {
                        Log::warning('Auto-populate kos data selesai dengan warning/error');
                    }
                    
                    // Log output dari command
                    $output = Artisan::output();
                    Log::info('PopulateKosData command output: ' . $output);
                    
                } catch (\Exception $e) {
                    Log::error('Gagal menjalankan auto-populate kos data: ' . $e->getMessage());
                }
            },
        ];
    }
    
}
