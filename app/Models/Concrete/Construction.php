<?php

namespace App\Models\Concrete;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concrete\Customer;
use App\Models\Concrete\Contract;
use App\Models\Concrete\VolumeTracking;

class Construction extends Model
{
    use HasFactory,SoftDeletes;
    protected $connection = 'mysql';
    protected $fillable = [
        'name',
        'address'
    ];
    
    public function customer(){
        return $this->belongsToMany(Customer::class,Contract::class,'construction_id','customer_id');
    }

    public function volumeTracking()
    {
        return $this->hasMany(VolumeTracking::class);
    }
}
