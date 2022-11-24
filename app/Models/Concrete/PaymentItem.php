<?php

namespace App\Models\Concrete;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concrete\Payment;
class PaymentItem extends Model
{
    use HasFactory,SoftDeletes;
    protected $connection = 'mysql';
    protected $fillable = [
        'payment_id',
        'volumn_trackings_id',
        'debit_account_id',
        'credit_account_id',
        'amount',
        'description'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class,'payment_id','id');
    }

    public function volumetracking()
    {
        return $this->belongsTo(VolumeTracking::class,'volumn_trackings_id','id');
    }

    public function debitAccount()
    {
        return $this->belongsTo(GlAccount::class,'debit_account_id','id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(GlAccount::class,'credit_account_id','id');
    }
}