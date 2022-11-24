<?php

namespace App\Models\Concrete;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Slump extends Model
{
    use HasFactory,SoftDeletes;
    protected $connection = 'mysql';
    protected $fillable = [
        'name'
    ];
    
    public function volumeTracking()
    {
        return $this->belongsTo(VolumeTracking::class);
    }
}
