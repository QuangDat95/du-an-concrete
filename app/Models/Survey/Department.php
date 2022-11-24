<?php

namespace App\Models\Survey;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Survey\User;
class Department extends Model
{
    use HasFactory,SoftDeletes;
    protected $connection = 'mysqlsurvey';
    public function user()
    {
        return $this->hasMany(User::class);
    }
}
