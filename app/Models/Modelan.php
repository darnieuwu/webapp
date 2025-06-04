<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modelan extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'nama',
        'peralatan_id',
        'keterangan',
        'backup_aduan_ids'
    ];
    
    protected $dates = ['deleted_at'];
    
    protected $casts = [
        'backup_aduan_ids' => 'array'
    ];
    
    protected $table = 'modelans';
    
    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class);
    }
    
    public function senarais()
    {
        return $this->hasMany(Senarai::class, 'modelan_id');
    }
}
