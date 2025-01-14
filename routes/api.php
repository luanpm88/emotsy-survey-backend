<?php

use Illuminate\Support\Facades\Route;

Route::post('/register', [App\Http\Controllers\Api\UserController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Get user information
    Route::get('/user', [App\Http\Controllers\Api\UserController::class, 'userInfo']);

    // Create user token
    Route::post('/user/create-token', [App\Http\Controllers\Api\UserController::class, 'createToken']);

    // Survey
    Route::post('/survey/{id}/copy', [App\Http\Controllers\Api\SurveyController::class, 'copy']);
    Route::post('/survey/{id}/delete', [App\Http\Controllers\Api\SurveyController::class, 'destroy']);
    Route::get('/survey/list', [App\Http\Controllers\Api\SurveyController::class, 'list']);
    Route::get('/survey/{id}', [App\Http\Controllers\Api\SurveyController::class, 'show']);
    Route::get('/survey', [App\Http\Controllers\Api\SurveyController::class, 'survey']);
    Route::post('/survey/rate', [App\Http\Controllers\Api\SurveyController::class, 'rate']);
    Route::post('/survey/create', [App\Http\Controllers\Api\SurveyController::class, 'create']);
    Route::post('/survey/{survey_id}/update', [App\Http\Controllers\Api\SurveyController::class, 'update']);

    // Device
    Route::get('/device/list', [App\Http\Controllers\Api\DeviceController::class, 'list']);
    Route::get('/device/{id}', [App\Http\Controllers\Api\DeviceController::class, 'show']);
    Route::get('/device', [App\Http\Controllers\Api\DeviceController::class, 'survey']);
    Route::post('/device/create', [App\Http\Controllers\Api\DeviceController::class, 'create']);
    Route::post('/device/{device_id}/update', [App\Http\Controllers\Api\DeviceController::class, 'update']);
});