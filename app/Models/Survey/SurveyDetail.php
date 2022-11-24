<?php

namespace App\Models\Survey;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Survey\Answer;
use App\Models\Survey\Question;
use App\Models\Survey\SurveyRecord;
use App\Traits\TraitUuid;
class SurveyDetail extends Model
{
    use HasFactory,SoftDeletes;
    use TraitUuid;
    public $incrementing = false;
    protected $connection = 'mysqlsurvey';
    protected $fillable = [
        'survey_record_id',
        'question_id',
        'answer_id',
        'value',
        'survey_id',
        'sort',
        'reason',
    ];
    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    public function surveyRecord()
    {
        return $this->belongsTo(SurveyRecord::class);
    }
}
