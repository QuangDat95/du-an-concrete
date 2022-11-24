<?php

namespace App\Http\Controllers\Survey;

use App\Models\Survey\SurveyRecord;
use Illuminate\Http\Request;
use App\Models\Survey\Survey;
use App\Models\Survey\SurveyDetail;
use Illuminate\Support\Facades\DB;
class SurveyRecordController extends Controller
{
    public function edit($surveyRecordId)
    {
        $surveyRecord = SurveyRecord::with('customer','construction','survey','employee')->findOrFail($surveyRecordId);
        return view('surveys.survey_record.show',['surveyRecord'=>$surveyRecord]);
    }
    public function show($survey)
    {
        $surveys = Survey::orderBy('name')->get(['id','name','sort']);
        $dayCreatedSurveyRecord = SurveyRecord::where(['survey_id'=>$survey])->orderBy('created_at','desc')->get(['created_at']);
        $arrYear = [];
        for($i = 0 ; $i < count($dayCreatedSurveyRecord);$i++){
            $year = date('Y',strtotime($dayCreatedSurveyRecord[$i]['created_at']));
            if(!in_array($year,$arrYear)){
                array_push($arrYear,$year);
            }
        }
        $titleListSurveyRecords = 'Báo cáo & thống kê khảo sát';
        return view('surveys.pages.list_survey_detail',[
            'surveys'=>$surveys,
            'surveyId'=>$survey,
            'arrYear'=>$arrYear,
            'titleListSurveyRecords'=>$titleListSurveyRecords]);
    }

    public function destroy(Request $request){
        $id = $request->id;
        $ids = explode(",",$id);
        foreach($ids as $value){
            SurveyRecord::destroy($value);
            $surveyDetail = SurveyDetail::where('survey_record_id',$value)->get();
            SurveyDetail::destroy($surveyDetail->pluck('id')->toArray());
        }
        $message = 'Xóa phiếu khảo sát thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function getAllSurveyRecords(Request $request)
    {
        if($request->ajax()){
            $surveyId = $request->surveyId;
            $startDay = $request->startDay;
            $endDay = $request->endDay;
            if(count($surveyId)>1){
                if($startDay==""&&$endDay==""){
                    $data = SurveyRecord::with('employee','customer','construction')->latest()->get();
                }
                else{
                    $data = SurveyRecord::with('employee','customer','construction')->whereBetween(DB::raw("DATE(created_at)"),array(formatDateSurvey($startDay),formatDateSurvey($endDay)))->latest()->get();
                }   
            }
            else{
                if($startDay==""&&$endDay=="")
                {
                    $data = SurveyRecord::with('employee','customer','construction')->where(['survey_id'=>$surveyId])->latest()->get();
                }
                else
                {
                    $data = SurveyRecord::with('employee','customer','construction')->where(['survey_id'=>$surveyId])->whereBetween(DB::raw("DATE(created_at)"),array(formatDateSurvey($startDay),formatDateSurvey($endDay)))->latest()->get();
                }
                
            }
            return datatables()->of($data)->addIndexColumn()
            ->editColumn('created_at',function(SurveyRecord $surveyRecord) {
                return date('d/m/Y H:i:s',strtotime($surveyRecord->created_at));
            })->addColumn('checkbox','surveys.action.survey_checkbox_view')
            ->editColumn('status',function(SurveyRecord $surveyRecord){
                    switch($surveyRecord->status){
                        case '0':
                            return view('surveys.action.button_danger_survey_record',['id'=>$surveyRecord->id]);
                            break;
                        case '1':
                            return view('surveys.action.button_success_survey_record');
                            break;
                        case '2':
                            return view('surveys.action.button_light_survey_record');
                            break;
                    }    
                })->rawColumns(['action','status','checkbox'])->make(true);
            }
    }
    public function getSurveyDetail(Request $request)
    {
        if($request->ajax()){
            $surveyId = $request->survey;
            $startDay = $request->startDay;
            $endDay = $request->endDay;
            if(count($surveyId)>1){
                if($startDay==""&&$endDay==""){
                    $data = SurveyDetail::with('surveyRecord','answer')->whereIn('sort',[8,13,14])->where('value','!=','')->orderBy('created_at','desc')->latest()->get();
                }
                else{
                    $data = SurveyDetail::with('surveyRecord','answer')->whereIn(DB::raw("DATE(created_at)"),array(formatDateSurvey($startDay),formatDateSurvey($endDay)))->whereIn('sort',[8,13,14])->where('value','!=','')->orderBy('created_at','desc')->latest()->get();
                }
            }
            else{
                $surveySort = Survey::where(['id'=>$surveyId])->get('sort');
                if($surveySort[0]['sort']==1){
                    if($startDay==""&&$endDay==""){
                        $data = SurveyDetail::with('surveyRecord','answer')->where(['survey_id'=>$surveyId,'sort'=>'8'])->where('value','!=','')->orderBy('created_at','desc')->latest()->get();
                    }
                    else{
                        $data = SurveyDetail::with('surveyRecord','answer')->whereIn(DB::raw("DATE(created_at)"),array(formatDateSurvey($startDay),formatDateSurvey($endDay)))->where(['survey_id'=>$surveyId,'sort'=>'8'])->where('value','!=','')->orderBy('created_at','desc')->latest()->get();
                    }
                }
                else{
                    if($startDay==""&&$endDay==""){
                        $data = SurveyDetail::with('surveyRecord','answer')->where(['survey_id'=>$surveyId])->whereIn('sort',[13,14])->where('value','!=','')->orderBy('created_at','desc')->latest()->get();
                    }
                    else{
                        $data = SurveyDetail::with('surveyRecord','answer')->whereIn(DB::raw("DATE(created_at)"),array(formatDateSurvey($startDay),formatDateSurvey($endDay)))->where(['survey_id'=>$surveyId])->whereIn('sort',[13,14])->where('value','!=','')->orderBy('created_at','desc')->latest()->get();
                    }
                }
            }
            
            return datatables()->of($data)->addIndexColumn()
                ->addColumn('customer_name',function(SurveyDetail $surveyDetail){
                    return $surveyDetail->surveyRecord->customer->name;
                })
                ->addColumn('construction_name',function(SurveyDetail $surveyDetail){
                    return $surveyDetail->surveyRecord->construction->name;
                })
                ->editColumn('created_at',function(SurveyDetail $surveyDetail){
                    return date('d/m/Y H:i:s',strtotime($surveyDetail->created_at));
                })->make(true);
        }
    }
    public function handleSurveyRecord($surveyRecordId)
    {
        $surveyRecord = SurveyRecord::find($surveyRecordId);
        $surveyRecord->status = '1';
        $surveyRecord->save();
        return back();
    }

    public function changelabelsurvey(Request $request)
    {
        $id = $request->id;
        $namesurveys = '';
        if(count($id)>1){
            $namesurveys = 'Phiếu Đánh Giá Khảo Sát Dịch Vụ Tổng Hợp';
            return $namesurveys;
        }else if(count($id) == 1){
            $namesurvey = Survey::select('name')->where('id','=',$id)->get();
            return $namesurvey[0]->name;
        }
    }
}