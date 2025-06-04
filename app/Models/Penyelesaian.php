<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penyelesaian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penyelesaians';
    
    protected $fillable = [
        'peralatan_id',
        'keterangan'
    ];
    
    protected $dates = ['deleted_at'];
    
    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class);
    }
}
