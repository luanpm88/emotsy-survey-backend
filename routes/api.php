<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Survey;
use App\Models\UserRating;
use Illuminate\Support\Facades\Hash;

Route::post('/register', [App\Http\Controllers\Api\UserController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Get user information
    Route::get('/user', [App\Http\Controllers\Api\UserController::class, 'userInfo']);

    // Create user token
    Route::post('/user/create-token', [App\Http\Controllers\Api\UserController::class, 'createToken']);

    // Get survey
    Route::get('/survey', [App\Http\Controllers\Api\SurveyController::class, 'survey']);
    Route::post('/survey/rate', [App\Http\Controllers\Api\SurveyController::class, 'rate']);
});