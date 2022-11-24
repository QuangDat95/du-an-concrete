<?php

namespace App\Models\Concrete;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concrete\Area;
use App\Models\Concrete\Customer;
use App\Models\Concrete\Construction;
use App\Models\Concrete\ConcreteGrade;
use App\Models\Concrete\Sampleage;
use App\Models\Concrete\Slump;
use App\Models\Concrete\Vehicle;
use App\Models\Concrete\SaleUser;
use App\Models\Concrete\Engineer;
use App\Models\Concrete\User;
use App\Models\Concrete\Organization;
use App\Models\Concrete\Contract;
use App\Models\Survey\Employee;
use App\Models\Concrete\PaymentCondition;

class VolumeTracking extends Model
{
    use HasFactory,SoftDeletes;
    protected $connection = 'mysql';
    protected $fillable = [
        'station_id',
        'user_id',
        'from_date',
        'customer_id',
        'construction_id',
        'payment_condition_id',
        'concrete_grade_id',
        'sampleage_id',
        'slump_id',
        'vehicle_id',
        'contract_id',
        'due_date',
        'sale_user_id',
        'engineer_id',
        'article',
        'actual_weight',
        'payment_volume',
        'concreate_price',
        'additive_price',
        'minus_volume',
        'sending_volume',
        'pumping_time_begin',
        'pumping_time_finish',
        'pump_price',
        'received_date',
        'shipping_surcharge',
        'introduce',
        'tip',
        'sendprice_concreate',
        'sendprice_pump',
        'sendprice_addditive',
        'total_price',
        'remain_price',
        'comment',
        'vat_flag',
        'vat_company',
        'vat_address',
        'tax_number',
        'vat_date',
        'vat_amount',
        'debit_account_1_id',
        'credit_account_1_id',
        'revenue_entry_amount',
        'description_revenue',
        'vat_rate',
        'debit_account_2_id',
        'credit_account_2_id',
        'tax_entry_amount',
        'description_tax',
        'status',
        'company_id'
    ];

    public function getFromDateAttribute($value)
    {
        return $this->attributes['from_date'] = ($value != '') ? (new Carbon($value))->format('d-m-Y') : '';
    }

    public function getReceivedDateAttribute($value)
    {
        return $this->attributes['received_date'] = ($value != '') ? (new Carbon($value))->format('d-m-Y') : '';
    }

    public function getDueDateAttribute($value)
    {
        return $this->attributes['due_date'] = ($value != '') ? (new Carbon($value))->format('d-m-Y') : '';
    }

    public function getVatDateAttribute($value)
    {
        return $this->attributes['vat_date'] = ($value != '') ? (new Carbon($value))->format('d-m-Y') : '';
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class,'station_id','id');
    }

    public function company()
    {
        return $this->belongsTo(Organization::class,'company_id','id');
    }
    
    public function station()
    {
        return $this->belongsTo(Organization::class,'station_id','id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id','id');
    }

    public function construction()
    {
        return $this->belongsTo(Construction::class,'construction_id','id');
    }

    public function concreteGrade()
    {
        return $this->belongsTo(ConcreteGrade::class,'concrete_grade_id','id');
    }

    public function sampleage()
    {
        return $this->belongsTo(Sampleage::class,'sampleage_id','id');
    }

    public function slump()
    {
        return $this->belongsTo(Slump::class,'slump_id','id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class,'vehicle_id','id');
    }

    public function saleUser()
    {
        return $this->belongsTo(SaleUser::class,'sale_user_id','id');
    }

    public function engineer()
    {
        return $this->belongsTo(Engineer::class,'engineer_id','id');
    }

    public function user()
    {
        return $this->belongsTo(Employee::class,'user_id','id');
    }

    public function paymentcondition()
    {
        return $this->belongsTo(PaymentCondition::class,'payment_condition_id','id');
    }

    public function getQS()
    {
        return $this->hasOne(Employee::class,'id','user_id');
    }

    public function getSale()
    {
        return $this->hasOne(Employee::class,'id','sale_user_id');
    }

    public function getEngineer()
    {
        return $this->hasOne(Employee::class,'id','engineer_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class,'contract_id','id');
    }
}