<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthController ;
use \App\Http\Controllers\UserController ;

//Public Route
Route::post('/register',[AuthController::class , 'register'])->name('register');
Route::post('/login',[AuthController::class , 'login'])->name('login');

//Protected Route For Authenticated Users
Route::group(['middleware' => ['auth:sanctum']] , function (){
    Route::post('/logout',[AuthController::class , 'logout'])->name('logout');
    Route::get('/profile',[UserController::class , 'profile'])->name('profile');
    Route::post('/update',[UserController::class , 'update'])->name('update');
    Route::put('/change_password',[UserController::class , 'changePassword'])->name('ChangePassword');
    Route::delete('/delete',[UserController::class , 'destroy'])->name('delete');
});


