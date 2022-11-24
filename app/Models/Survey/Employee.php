<?php

namespace App\Models\Survey;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    protected $connection = "sqlsrv";
    protected $table = 'employees';
    protected $fillable = [
        'id',
        'manhanvien',
        'hovaten',
        'phongban',
        'sodienthoai',
        'email',
        'tinhtrang',
        'password',
        'verified_at',
        'created_at',
        'update_at',
        'image'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getHovatenAttribute($value)
    {
        return $this->attributes['hovaten'] = trim($value);
    }

    public function payment()
    {
        return $this->morphMany(Payment::class, 'partyable');
    }
}
