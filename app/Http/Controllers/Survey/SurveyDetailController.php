<?php

namespace App\Http\Controllers\Survey;

use App\Jobs\SendEmail;
use Illuminate\Http\Request;
use App\Models\Survey\Survey;
use App\Models\Survey\SurveyDetail;
use App\Models\Survey\SurveyRecord;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Models\Concrete\Construction;

class SurveyDetailController extends Controller
{
    //trang tạo link
    public function getlink()
    {
        $survey = Survey::orderby('name','asc')->get();
        $sort = SurveyRecord::max('sort') + 1;
        $constructions = Construction::get();
        return view('surveys.pages.form_make_link',compact('survey','constructions'));
    }
//form request link
    public function makelink(Request $request)
    {
        $construction_id = base64_encode($request->construction_id);
        $customer_id = base64_encode($request->customer_id);
        $survey_id = $request->survey_id;
        $check = SurveyRecord::where('customer_id',$request->customer_id)->where('construction_id',$request->construction_id)->where('survey_id',$survey_id)->exists();
        if($check == false){
            return url('/').'/surveys/customer/survey/detail/'.$survey_id.'/'.$customer_id.'/'.$construction_id;
        }else{
            $message = 'Nội dung này đã khảo sát, xin hãy tạo link khác!';
            return response()->json( array('error' => true,'message'=>$message));
        }
    }

    public function addSurvey($survey,Request $request)
    {
        if($request->isMethod('get')){
            return $this->create($survey);
        }
        else{
            return $this->store($request);
        }
    }

    public function createCustomer($survey,$customer,$construction){
        $survey = Survey::findOrFail($survey);
        $sort = SurveyRecord::max('sort')+1;
        $surveyTitle = $survey->name;
        $check = SurveyRecord::where('customer_id',base64_decode($customer))->where('construction_id',base64_decode($construction))->where('survey_id',$survey->id)->exists();
        if($check == false){
            $customer = base64_decode($customer);
            $construction = base64_decode($construction);
            return view('surveys.pages.form_survey_customer',compact('survey','sort','surveyTitle','customer','construction'));
        }else{
            return view('surveys.pages.thank_yous');
        }
    }

    public function storeCustomer(Request $request)
    {   
        $customer = $request->customer_id;
        $survey = $request->survey_id;
        $construction = $request->construction_id;
        $this->storeSurveyRecord($request);
        $survey = Survey::find($request->survey_id);
        $surveyRecord = SurveyRecord::with('customer','construction','surveyDetails')->where('sort',$request->sort)->get(['id','customer_id','construction_id','address']);
        $arrBadAnswer = $this->storeSurveyDetail($request,$surveyRecord[0]['id']);
        if(count($arrBadAnswer)>0){
            $message = [
                'customer_name' => $surveyRecord[0]->customer->name,
                'construction_name' => $surveyRecord[0]->construction->name,
                'construction_address'=>$surveyRecord[0]->address,
                'survey_name' => $survey->name,
                'list_bad_answers' => $arrBadAnswer,
                'name' => 'Khách hàng',
                'email_user'=> 'khachhang@gmail.com',
            ];
            SendEmail::dispatch($message)->delay(now()->addSeconds(90));
        }
        return redirect()->route('surveyDetails.createCustomer',['survey' => $survey->id, 'customer' => base64_encode($customer), 'construction' => base64_encode($construction)]);
    }

    public function store(Request $request)
    {   
        $this->storeSurveyRecord($request);
        $survey = Survey::find($request->survey_id);
        $surveyRecord = SurveyRecord::with('customer','construction','surveyDetails')->where('sort',$request->sort)->get(['id','customer_id','construction_id','address']);
        $arrBadAnswer = $this->storeSurveyDetail($request,$surveyRecord[0]['id']);
        if(count($arrBadAnswer)>0){
            $message = [
                'customer_name' => $surveyRecord[0]->customer->name,
                'construction_name' => $surveyRecord[0]->construction->name,
                'construction_address'=>$surveyRecord[0]->address,
                'survey_name' => $survey->name,
                'list_bad_answers' => $arrBadAnswer,
                'name' => Auth::user()->hovaten,
                'email_user'=> Auth::user()->email,
            ];
            SendEmail::dispatch($message)->delay(now()->addSeconds(90));
        }
        return redirect()->route('surveyDetails.addSurvey',['survey'=>$survey->id]);
    }

    public function create($survey)
    {
        $survey = Survey::findOrFail($survey);
        $sort = SurveyRecord::max('sort')+1;
        $surveyTitle = $survey->name;
        return view('surveys.pages.form_survey',['survey'=>$survey,'sort'=>$sort,'surveyTitle'=>$surveyTitle]);
    }

    public function storeSurveyRecord(Request $request)
    {       
        $surveyRecord = SurveyRecord::create($request->all());
    }

    public function storeSurveyDetail(Request $request,$surveyRecordId)
    {     
        $arrBadAnswer = array();
        $arrAnswer = array();
        $survey = Survey::find($request->survey_id);
        $answers = $survey->answers;
        if(count($answers)==8){
            $i = 1;
        }
        else{
            $i = 9;
        }
        $check = 0;
        foreach($answers as $answer){        
            $nameCheckboxAnswer = 'answer_value_'.$answer->id;
            $nameReasonAnswer = 'answer_reason_'.$answer->id;
            $value = $request[$nameCheckboxAnswer];
            if($value=='value_2'){
                $arrAnswer['name'] = $answer->key;
                $arrAnswer['description'] = $request[$nameReasonAnswer];
                array_push($arrBadAnswer,$arrAnswer);
                $check = 1;
            }
            SurveyDetail::create([
                'survey_record_id' => $surveyRecordId,
                'question_id' => $answer->question_id,
                'answer_id'=> $answer->id,
                'value'=> $value,
                'survey_id' => $request->survey_id,
                'sort' => $i,
                'reason'=>$request[$nameReasonAnswer],
            ]);
            $i++;
        }
        if($check == 0){
            $surveyRecord = SurveyRecord::findOrFail($surveyRecordId);
            $surveyRecord->status = 2;
            $surveyRecord->save();
        }
        return $arrBadAnswer;
    }
}
