<?php

namespace App\Models\Concrete;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concrete\PaymentMethod;
use App\Models\Concrete\Customer;
use App\Models\Concrete\Supplier;
use App\Models\Survey\Employee;
use App\Models\Concrete\TransactionType;
use App\Models\Concrete\PaymentItem;
use Carbon\Carbon;

class Debit extends Model
{
    use HasFactory,SoftDeletes;
    protected $connection = 'mysql';
    protected $table = 'payments';
    protected $fillable = [
        'payment_method_id',
        'payment_date',
        'partyable_type',
        'partyable_id',
        'customer_name',
        'amount',
        'description',
        'created_by',
        'transaction_type_id',
        'code',
        'company_id'
    ];
    public function partyable()
    {
        return $this->morphTo();
    }

    public function getPaymentDateAttribute($value)
    {
        if($value != ''){
            return  $this->attributes['payment_date'] = (new Carbon($value))->format('d-m-Y');
        }else{
            return $this->attributes['payment_date'] = '';
        }
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class,'payment_method_id','id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'partyable_id','id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'partyable_id','id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class,'partyable_id','id');
    }

    public function transaction_type()
    {
        return $this->belongsTo(TransactionType::class,'transaction_type_id','id');
    }

    public function payment_item()
    {
        return $this->hasMany(PaymentItem::class,'payment_id');
    }
}
