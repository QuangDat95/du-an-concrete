<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Survey\UserController;
use App\Http\Controllers\Survey\AdminController;
use App\Http\Controllers\Survey\SurveyDetailController;
use App\Http\Controllers\Survey\SurveyRecordController;
use App\Http\Controllers\Survey\AjaxController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*------------------------ AUTH CONTROLLER ------------------------*/
//trang hiển thị link cho khách khảo sát
Route::get('customer/survey/detail/{survey}/{customer}/{construction}',[SurveyDetailController::class,'createCustomer'])->name('surveyDetails.createCustomer');
Route::post('customer/survey/detail/store',[SurveyDetailController::class,'storeCustomer'])->name('surveyDetails.storeCustomer');
Route::controller(AuthController::class)->group(function(){
    Route::view('login','auth.login')->middleware('checkLoginMiddleware')->name('auth.show');
    Route::post('login','login')->name('auth.login');
    Route::match(['get','post'],'logout','logout')->name('auth.logout');
});
/* update profile user */
Route::prefix('users')->group(function(){
    //trang tạo link
    Route::get('make-link',[SurveyDetailController::class,'getlink'])->name('users.make-link');
    //form request link
    Route::post('make-link/request',[SurveyDetailController::class,'makelink'])->name('users.make-link-request');
    Route::controller(UserController::class)->group(function(){
        Route::match(['get','post'],'{user}/edit','edit')->middleware('loginMiddleware')->name('users.edit');
        Route::get('/password/change','passwordChange')->middleware('loginMiddleware')->name('users.passwordChange');
        Route::match(['get','post'],'/password/set','setPassword')->name('users.setPassword');
        Route::post('/image/upload','uploadImage');
    });
    /*------------------------ SERVEY CONTROLLER ------------------------*/
    Route::controller(SurveyDetailController::class)->middleware('accessSurveyMiddleware')->group(function(){
        Route::match(['get','post'],'survey/detail/{survey}','addSurvey')->name('surveyDetails.addSurvey');
    });
    Route::controller(SurveyRecordController::class)->middleware('accessReportSurveyMiddleware')->group(function(){
        Route::get('survey/record/list/{survey}','show')->name('surveyRecords.show');
        Route::get('survey/record/{surveyRecord}/edit','edit')->name('surveyRecords.edit');
        Route::post('survey/record/delete','destroy')->name('surveyRecords.destroy');
        Route::get('survey/records','getAllSurveyRecords')->name('surveyRecords.getAllSurveyRecords');
        Route::post('survey/detail','getSurveyDetail');
        Route::get('survey/record/{surveyRecord}/handle','handleSurveyRecord')->name('surveyRecords.handle');
        Route::post('survey/change/label','changelabelsurvey');
    });
});
//Route Ajax
Route::prefix('ajax')->controller(AjaxController::class)->group(function(){
    Route::post('statistic','getAllDataStatistic');
    Route::post('customers','getCustomerByConstructionId');
    Route::post('list/users','getAllUser');
});
//Route Admin
Route::prefix('admin')->middleware('accessAdminMiddleware')->group(function(){
    /*------------------------ ADMIN CONTROLLER ------------------------*/
    Route::controller(AdminController::class)->group(function(){
        Route::get('/','show')->name('admin.show');
        Route::get('roles/set','setRole');
        Route::get('permissions/set','setPermission');
    });
});
Route::view('error','surveys.pages.error_page')->name('error.show');