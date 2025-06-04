<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peralatan extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'nama',
        'keterangan'
    ];
    
    protected $dates = ['deleted_at'];
    
    public function models()
    {
        return $this->hasMany(Modelan::class, 'peralatan_id');
    }
    
    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }
    
    public function senarais()
    {
        return $this->hasMany(Senarai::class);
    }
}
