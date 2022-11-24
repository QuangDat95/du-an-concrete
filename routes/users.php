<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Concrete\UserController;

Auth::routes();
Route::group(['middleware' => 'auth'], function () {
    //  Users
    Route::match(['get', 'post'], 'users/{user?}', [UserController::class,'index'])->name('users');
    // Route::post('users/edit/{user}',[UserController::class,'edit'])->name('users.edit');
    Route::get('users/profile/{user}',[UserController::class,'profile'])->name('users.profile');
    Route::post('setting/upload/image',[UserController::class,'changeImage']);
    Route::delete('users/delete',[UserController::class,'destroy'])->name('users.delete');
    //permission
    Route::put('users/password/{user}',[UserController::class,'password'])->name('users.password');
    Route::get('role-assignment/{id}',[UserController::class,'roles']);
    Route::get('permission-assigment/{id}',[UserController::class,'permissions']);
    Route::post('insert-roles',[UserController::class,'insertRole'])->name('user.insertroles');
    Route::post('insert-permissions',[UserController::class,'insertPermission'])->name('user.insertpermissions');
});
