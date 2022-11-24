<?php

namespace App\Models\Concrete;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TransactionEntry extends Model
{
    use HasFactory,SoftDeletes;
    protected $connection = 'mysql';
    protected $primaryKey  = 'station_id';
    public $incrementing = false;
    protected $fillable = [
        'station_id',
        'company_id',
        'revenue_debit_account_id',
        'revenue_credit_account_id',
        'vat_rate',
        'tax_debit_account_id',
        'tax_credit_account_id'
    ];
    
    public function revenuedebitaccount()
    {
        return $this->belongsTo(GlAccount::class,'revenue_debit_account_id','id')->withDefault(['account_code' => '']);
    }

    public function revenuecreditaccount()
    {
        return $this->belongsTo(GlAccount::class,'revenue_credit_account_id','id')->withDefault(['account_code' => '']);
    }

    public function taxdebitaccount()
    {
        return $this->belongsTo(GlAccount::class,'tax_debit_account_id','id')->withDefault(['account_code' => '']);
    }
    
    public function taxcreditaccount()
    {
        return $this->belongsTo(GlAccount::class,'tax_credit_account_id','id')->withDefault(['account_code' => '']);
    }

    public function station()
    {
        return $this->belongsTo(Organization::class,'station_id');
    }
}
