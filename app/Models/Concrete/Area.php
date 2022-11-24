<?php

namespace App\Models\Concrete;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concrete\Station;

class Area extends Model
{
    use HasFactory,SoftDeletes;
    protected $connection = 'mysql';
    protected $fillable = [
        'name'
    ];

    public function station()
    {
        return $this->hasOne(Station::class);
    }

    public function origanzations()
    {
        return $this->hasMany(Organization::class);
    }

    public function volumeTracking()
    {
        return $this->belongsTo(VolumeTracking::class);
    }
}
