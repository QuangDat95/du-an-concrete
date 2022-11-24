<?php

namespace App\Models\Concrete;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concrete\VolumeTracking;
use App\Models\Concrete\TrackingContract;

class Customer extends Model
{
    use HasFactory,SoftDeletes;
    protected $connection = 'mysql';
    protected $fillable = [
        'name',
        'name_other',
        'address',
        'phone_director',
        'phone_accountant',
        'phone_qs',
        'phone_cht',
        'type_id',
        'status_id',
        'accountant_name',
        'address_office'
    ];

    public function construction()
    {
        return $this->belongsToMany(VolumeTracking::class,'customer_id','construction_id');
    }

    public function construction_contract()
    {
        return $this->belongsToMany(TrackingContract::class,'customer_id','construction_id');
    }

    public function payment()
    {
        return $this->morphMany(Payment::class, 'partyable');
    }

    public function volumeTracking()
    {
        return $this->hasMany(volumeTracking::class);
    }
}
