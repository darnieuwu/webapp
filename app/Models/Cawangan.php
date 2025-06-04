<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cawangan extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'nama',
        'keterangan'
    ];
    
    protected $dates = ['deleted_at'];
    
    public function ppks()
    {
        return $this->hasMany(Ppk::class);
    }
}
