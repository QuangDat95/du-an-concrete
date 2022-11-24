<?php

namespace App\Models\Survey;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
use App\Models\SurveyDetail;
use App\Traits\TraitUuid;
class Answer extends Model
{
    use HasFactory,SoftDeletes;
    protected $connection = 'mysqlsurvey';
    use TraitUuid;
    public $incrementing = false;
    public function question()
    {
        return $this->belongsTo(Question::class,'question_id');
    }
    public function surveyDetails()
    {
        return $this->hasMany(SurveyDetail::class);
    }
}
