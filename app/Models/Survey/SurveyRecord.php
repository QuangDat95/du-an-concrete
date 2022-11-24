<?php

namespace App\Models\Survey;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Survey\SurveyDetail;
use App\Models\Survey\Employee;
use App\Models\Survey\Survey;
use App\Models\Concrete\Customer;
use App\Models\Concrete\Construction;
use App\Traits\TraitUuid;
class SurveyRecord extends Model
{
    use HasFactory,SoftDeletes;
    use TraitUuid;
    protected $connection = 'mysqlsurvey';
    public $incrementing = false;
    protected $fillable = [
        'customer_id',
        'construction_id',
        'address',
        'user_id',
        'survey_id',
        'sort',
        'status'
    ];
    public function surveyDetails()
    {
        return $this->hasMany(SurveyDetail::class)->orderBy('sort');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class,'user_id');
    }
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id','id');
    }
    public function construction()
    {
        return $this->belongsTo(Construction::class,'construction_id','id');
    }
}
