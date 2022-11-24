<?php

namespace App\Models\Concrete;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concrete\Customer;
use App\Models\Concrete\Construction;
use App\Models\Concrete\PaymentCondition;
use Carbon\Carbon;

class Contract extends Model
{
    use HasFactory,SoftDeletes;
    protected $connection = 'mysql';
    protected $fillable = ['contract_code','customer_id','construction_id','payment_condition_id','contract_date','due_date','sales_user_id'];

    public function getContractDateAttribute($value)
    {
        if($value != ''){
            return  $this->attributes['contract_date'] = (new Carbon($value))->format('d-m-Y');
        }else{
            return $this->attributes['contract_date'] = '';
        }
    }

    public function getDueDateAttribute($value)
    {
        if($value != ''){
            return  $this->attributes['due_date'] = (new Carbon($value))->format('d-m-Y');
        }else{
            return $this->attributes['due_date'] = '';
        }
    }

    public function customer()
    {
        return $this->hasOne(Customer::class,'id','customer_id');
    }

    public function construction()
    {
        return $this->hasOne(Construction::class,'id','construction_id');
    }

    public function paymentCondition()
    {
        return $this->belongsTo(PaymentCondition::class,'payment_condition_id','id');
    }
}
