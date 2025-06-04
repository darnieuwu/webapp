<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Senarai extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'tarikh_aduan',
        'ppk_id',
        'cawangan_id',
        'peralatan_id',
        'aduan',
        'no_siri',
        'modelan_id',
        'penyelesaian',
        'tarikh_hantar_baikpulih',
        'vendor_id',
        'tarikh_selesai_baikpulih',
        'tarikh_hantar_cawangan',
        'status_id',
        'catatan',
        'kos',
        // Snapshot columns
        'cawangan_nama_snapshot',
        'peralatan_nama_snapshot',
        'modelan_nama_snapshot',
        'vendor_nama_snapshot',
        'ppk_nama_snapshot',
    ];
    
    protected $casts = [
        'tarikh_aduan' => 'date',
        'tarikh_hantar_baikpulih' => 'date',
        'tarikh_selesai_baikpulih' => 'date',
        'tarikh_hantar_cawangan' => 'date',
    ];
    
    public function ppk()
    {
        return $this->belongsTo(Ppk::class);
    }
    
    public function cawangan()
    {
        return $this->belongsTo(Cawangan::class);
    }
    
    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class);
    }

    public function modelan()
    {
        return $this->belongsTo(Modelan::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    
    // Method untuk memproses array aduan dari string
    public function getAduanArrayAttribute()
    {
        return $this->aduan ? explode(';', $this->aduan) : [];
    }
    
    // Method untuk memproses array penyelesaian dari string
    public function getPenyelesaianArrayAttribute()
    {
        return $this->penyelesaian ? explode(';', $this->penyelesaian) : [];
    }
    /**
     * Capture and save snapshot data from related models
     * Call this method whenever creating or updating an Aduan record
     */
    public function captureSnapshots()
    {
        // Capture Cawangan snapshot
        if ($this->cawangan_id && $this->cawangan) {
            $this->cawangan_nama_snapshot = $this->cawangan->nama;
        }
        
        // Capture Peralatan snapshot
        if ($this->peralatan_id && $this->peralatan) {
            $this->peralatan_nama_snapshot = $this->peralatan->nama;
        }
        
        // Capture Model snapshot
        if ($this->modelan_id && $this->modelan) {
            $this->modelan_nama_snapshot = $this->modelan->nama;
        }
        
        // Capture Vendor snapshot
        if ($this->vendor_id && $this->vendor) {
            $this->vendor_nama_snapshot = $this->vendor->nama;
        }
        
        // Capture PPK snapshot
        if ($this->ppk_id && $this->ppk) {
            $this->ppk_nama_snapshot = $this->ppk->nama;
        }
        
        return $this;
    }
    
    /**
     * Get display name for Cawangan (uses snapshot if available, fallback to relationship)
     */
    public function getCawanganNameAttribute()
    {
        return $this->cawangan_nama_snapshot ?? optional($this->cawangan)->nama;
    }
    
    /**
     * Get display name for Peralatan (uses snapshot if available, fallback to relationship)
     */
    public function getPeralatanNameAttribute()
    {
        return $this->peralatan_nama_snapshot ?? optional($this->peralatan)->nama;
    }
    
    /**
     * Get display name for Model (uses snapshot if available, fallback to relationship)
     */
    public function getModelanNameAttribute()
    {
        return $this->modelan_nama_snapshot ?? optional($this->modelan)->nama;
    }
    
    /**
     * Get display name for Vendor (uses snapshot if available, fallback to relationship)
     */
    public function getVendorNameAttribute()
    {
        return $this->vendor_nama_snapshot ?? optional($this->vendor)->nama;
    }
    
    /**
     * Get display name for PPK (uses snapshot if available, fallback to relationship)
     */
    public function getPpkNameAttribute()
    {
        return $this->ppk_nama_snapshot ?? optional($this->ppk)->nama;
    }
    
    /**
     * Boot method to automatically capture snapshots when saving
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($aduan) {
            // Load relationships if they are not already loaded
            if (!$aduan->relationLoaded('cawangan') && $aduan->cawangan_id) {
                $aduan->load('cawangan');
            }
            if (!$aduan->relationLoaded('peralatan') && $aduan->peralatan_id) {
                $aduan->load('peralatan');
            }
            if (!$aduan->relationLoaded('modelan') && $aduan->modelan_id) {
                $aduan->load('modelan');
            }
            if (!$aduan->relationLoaded('vendor') && $aduan->vendor_id) {
                $aduan->load('vendor');
            }
            if (!$aduan->relationLoaded('ppk') && $aduan->ppk_id) {
                $aduan->load('ppk');
            }
            
            // Capture snapshots automatically
            $aduan->captureSnapshots();
        });
    }//
}
