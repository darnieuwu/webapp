<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Senarai;
use App\Models\Aduan;
use App\Models\Vendor;
use App\Models\Penyelesaian;
use App\Models\Peralatan;
use App\Models\Kos;
use Illuminate\Support\Facades\DB;

class PopulateKosData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:populate-kos-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate aduans, penyelesaians and kos tables from existing senarais data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to populate Kos data...');

        try {
            DB::beginTransaction();

            // Get all senarais with aduan data
            $senarais = Senarai::whereNotNull('aduan')
                          ->where('aduan', '!=', '')
                          ->with('peralatan')
                          ->get();
            $this->info("Found {$senarais->count()} senarais with aduan data");
            $aduanCount = 0;
            foreach ($senarais as $senarai) {
                // Split multiple aduan items (semicolon separated)
                $aduanItems = array_map('trim', explode(';', $senarai->aduan));
                
                foreach ($aduanItems as $keterangan) {
                    if (empty($keterangan)) continue;
                    
                    // Create aduan if it doesn't exist
                    $aduan = Aduan::firstOrCreate([
                        'peralatan_id' => $senarai->peralatan_id,
                        'keterangan' => $keterangan
                    ]);

                    if ($aduan->wasRecentlyCreated) {
                        $aduanCount++;
                        $this->line("Created aduan: {$keterangan} for {$senarai->peralatan->nama}");
                    }
                }
            }
        

            // Get all senarais with penyelesaian data
            $senarais = Senarai::whereNotNull('penyelesaian')
                          ->where('penyelesaian', '!=', '')
                          ->with('peralatan')
                          ->get();

            $this->info("Found {$senarais->count()} senarais with penyelesaian data");

            $penyelesaianCount = 0;
            $kosCount = 0;

            foreach ($senarais as $senarai) {
                // Split multiple penyelesaian items (semicolon separated)
                $penyelesaianItems = array_map('trim', explode(';', $senarai->penyelesaian));
                
                foreach ($penyelesaianItems as $keterangan) {
                    if (empty($keterangan)) continue;
                    
                    // Create penyelesaian if it doesn't exist
                    $penyelesaian = Penyelesaian::firstOrCreate([
                        'peralatan_id' => $senarai->peralatan_id,
                        'keterangan' => $keterangan
                    ]);

                    if ($penyelesaian->wasRecentlyCreated) {
                        $penyelesaianCount++;
                        $this->line("Created penyelesaian: {$keterangan} for {$senarai->peralatan->nama}");
                    }
                    // Create kos if it doesn't exist
                    if ($senarai->kos > 0) {
                        // Get a random vendor for this record (since aduans might not have vendor mapping)
                        $vendor = $senarai->vendor_id ?
                                 Vendor::find($senarai->vendor_id) :
                                 Vendor::inRandomOrder()->first();
                        
                        if ($vendor) {
                            // Check if kos record already exists
                            $kosExists = Kos::where('vendor_id', $vendor->id)
                                           ->where('penyelesaian_id', $penyelesaian->id)
                                           ->exists();

                            if (!$kosExists) {
                                Kos::create([
                                    'vendor_id' => $vendor->id,
                                    'penyelesaian_id' => $penyelesaian->id,
                                    'nilai' => $senarai->kos
                                ]);
                                $kosCount++;
                                $this->line("Created kos: {$vendor->nama} - {$keterangan} - RM{$senarai->kos}");
                            }
                        }
                    }
                }
            }
            DB::commit();
            $this->info("Successfully populated data!");
            $this->info("Total Aduans created: {$aduanCount}");
            $this->info("Total Penyelesaians created: {$penyelesaianCount}");
            $this->info("Total Kos created: {$kosCount}");
            // Show current counts
            $totalAduans = Aduan::count();
            $totalPenyelesaians = Penyelesaian::count();
            $totalKos = Kos::count();
            $totalVendors = Vendor::count();
            $this->info("Total records in database:");
            $this->info("- Aduans: {$totalAduans}");
            $this->info("- Penyelesaians: {$totalPenyelesaians}");
            $this->info("- Kos: {$totalKos}");
            $this->info("- Vendors: {$totalVendors}");

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Error populating data: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
