<?php

namespace App\Models\Survey;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Survey\Question;
use App\Models\Survey\Answer;
use App\Models\Survey\SurveyRecord;
use App\Models\Survey\SurveyDetail;
use App\Traits\TraitUuid;
class Survey extends Model
{
    use HasFactory,SoftDeletes;
    use TraitUuid;
    protected $connection = 'mysqlsurvey';
    public $incrementing = false;
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('sort');
    }
    public function answers()
    {
        return $this->hasManyThrough(Answer::class,Question::class);
    }
    public function surveyRecords()
    {
        return $this->hasMany(SurveyRecord::class);
    }
    public function surveyDetails()
    {
        return $this->hasMany(SurveyDetail::class);
    }
}
