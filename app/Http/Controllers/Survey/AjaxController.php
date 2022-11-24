<?php

namespace App\Http\Controllers\Survey;

use Illuminate\Http\Request;
use App\Models\Survey\Survey;
use App\Models\Survey\SurveyRecord;
use App\Models\Concrete\Customer;
use App\Models\Survey\Question;
use App\Models\Survey\Answer;
use App\Models\Survey\SurveyDetail;
use App\Models\Concrete\VolumeTracking;
use Illuminate\Support\Facades\DB;
use App\Models\Survey\Employee;
use DataTables;
class AjaxController extends Controller
{
    public function getAllDataStatistic(Request $request)
    {
        $surveyId = $request->surveyId;
        $startDay = $request->startDay;
        $endDay = $request->endDay;
        if(count($surveyId)>1){
            if($startDay==""&&$endDay==""){
                $surveyId = "";
                $survey = Survey::with('questions','surveyDetails','answers')->get(['id','name']);
                $listSurveyRecords = SurveyRecord::orderBy('created_at')->get(['created_at']);
                $listAnswers = Answer::orderBy('sort')->get();
                $listQuestions = Question::orderBy('sort')->get();
                $listSurveyDetail = SurveyDetail::orderBy('sort')->get();
            }
            else{
                $surveyId = "";
                $survey = Survey::with('questions','surveyDetails','answers')->get(['id','name']);
                $listSurveyRecords = SurveyRecord::whereIn(DB::raw("DATE(created_at)"),array(formatDateSurvey($startDay),formatDateSurvey($endDay)))->orderBy('created_at')->get(['created_at']);
                $listAnswers = Answer::orderBy('sort')->get();
                $listQuestions = Question::orderBy('sort')->get();
                $listSurveyDetail = SurveyDetail::whereIn(DB::raw("DATE(created_at)"),array(formatDateSurvey($startDay),formatDateSurvey($endDay)))->orderBy('sort')->get();
            }
            
        }
        else{
            if($startDay==""&&$endDay==""){
                $survey = Survey::with('questions','surveyDetails','answers')->where('id',$surveyId)->first();
                $listSurveyRecords = SurveyRecord::where('survey_id',$surveyId)->orderBy('created_at')->get(['created_at']);
                $listAnswers = $survey->answers;
                $listQuestions = $survey->questions;
                $listSurveyDetail = $survey->surveyDetails;
            }
            else{
                $survey = Survey::with('questions','surveyDetails','answers')->where('id',$surveyId)->first();
                $listSurveyRecords = SurveyRecord::where('survey_id',$surveyId)->whereIn(DB::raw("DATE(created_at)"),array(formatDateSurvey($startDay),formatDateSurvey($endDay)))->orderBy('created_at')->get(['created_at']);
                $listAnswers = $survey->answers;
                $listQuestions = $survey->questions;
                $listSurveyDetail = SurveyDetail::whereIn(DB::raw("DATE(created_at)"),array(formatDateSurvey($startDay),formatDateSurvey($endDay)))->where('survey_id',$surveyId)->orderBy('sort')->get();
            }
            
        }
        $numberSurveyRecords = $this->getNumberSurveyRecordsBySurveyId($surveyId,$startDay,$endDay);
        $numberCustomers = $this->getNumberCustomersBySurveyId($surveyId,$startDay,$endDay);
        $totalCustomers = $this->getTotalNumberCustomers();
        $numberCustomersThisMonth = $this->getNumberCustomersThisMonth($surveyId,$startDay,$endDay);
        $numberCustomersPreviosMonth = $this->getNumberCustomersPreviosMonth($surveyId);
        $listSurveys = Survey::with('surveyRecords')->orderBy('sort')->get(['id']);
        $listAllSurveyRecords = SurveyRecord::get(['id','survey_id']);
        
        $arr_number = array($numberSurveyRecords,$numberCustomers,$totalCustomers,$numberCustomersThisMonth,$numberCustomersPreviosMonth);
        $responses = array(
           'number' => $arr_number,
           'list_questions' => $listQuestions,
           'list_answers'=>$listAnswers,
           'list_survey_detail'=>$listSurveyDetail,
           'list_surveys' =>$listSurveys,
           'list_all_survey_record' => $listAllSurveyRecords,
           'list_survey_records' => $listSurveyRecords,
        );
        echo json_encode($responses);
    }
    public function getNumberSurveyRecordsBySurveyId($surveyId,$startDay,$endDay)
    {
        if($surveyId==""){
            if($startDay==""&&$endDay==""){
                $numberSurveyRecords = SurveyRecord::count('id');
            }
            else{
                $numberSurveyRecords = SurveyRecord::whereIn(DB::raw("DATE(created_at)"),array(formatDateSurvey($startDay),formatDateSurvey($endDay)))->count('id');
            }
            
        }
        else{
            if($startDay==""&&$endDay==""){
                $numberSurveyRecords = SurveyRecord::where('survey_id',$surveyId)->count('id');
            }
            else{
                $numberSurveyRecords = SurveyRecord::whereIn(DB::raw("DATE(created_at)"),array(formatDateSurvey($startDay),formatDateSurvey($endDay)))->where('survey_id',$surveyId)->whereIn(DB::raw("DATE(created_at)"),array($startDay,$endDay))->count('id');    
            }
            
        }
        
        return $numberSurveyRecords;
    }
    public function getNumberCustomersBySurveyId($surveyId,$startDay,$endDay)
    {
        if($surveyId==""){
            if($startDay==""&&$endDay==""){
                $numberCustomers = SurveyRecord::distinct('customer_id')->count('customer_id');
            }
            else{
                $numberCustomers = SurveyRecord::whereIn(DB::raw("DATE(created_at)"),array(formatDateSurvey($startDay),formatDateSurvey($endDay)))->distinct('customer_id')->count('customer_id');
            }
        }
        else{
            if($startDay==""&&$endDay==""){
                $numberCustomers = SurveyRecord::where('survey_id',$surveyId)->distinct('customer_id')->count('customer_id');
            }
            else{
                $numberCustomers = SurveyRecord::whereIn(DB::raw("DATE(created_at)"),array(formatDateSurvey($startDay),formatDateSurvey($endDay)))->where('survey_id',$surveyId)->distinct('customer_id')->count('customer_id');
            }
            
        }
        
        return $numberCustomers;
    }
    public function getTotalNumberCustomers()
    {
        $totalCustomers = Customer::count();
        return $totalCustomers;
    }
    public function getCustomerByConstructionId(Request $request)
    {
        $constructionId = $request->input('construction_id');
        $arrCustomer = [];
            $customerId = VolumeTracking::where(['construction_id'=>$constructionId])->distinct()->get(['customer_id']);
            for($i = 0 ; $i< count($customerId) ; $i++)
            {
                $customer = Customer::findOrFail($customerId[$i]['customer_id']);
                array_push($arrCustomer,$customer);
            }
        echo json_encode($arrCustomer);
    }
    public function getNumberCustomersThisMonth($surveyId,$startDay,$endDay)
    {
        $date = getdate();
        $month = $date['mon'];
        if($surveyId==""){
            if($startDay==""&&$endDay==""){
                $numberCustomers = SurveyRecord::whereMonth('created_at',$month)->distinct('customer_id')->count('customer_id');
            }
            else{
                $numberCustomers = SurveyRecord::whereMonth('created_at',$month)->whereIn(DB::raw("DATE(created_at)"),array(formatDateSurvey($startDay),formatDateSurvey($endDay)))->distinct('customer_id')->count('customer_id');
            }
        }
        else{
            if($startDay==""&&$endDay==""){
                $numberCustomers = SurveyRecord::whereMonth('created_at',$month)->where('survey_id',$surveyId)->distinct('customer_id')->count('customer_id');
            }
            else{
                $numberCustomers = SurveyRecord::whereMonth('created_at',$month)->whereIn(DB::raw("DATE(created_at)"),array(formatDateSurvey($startDay),formatDateSurvey($endDay)))->where('survey_id',$surveyId)->distinct('customer_id')->count('customer_id');
            }   
            
        }
        
        return $numberCustomers;
    }
    public function getNumberCustomersPreviosMonth()
    {
        $date = getdate();
        $month = $date['mon']-1;
        $numberCustomers = SurveyRecord::whereMonth('created_at',$month)->distinct('customer_id')->count('customer_id');
        return $numberCustomers;
    }
    public function getAllUser(Request $request)
    {
        if($request->ajax()){
            $data = Employee::with('roles','permissions')->orderBy('created_at','desc')->select('id','hovaten','email','phongban')->where('id','!=','257')->get();
            return datatables()->of($data)->addIndexColumn()
                ->addColumn('checkbox',function($employee){
                    $permissionIds = $employee->permissions->pluck('id');
                    $permissionIds = json_encode($permissionIds);
                    $roleIds = $employee->roles->pluck('id');
                    $roleIds = json_encode($roleIds);
                    return view('action.checkbox_user',['id'=>$employee->id,'permission_id'=>$permissionIds,'role_id'=>$roleIds]);
                })->addColumn('role_name',function(Employee $user){
                    if(count($user->getRoleNames())>1){
                        return $user->getRoleNames()[0].','.$user->getRoleNames()[1]; 
                    }
                    return $user->getRoleNames()[0];
                })
                ->addColumn('permission_name',function(Employee $user){
                    $permissionName="";
                    if(count($user->getAllPermissions())>0){
                        $listPermissions = $user->getAllPermissions();
                        $permissionName = "";
                        foreach($listPermissions as $permission){
                            $permissionName .= $permission['name']."<br>";
                        }
                    }
                    return $permissionName;
                })->rawColumns(['checkbox','permission_name'])->make(true);
        }
    }
}
