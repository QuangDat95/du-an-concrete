<?php

namespace App\Models\Concrete;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'mysql';
    protected $fillable = ['code','name','address'];

    public function payment()
    {
        return $this->morphMany(Payment::class, 'partyable');
    }
}
