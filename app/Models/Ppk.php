<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ppk extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'nama',
        'cawangan_id',
        'keterangan',
        'backup_aduan_ids'
    ];
    
    protected $dates = ['deleted_at'];
    
    protected $casts = [
        'backup_aduan_ids' => 'array'
    ];
    
    public function cawangan()
    {
        return $this->belongsTo(Cawangan::class);
    }
    
    public function senarais()
    {
        return $this->hasMany(Senarai::class);
    }
}
