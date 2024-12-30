<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SurveyController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('backend/users', UserController::class);
Route::resource('backend/surveys', SurveyController::class);