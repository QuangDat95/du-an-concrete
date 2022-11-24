<?php

namespace App\Models\Concrete;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concrete\Area;
class Station extends Model
{
    use HasFactory,SoftDeletes;
    protected $connection = 'mysql';
    protected $table = 'organizations';
    protected $fillable = ['name','address','tax_number','email','organization_type_id','area_id','parent_id'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
