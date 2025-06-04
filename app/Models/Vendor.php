<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'nama',
        'keterangan',
        'peralatan_id',
        'backup_aduan_ids'
    ];
    
    protected $dates = ['deleted_at'];
    
    protected $casts = [
        'backup_aduan_ids' => 'array'
    ];
    
    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class);
    }
    
    public function senarais()
    {
        return $this->hasMany(Senarai::class);
    }
}
