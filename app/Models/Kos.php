<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kos extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kos';
    
    protected $fillable = [
        'vendor_id',
        'penyelesaian_id',
        'nilai'
    ];
    
    protected $dates = ['deleted_at'];
    
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    
    public function penyelesaian()
    {
        return $this->belongsTo(Penyelesaian::class);
    }
}
