<?php

namespace App\Models\Concrete;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleUser extends Model
{
    use HasFactory;

    protected $connection= 'sqlsrv';
    protected $table = 'employees';
}
