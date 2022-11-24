<?php

namespace App\Models\Survey;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CustomersConstruction extends Model
{
    use HasFactory,SoftDeletes;
    protected $connection = 'mysqlsurvey';
    protected $fillable = [
        'customer_id',
        'construction_id',
    ];
}
