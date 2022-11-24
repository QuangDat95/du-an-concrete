<?php

namespace App\Models\Survey;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Survey\Answer;
use App\Models\Survey\Survey;
use App\Models\Survey\SurveyDetail;
use App\Traits\TraitUuid;
class Question extends Model
{
    use HasFactory,SoftDeletes;
    use TraitUuid;
    protected $connection = 'mysqlsurvey';
    public $incrementing = false;
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
    public function survey()
    {
        return $this->belongsTo(Survey::class,'survey_id');
    }
    public function surveyDetails()
    {
        return $this->hasMany(SurveyDetail::class);
    }
}
