<?php

namespace App\Models\Concrete;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concrete\GlAccount;
use Kalnoy\Nestedset\NodeTrait;

class TransactionType extends Model
{
    use HasFactory,SoftDeletes;
    protected $connection = 'mysql';
    protected $fillable = ['name','payment_method_id','debit_account_id','credit_account_id','description'];
    public function debitaccount()
    {
        return $this->belongsTo(GlAccount::class,'debit_account_id','id')->withDefault(['account_code' => '']);
    }

    public function creditaccount()
    {
        return $this->belongsTo(GlAccount::class,'credit_account_id','id')->withDefault(['account_code' => '']);
    }
}
